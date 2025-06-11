<?php
namespace App\Jobs;

use App\Models\UserProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;

class ResizeProfileImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userProfile;
    protected $imagePath;

    public function __construct(UserProfile $userProfile, string $imagePath)
    {
        $this->userProfile = $userProfile;
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        try {
        $image = Image::make(Storage::disk('public')->path($this->imagePath));

        $image->resize(180, 180);

        $resizedImageName = pathinfo($this->imagePath, PATHINFO_FILENAME) . '-resized.jpg';

        Storage::disk('public')->put('profiles/resized/' . $resizedImageName, (string) $image->encode());

        $this->userProfile->profile_image = 'profiles/resized/' . $resizedImageName;
        $this->userProfile->save();

        } catch (\Exception $e) {
            Log::error('이미지 리사이징 오류: ' . $e->getMessage(), [
            'exception' => $e,
            'imagePath' => $this->imagePath,
            'userProfileId' => $this->userProfile->id ?? 'N/A'
        ]);
        }
    }
}
