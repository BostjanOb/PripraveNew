<x-layouts.app title="Pomoč">
    <section class="border-b border-border bg-card">
        <div class="mx-auto max-w-4xl px-4 py-12 text-center md:py-16">
            <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-teal-100">
                <x-icon-regular.circle-question class="size-8 text-teal-600" />
            </div>

            <h1 class="font-serif text-3xl font-bold text-foreground md:text-4xl">
                Pomoč
            </h1>

            <p class="mt-3 text-base text-muted-foreground md:text-lg">
                Pogosta vprašanja in odgovori o uporabi Priprave.net
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 md:py-14">
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($faqs as $faq)
                <article class="rounded-2xl border border-border bg-card p-6 transition-all hover:shadow-md">
                    <div class="mb-3 flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $faq->iconBackgroundClass() }}">
                            <x-dynamic-component :component="'icon-regular.' . $faq->icon" class="size-5 {{ $faq->iconForegroundClass() }}" />
                        </div>

                        <h2 class="font-serif text-base font-bold text-foreground">
                            {{ $faq->question }}
                        </h2>
                    </div>

                    <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ $faq->answer }}
                    </p>
                </article>
            @endforeach
        </div>

        <div class="mt-12 rounded-2xl border-2 border-teal-200 bg-teal-50 p-8 text-center shadow-sm dark:border-teal-400/35 dark:bg-slate-900">
            <h2 class="font-serif text-xl font-bold text-foreground">
                Niste našli odgovora?
            </h2>

            <p class="mt-2 text-sm text-muted-foreground dark:text-slate-300">
                Kontaktirajte nas in z veseljem vam bomo pomagali.
            </p>

            <a
                href="{{ route('contact') }}"
                class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-teal-500 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-teal-600 dark:bg-teal-500 dark:hover:bg-teal-400"
            >
                <x-icon-regular.envelope class="size-4" />
                Kontaktirajte nas
                <x-icon-regular.arrow-right class="size-4" />
            </a>
        </div>
    </section>
</x-layouts.app>
