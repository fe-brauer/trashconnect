<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Network extends Model
{
    protected $fillable = ['name','slug','url','logo_path'];

    protected $appends = ['logo_url'];

    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }
}

