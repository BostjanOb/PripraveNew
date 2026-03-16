<x-layouts.app
    title="Pogoji uporabe | Priprave.net"
    metaDescription="Preberite pogoje uporabe spletne strani Priprave.net, pravila nalaganja gradiv in osnovne obveznosti uporabnikov."
    :canonical="route('terms')"
>
    @php
        $sections = [
            [
                'title' => '1. Splošni pogoji',
                'content' => 'S prijavo in uporabo spletne strani Priprave.net se uporabnik strinja s temi pogoji uporabe. Priprave.net si pridržuje pravico do spremembe pogojev uporabe brez predhodnega obvestila. Spremenjeni pogoji začnejo veljati z dnem objave na spletni strani.',
            ],
            [
                'title' => '2. Registracija in uporabniški račun',
                'content' => 'Za uporabo nekaterih funkcij strani je potrebna registracija. Uporabnik je odgovoren za točnost podatkov, ki jih posreduje ob registraciji. Vsak uporabnik lahko ima samo en račun. Uporabnik je odgovoren za varnost svojega gesla in računa.',
            ],
            [
                'title' => '3. Nalaganje in deljenje vsebin',
                'content' => 'Uporabnik z nalaganjem gradiv jamči, da je avtor naložene vsebine ali da ima ustrezna dovoljenja za deljenje. Uporabnik se strinja, da bo naloženo gradivo dostopno drugim uporabnikom strani brezplačno. Prepovedano je nalaganje vsebin, ki kršijo avtorske pravice tretjih oseb.',
            ],
            [
                'title' => '4. Avtorske pravice',
                'content' => 'Avtor naložene priprave ohrani vse avtorske pravice nad svojo vsebino. Z nalaganjem na Priprave.net avtor podeljuje neizključno licenco za prikaz in prenos gradiva drugim uporabnikom v izobraževalnem namenu. Uporabniki smejo prenašati gradiva izključno za lastno pedagoško uporabo.',
            ],
            [
                'title' => '5. Pravila obnašanja',
                'content' => 'Uporabniki morajo spoštovati druge člane skupnosti. Prepovedano je objavljanje žaljivih, neustreznih ali diskriminatornih vsebin. Prepovedano je neželeno oglaševanje ali spam. Kršitve pravil lahko vodijo do začasne ali trajne blokade računa.',
            ],
            [
                'title' => '6. Odgovornost',
                'content' => 'Priprave.net ne prevzema odgovornosti za točnost, popolnost ali kakovost naloženih priprav. Uporabniki naložijo in uporabljajo gradiva na lastno odgovornost. Priprave.net si pridržuje pravico do odstranitve vsebin, ki kršijo te pogoje ali veljavno zakonodajo.',
            ],
            [
                'title' => '7. Zasebnost in varstvo podatkov',
                'content' => 'Priprave.net zbira in obdeluje osebne podatke v skladu z veljavno zakonodajo o varstvu osebnih podatkov (GDPR). Podrobnejše informacije o obdelavi podatkov so na voljo v politiki zasebnosti. Uporabnik ima pravico do vpogleda, popravka in izbrisa svojih osebnih podatkov.',
            ],
            [
                'title' => '8. Prekinitev uporabe',
                'content' => 'Uporabnik lahko kadar koli zapre svoj račun. Priprave.net si pridržuje pravico do prekinitve ali omejitve dostopa uporabniku, ki krši pogoje uporabe. Ob prekinitvi računa se naložene priprave lahko ohranijo na strani.',
            ],
        ];
    @endphp

    <section class="border-b border-border bg-card">
        <div class="mx-auto max-w-4xl px-4 py-12 text-center md:py-16">
            <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-amber-100">
                <x-icon-regular.file-lines class="size-8 text-amber-600" />
            </div>

            <h1 class="font-serif text-3xl font-bold text-foreground md:text-4xl">
                Pogoji uporabe
            </h1>

            <p class="mt-3 text-base text-muted-foreground md:text-lg">
                Pravila in pogoji za uporabo spletne strani Priprave.net
            </p>

            <p class="mt-2 text-xs text-muted-foreground">
                Zadnja posodobitev: 1. januar 2026
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-10 md:py-14">
        <div class="space-y-8">
            @foreach ($sections as $section)
                <article>
                    <h2 class="font-serif text-lg font-bold text-foreground">
                        {{ $section['title'] }}
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-muted-foreground">
                        {{ $section['content'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>
