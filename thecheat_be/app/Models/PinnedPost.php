<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinnedPost extends Model
{

    public $incrementing = false;
    protected $primaryKey = ['community_id', 'post_id'];

    protected $table = 'pinned_posts';

    protected $fillable = ['coummunity_id', 'post_id', 'pinned_at'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
