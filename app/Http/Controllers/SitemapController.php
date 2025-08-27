<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Show;
use App\Models\StaticPage; // 👈 neu
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

        // ✅ Static Pages aufnehmen (nur veröffentlichte)
        StaticPage::query()
            ->where('published', true)
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->chunk(500, function ($chunk) use ($sitemap) {
                foreach ($chunk as $page) {
                    $sitemap->add(
                        Url::create(route('pages.show', $page->slug))
                            ->setLastModificationDate($page->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.6)
                    );
                }
            });

        Show::select('id','slug')->chunk(500, function ($chunk) use ($sitemap) {
            foreach ($chunk as $show) {
                $sitemap->add(
                    Url::create(route('show.detail', ['id'=>$show->id, 'slug'=>$show->slug]))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            }
        });

        Candidate::select('slug')->chunk(500, function ($chunk) use ($sitemap) {
            foreach ($chunk as $c) {
                $sitemap->add(
                    Url::create(route('candidates.show', $c->slug))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.5)
                );
            }
        });

        return response($sitemap->render())
            ->header('Content-Type','application/xml; charset=UTF-8');
    }
}
