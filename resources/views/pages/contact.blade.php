<x-layouts.app title="Kontakt">
    <section class="border-b border-border bg-card">
        <div class="mx-auto max-w-4xl px-4 py-12 text-center md:py-16">
            <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-violet-100 dark:bg-violet-900/40">
                <x-icon-regular.comment class="size-8 text-violet-600 dark:text-violet-400" />
            </div>

            <h1 class="font-serif text-3xl font-bold text-foreground md:text-4xl">
                Kontakt
            </h1>

            <p class="mt-3 text-base text-muted-foreground md:text-lg">
                Imate vprašanje, predlog ali težavo? Pišite nam.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 md:py-14">
        <div class="grid gap-8 md:grid-cols-5">
            <div class="space-y-4 md:col-span-2">
                <h2 class="font-serif text-lg font-bold text-foreground">
                    Kontaktni podatki
                </h2>

                <div class="space-y-3">
                    <a href="mailto:info@priprave.net" class="flex items-start gap-3 rounded-xl border border-border bg-card p-4 transition-colors hover:border-teal-300 hover:bg-teal-50 dark:hover:border-teal-700 dark:hover:bg-teal-900/20">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-teal-100 dark:bg-teal-900/40">
                            <x-icon-regular.envelope class="size-5 text-teal-600 dark:text-teal-400" />
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                E-pošta
                            </p>
                            <p class="mt-0.5 text-sm font-medium text-foreground">
                                info@priprave.net
                            </p>
                        </div>
                    </a>

                    <a href="https://www.facebook.com/priprave.net" target="_blank" rel="noopener noreferrer" class="flex items-start gap-3 rounded-xl border border-border bg-card p-4 transition-colors hover:border-pink-300 hover:bg-pink-50 dark:hover:border-pink-700 dark:hover:bg-pink-900/20">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-pink-100 dark:bg-pink-900/40">
                            <x-icon-brands.facebook-f class="size-5 text-pink-600 dark:text-pink-400" />
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                Facebook
                            </p>
                            <p class="mt-0.5 text-sm font-medium text-foreground truncate">
                                Priprave.net
                            </p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="md:col-span-3">
                <livewire:contact-form />
            </div>
        </div>
    </section>
</x-layouts.app>
