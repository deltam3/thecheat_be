<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable = ['post_id', 'image_url', 'image_order', 'updated_at', 'created_at'];
}
