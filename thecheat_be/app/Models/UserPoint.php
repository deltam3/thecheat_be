<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $fillable = ['user_id', 'points', 'activity_type', 'activity_reference_id', ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
