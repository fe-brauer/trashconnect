<?php

use App\Http\Controllers\SitemapController;
use App\Livewire\CandidateSelector;
use App\Livewire\ConnectionFinder;
use App\Livewire\ShowBrowser;
use App\Livewire\ShowDetails;
use Illuminate\Support\Facades\Route;
use App\Livewire\StaticPageShow;
use App\Livewire\UpdateDetail;
use App\Livewire\UpdatesFeed;


Route::get('/', \App\Livewire\TabsNavigation::class)->name('home');

Route::get('/candidates', CandidateSelector::class)->name('candidates.index');
Route::get('/candidate/{slug}', \App\Livewire\CandidateProfile::class)->name('candidates.show');

Route::get('/connections/{candA}/{candB}', \App\Livewire\ConnectionPage::class)->name('connections.show');

Route::get('/shows', ShowBrowser::class)->name('shows.index');
Route::get('/show/{id}-{slug}', ShowDetails::class)->whereNumber('id')->name('show.detail');

Route::get('/season/{showSlug}/{seasonSlug}', \App\Livewire\SeasonDetails::class)->name('season.show');

Route::get('/sitemap.xml', [SitemapController::class,'index'])->name('sitemap');

Route::get('/pages/{page:slug}', StaticPageShow::class)->name('pages.show');

// Detailseite
Route::get('/updates/{slug}', UpdateDetail::class)->name('updates.detail');

// Optional: Tag-Filter-Seite (reuse Feed als Page) – wenn du magst
Route::get('/updates/tag/{slug}', function (string $slug) {
    return view('updates.tag', ['slug' => $slug]); // simple wrapper
})->name('updates.tag');
