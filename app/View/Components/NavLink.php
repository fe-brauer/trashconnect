<?php

namespace App\View\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class NavLink extends Component
{
    public function __construct(
        public string $href,
        public ?string $route = null,           // z.B. "shows.*" (aktiv inkl. Unterseiten)
        public bool $exact = false,             // nur exakt die Route aktiv?
        public ?string $activeClass = null,
        public ?string $inactiveClass = null,
        public ?string $baseClass = null,
    ) {}

    protected function isActive(): bool
    {
        // Prefer route name matching, wenn übergeben
        if ($this->route) {
            // exact=true -> nur exakt die Route, sonst inkl. Unterrouten
            if ($this->exact) {
                return request()->routeIs($this->route);
            }
            // falls kein Wildcard dabei ist, automatisch auf Unterrouten erweitern
            $patterns = Str::contains($this->route, '*') ? [$this->route] : [$this->route, "{$this->route}.*"];
            return request()->routeIs(...$patterns);
        }

        // Fallback: per Pfad (aus href) inkl. Unterseiten matchen
        $path = trim(parse_url($this->href, PHP_URL_PATH) ?? '/', '/');
        if ($path === '') {
            return request()->is('/') || request()->path() === '/';
        }
        return request()->is($path) || request()->is($path . '/*');
    }

    public function render(): View
    {
        $active       = $this->isActive();
        $baseClass    = $this->baseClass    ?? 'rounded-md px-3 py-2 text-base font-medium transition';
        $activeClass  = $this->activeClass  ?? 'bg-tv-violet text-white';
        $inactiveClass= $this->inactiveClass?? 'text-tv-violet hover:bg-tv-violet/30';

        return view('components.nav-link', [
            'active'       => $active,
            'computedClass'=> trim($baseClass . ' ' . ($active ? $activeClass : $inactiveClass)),
        ]);
    }
}
