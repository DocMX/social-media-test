<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = ['user_id', 'media_path', 'media_type', 'caption', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function views()
    {
        return $this->hasMany(StoryView::class);
    }
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'story_views')
            ->withPivot('viewed_at');
    }
}
