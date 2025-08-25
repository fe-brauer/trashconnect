<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Show;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home')))
            ->add(Url::create(route('shows.index')))
            ->add(Url::create(route('candidates.index')));

        Show::select('id','slug')->chunk(500, function ($chunk) use ($sitemap) {
            foreach ($chunk as $show) {
                $sitemap->add(Url::create(route('show.detail', ['id'=>$show->id, 'slug'=>$show->slug])));
            }
        });

        Candidate::select('slug')->chunk(500, function ($chunk) use ($sitemap) {
            foreach ($chunk as $c) {
                $sitemap->add(Url::create(route('candidates.show', $c->slug)));
            }
        });

        return response($sitemap->render())
            ->header('Content-Type','application/xml');
    }
}
