<x-layouts.app>
    <x-seo
        title="Alle Reality-TV-Shows im Überblick – TrashConnect"
        description="Alphabetische Übersicht aller Reality-TV-Shows: mit Staffeln, vollständigen Besetzungen, Logos und (falls verfügbar) Streaming-Links."
        :canonical="route('shows.index')"
        ogTitle="Alle Reality-TV-Shows"
        ogDescription="Shows mit Staffeln, Besetzungen, Logos & Streaming-Links – alphabetisch gelistet."
        ogType="website"
    />

    <livewire:show-browser />
</x-layouts.app>
