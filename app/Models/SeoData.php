<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoData extends Model
{
    protected $fillable = [
        'title','description','keywords','schema_markup',
    ];

    protected $casts = [
        'schema_markup' => 'array',
    ];

    public function model() {
        return $this->morphTo();
    }
}
