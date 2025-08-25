<x-layouts.app>
<x-seo :title="'Updates – #'.$slug.' – TrashConnect'" :description="'Alle Updates mit #'.$slug" />
<div class="space-y-6">
    <h1 class="text-2xl font-bold">Updates zu #{{ $slug }}</h1>
    <livewire:updates-feed :tag="$slug" :limit="20" />
</div>
</x-layouts.app>
