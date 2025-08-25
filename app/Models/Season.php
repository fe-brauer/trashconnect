<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{
    protected $fillable = [
        'show_id','name','slug','year','episode_count','meta_title','meta_description',
    ];

    public function show() {
        return $this->belongsTo(Show::class);
    }

    public function participants() {
        return $this->hasMany(Participant::class);
    }

    public function candidates() {
        return $this->belongsToMany(Candidate::class, Participant::class);
    }

    public function seo() {
        return $this->morphOne(SeoData::class, 'model');
    }
}
