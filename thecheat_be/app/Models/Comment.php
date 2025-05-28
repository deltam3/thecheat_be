<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_comment_id',
        'content',
        'edited_at',
        'username',
        'profile_image',
        'updated_at',
    ];

    public function commentImages()
    {
        return $this->hasMany(CommentImage::class);
    }
}
