<x-layouts.app
    title="Gradiva za pouk | Priprave.net"
    metaDescription="Poiščite učne priprave, delovne liste in teste za predšolsko vzgojo, osnovno in srednjo šolo na Priprave.net."
    :canonical="route('home')"
>
    <x-home.hero-search :$documentCount />
    <x-home.category-cards :$schoolTypes />

    <div class="overflow-hidden mx-auto w-[300px] h-[250px] md:h-auto md:max-w-3xl lg:max-w-5xl px-4 mb-8 text-center">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7799323007690890"
                crossorigin="anonymous"></script>
        <!-- priprave.net - prva stran -->
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-7799323007690890"
            data-ad-slot="0690119852"
            data-ad-format="auto"
            data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>

    <livewire:latest-documents />
    <x-home.upload-cta :$userCount />
    <x-home.stats-section
        :$documentCount
        :$userCount
        :$downloadCount
        :$averageRating
    />
</x-layouts.app>
