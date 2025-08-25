<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StaticPage extends Model
{
    protected $fillable = [
        'title','slug','content',
        'show_in_nav','nav_order','published',
        'meta_title','meta_description',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        $forget = fn () => \Illuminate\Support\Facades\Cache::forget('main_nav_pages');

        static::saved(function (StaticPage $page) use ($forget) {
            $forget();
            \Illuminate\Support\Facades\Cache::forget("page:{$page->getOriginal('slug')}");
            \Illuminate\Support\Facades\Cache::forget("page:{$page->slug}");
        });

        static::deleted(function (StaticPage $page) use ($forget) {
            $forget();
            \Illuminate\Support\Facades\Cache::forget("page:{$page->slug}");
        });
    }
}
