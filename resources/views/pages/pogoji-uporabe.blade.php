<x-layouts.app title="Pogoji uporabe">
    @php
        $sections = [
            [
                'title' => '1. Splosni pogoji',
                'content' => 'S prijavo in uporabo spletne strani Priprave.net se uporabnik strinja s temi pogoji uporabe. Priprave.net si pridrzuje pravico do spremembe pogojev uporabe brez predhodnega obvestila. Spremenjeni pogoji zacnejo veljati z dnem objave na spletni strani.',
            ],
            [
                'title' => '2. Registracija in uporabniski racun',
                'content' => 'Za uporabo nekaterih funkcij strani je potrebna registracija. Uporabnik je odgovoren za tocnost podatkov, ki jih posreduje ob registraciji. Vsak uporabnik lahko ima samo en racun. Uporabnik je odgovoren za varnost svojega gesla in racuna.',
            ],
            [
                'title' => '3. Nalaganje in deljenje vsebin',
                'content' => 'Uporabnik z nalaganjem gradiv jamci, da je avtor nalozene vsebine ali da ima ustrezna dovoljenja za deljenje. Uporabnik se strinja, da bo nalozeno gradivo dostopno drugim uporabnikom strani brezplacno. Prepovedano je nalaganje vsebin, ki krsijo avtorske pravice tretjih oseb.',
            ],
            [
                'title' => '4. Avtorske pravice',
                'content' => 'Avtor nalozene priprave ohrani vse avtorske pravice nad svojo vsebino. Z nalaganjem na Priprave.net avtor podeljuje neizklucno licenco za prikaz in prenos gradiva drugim uporabnikom v izobrazevalnem namen. Uporabniki smejo prenasati gradiva izkljucno za lastno pedagosko uporabo.',
            ],
            [
                'title' => '5. Pravila obnasanja',
                'content' => 'Uporabniki morajo spoštovati druge clane skupnosti. Prepovedano je objavljanje zaljivih, neustreznih ali diskriminatornih vsebin. Prepovedano je nezeleno oglasevanje ali spam. Krsitve pravil lahko vodijo do zacasne ali trajne blokade racuna.',
            ],
            [
                'title' => '6. Odgovornost',
                'content' => 'Priprave.net ne prevzema odgovornosti za tocnost, popolnost ali kakovost nalozenih priprav. Uporabniki nalozijo in uporabljajo gradiva na lastno odgovornost. Priprave.net si pridrzuje pravico do odstranitve vsebin, ki krsijo te pogoje ali veljavno zakonodajo.',
            ],
            [
                'title' => '7. Zasebnost in varstvo podatkov',
                'content' => 'Priprave.net zbira in obdeluje osebne podatke v skladu z veljavno zakonodajo o varstvu osebnih podatkov (GDPR). Podrobnejse informacije o obdelavi podatkov so na voljo v politiki zasebnosti. Uporabnik ima pravico do vpogleda, popravka in izbrisa svojih osebnih podatkov.',
            ],
            [
                'title' => '8. Prekinitev uporabe',
                'content' => 'Uporabnik lahko kadar koli zapre svoj racun. Priprave.net si pridrzuje pravico do prekinitve ali omejitve dostopa uporabniku, ki krsi pogoje uporabe. Ob prekinitvi racuna se nalozene priprave lahko ohranijo na strani.',
            ],
            [
                'title' => '9. Koncne dolocbe',
                'content' => 'Za razmerja med uporabnikom in Priprave.net se uporablja pravo Republike Slovenije. Za resevanje morebitnih sporov je pristojno sodisce v Ljubljani. Ti pogoji uporabe veljajo od 1. januarja 2026.',
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
