<?php
namespace App\Jobs;

use App\Models\UserProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use Intervention\Image\Facades\Image;
// use Storage;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ResizeProfileImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userProfile;
    protected $imagePath;

    /**
     * Create a new job instance.
     *
     * @param UserProfile $userProfile
     * @param string $imagePath
     */
    public function __construct(UserProfile $userProfile, string $imagePath)
    {
        $this->userProfile = $userProfile;
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $image = Storage::disk('public')->get($this->imagePath);

        $image = Image::read($image)->resize(180, 180);

        $resizedImageName = basename($this->imagePath, '.' . pathinfo($this->imagePath, PATHINFO_EXTENSION)) . '-resized.jpg';

        Storage::disk('public')->put('profiles/resized/' . $resizedImageName, (string) $image->encode());

        $this->userProfile->profile_image = 'profiles/resized/' . $resizedImageName;
        $this->userProfile->save();
    }
}
