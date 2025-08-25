<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Update extends Model
{
    protected $fillable = [
        'title','slug','excerpt','content','cover_path',
        'is_pinned','is_public','published_at','kind','status',
        'meta_title','meta_description', 'content_blocks'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_public' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['cover_url'];

    public function shows() { return $this->belongsToMany(Show::class); }
    public function tags()  { return $this->belongsToMany(Tag::class); }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }

    // Nur öffentlich & veröffentlicht (<= now)
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_public', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}

