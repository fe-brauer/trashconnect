<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    protected $fillable = [
        'name','slug','bio','instagram_url','birth_date','meta_title','meta_description',
    ];

    protected $casts = [
        'birth_date'   => 'date'
    ];

    public function participants() {
        return $this->hasMany(Participant::class);
    }

    public function seasons() {
        return $this->belongsToMany(Season::class, Participant::class);
    }

    public function seo() {
        return $this->morphOne(SeoData::class, 'model');
    }

    public function scopeSlug($q, string $slug) {
        return $q->where('slug', $slug);
    }
}
