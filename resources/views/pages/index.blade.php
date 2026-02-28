<x-layouts.app title="Priprave za pouk">
    <x-home.hero-search :documentCount="$documentCount" />
    <x-home.category-cards :$schoolTypes />
    <livewire:latest-documents />
    <x-home.upload-cta :userCount="$userCount" />
    <x-home.stats-section
        :documentCount="$documentCount"
        :userCount="$userCount"
        :downloadCount="$downloadCount"
        :averageRating="$averageRating"
    />
</x-layouts.app>
