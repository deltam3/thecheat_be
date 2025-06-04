<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentImage extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'comment_id',
        'image_url',
        'image_order',
        'updated_at',
        'created_at'
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
