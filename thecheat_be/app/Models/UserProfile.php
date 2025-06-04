<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false; 
    public $timestamps = true; 

    protected $fillable = [
        'user_id',
        'profile_image',
        'intro_text',
        'is_deleted',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
