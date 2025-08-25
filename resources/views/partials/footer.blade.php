<footer class="py-12 text-sm text-slate-500">
    © {{ date('Y') }} TrashConnect |
    <a href="{{ route('pages.show', 'impressum') }}" class="tc-link" wire:navigate>Impressum</a> |
    <a href="{{ route('pages.show', 'datenschutz') }}" class="tc-link" wire:navigate>Datenschutz</a>
</footer>
