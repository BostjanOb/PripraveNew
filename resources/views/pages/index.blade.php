<x-layouts.app
    title="Gradiva za pouk | Priprave.net"
    metaDescription="Poiščite učne priprave, delovne liste in teste za predšolsko vzgojo, osnovno in srednjo šolo na Priprave.net."
    :canonical="route('home')"
>
    <x-home.hero-search :$documentCount />
    <x-home.category-cards :$schoolTypes />

    <section>
        <style>
            .adprva { width: 300px; height: 250px; }
            @media(min-width: 500px) { .adprva { width: 468px; height: 60px; } }
            @media(min-width: 800px) { .adprva { width: 728px; height: 90px; } }
            @media(min-width: 1024px) { .adprva { width: 970px; height: 250px; } }
        </style>
        <div class="adprva mx-auto">
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7799323007690890"
                    crossorigin="anonymous"></script>
            <!-- priprave.net - prva stran -->
            <ins class="adsbygoogle adprva"
                style="display:block"
                data-ad-client="ca-pub-7799323007690890"
                data-ad-slot="0690119852"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </section>

    <livewire:latest-documents />
    <x-home.upload-cta :$userCount />
    <x-home.stats-section
        :$documentCount
        :$userCount
        :$downloadCount
        :$averageRating
    />
</x-layouts.app>
