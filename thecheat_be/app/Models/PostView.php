<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'post_id', 'viewed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
