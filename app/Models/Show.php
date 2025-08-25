<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Show extends Model
{
    protected $fillable = [
        'name','slug','description','genre','meta_title','meta_description', 'logo_path',
        'streaming_url',
        'network_id'
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path
            ? Storage::disk('public')->url($this->logo_path)
            : null;
    }

    public function seasons() {
        return $this->hasMany(Season::class);
    }

    public function seo() {
        return $this->morphOne(SeoData::class, 'model');
    }

    public function network()
    {
        return $this->belongsTo(\App\Models\Network::class);
    }
}
