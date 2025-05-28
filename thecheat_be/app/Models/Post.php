<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'community_id',
        'title',
        'content',
        'images',
        'view_count',
        'is_flagged',
        'profile_image',
        'thumbnail_image',
        'police_station_name',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class, 'post_id');
    }


    public function postImages()
    {
        return $this->hasMany(PostImage::class, 'post_id');
    }

    public function reports()
    {
        return $this->hasMany(PostReport::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }
}
