<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Kako najdem učno pripravo?',
                'answer' => 'Uporabite iskalno vrstico na vrhu strani ali obiščite stran Brskanje. Priprave lahko filtrirate po stopnji izobraževanja, razredu, predmetu in tipu gradiva.',
                'icon' => 'magnifying-glass',
                'icon_background_color' => '#CCFBF1',
                'sort_order' => 1,
            ],
            [
                'question' => 'Kako naložim svojo pripravo?',
                'answer' => 'Prijavite se v svoj račun in kliknite gumb "Dodaj pripravo" v zgornjem meniju. Izpolnite obrazec z naslovom, opisom, stopnjo, razredom in predmetom ter naložite datoteke.',
                'icon' => 'upload',
                'icon_background_color' => '#FEF3C7',
                'sort_order' => 2,
            ],
            [
                'question' => 'Ali je prenos priprav brezplačen?',
                'answer' => 'Da, vse priprave na Priprave.net so brezplačne za prenos. Stran deluje na principu deljenja med učitelji.',
                'icon' => 'download',
                'icon_background_color' => '#D1FAE5',
                'sort_order' => 3,
            ],
            [
                'question' => 'Kako se registriram?',
                'answer' => 'Kliknite gumb Prijava v zgornjem meniju, nato izberite Registracija. Lahko se registrirate z e-poštnim naslovom ali preko Google/Facebook računa.',
                'icon' => 'user',
                'icon_background_color' => '#E0F2FE',
                'sort_order' => 4,
            ],
            [
                'question' => 'Kako ocenim pripravo?',
                'answer' => 'Na strani posamezne priprave najdete zvezdice za ocenjevanje. Kliknite na zeleno število zvezdic (1-5), da oddate svojo oceno.',
                'icon' => 'star',
                'icon_background_color' => '#FEF3C7',
                'sort_order' => 5,
            ],
            [
                'question' => 'Kako komentiram pripravo?',
                'answer' => 'Pod vsako pripravo je razdelek za komentarje. Vpišite svoj komentar in kliknite Komentiraj. Za komentiranje morate biti prijavljeni.',
                'icon' => 'comment-alt-captions',
                'icon_background_color' => '#EDE9FE',
                'sort_order' => 6,
            ],
            [
                'question' => 'Kaj storim, če najdem neustrezno vsebino?',
                'answer' => 'Na strani priprave kliknite gumb "Prijavi neustrezno". Naša ekipa bo pregledala prijavo in ustrezno ukrepala.',
                'icon' => 'shield-check',
                'icon_background_color' => '#FFE4E6',
                'sort_order' => 7,
            ],
            [
                'question' => 'Katere formate datotek lahko naložim?',
                'answer' => 'Podpiramo formate DOC, DOCX, PDF, PPT, PPTX, XLS, XLSX ter slikovne formate JPG, PNG. Največja velikost posamezne datoteke je 20 MB.',
                'icon' => 'book-open',
                'icon_background_color' => '#FCE7F3',
                'sort_order' => 8,
            ],
        ];

        Faq::query()->upsert(
            $faqs,
            ['sort_order'],
            ['question', 'answer', 'icon', 'icon_background_color']
        );
    }
}
