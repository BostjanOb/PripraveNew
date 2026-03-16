<x-layouts.app
    title="Gradiva za pouk | Priprave.net"
    metaDescription="Poiščite učne priprave, delovne liste in teste za predšolsko vzgojo, osnovno in srednjo šolo na Priprave.net."
    :canonical="route('home')"
>
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
