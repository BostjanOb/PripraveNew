<?php

namespace App\Support;

class BadgeRegistry
{
    /** @var array<int, array{id: string, name: string, description: string, category: string, requirement: string, color: array{bg: string, border: string, text: string, iconBg: string}}> */
    private static array $badges = [
        // ── Contribution (uploads) ──
        [
            'id' => 'prvi-korak',
            'name' => 'Prvi korak',
            'description' => 'Naloži svojo prvo pripravo',
            'category' => 'contribution',
            'requirement' => '1 naložena priprava',
            'color' => [
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/50',
                'border' => 'border-emerald-200 dark:border-emerald-800',
                'text' => 'text-emerald-700 dark:text-emerald-300',
                'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/50',
            ],
        ],
        [
            'id' => 'prispevkar',
            'name' => 'Prispevkar',
            'description' => 'Redno deliš svoje priprave s kolegi',
            'category' => 'contribution',
            'requirement' => '5 naloženih priprav',
            'color' => [
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/50',
                'border' => 'border-emerald-200 dark:border-emerald-800',
                'text' => 'text-emerald-700 dark:text-emerald-300',
                'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/50',
            ],
        ],
        [
            'id' => 'aktivni-avtor',
            'name' => 'Aktivni avtor',
            'description' => 'Pogost in zanesljiv prispevek k skupnosti',
            'category' => 'contribution',
            'requirement' => '15 naloženih priprav',
            'color' => [
                'bg' => 'bg-teal-50 dark:bg-teal-950/50',
                'border' => 'border-teal-200 dark:border-teal-800',
                'text' => 'text-teal-700 dark:text-teal-300',
                'iconBg' => 'bg-teal-100 dark:bg-teal-900/50',
            ],
        ],
        [
            'id' => 'zvezda-skupnosti',
            'name' => 'Zvezda skupnosti',
            'description' => 'Eden izmed najbolj aktivnih članov skupnosti',
            'category' => 'contribution',
            'requirement' => '50 naloženih priprav',
            'color' => [
                'bg' => 'bg-amber-50 dark:bg-amber-950/50',
                'border' => 'border-amber-200 dark:border-amber-800',
                'text' => 'text-amber-700 dark:text-amber-300',
                'iconBg' => 'bg-amber-100 dark:bg-amber-900/50',
            ],
        ],
        [
            'id' => 'mojster-priprav',
            'name' => 'Mojster priprav',
            'description' => 'Legendarni prispevek k skupnosti učiteljev',
            'category' => 'contribution',
            'requirement' => '100+ naloženih priprav',
            'color' => [
                'bg' => 'bg-amber-50 dark:bg-amber-950/50',
                'border' => 'border-amber-300 dark:border-amber-700',
                'text' => 'text-amber-800 dark:text-amber-200',
                'iconBg' => 'bg-gradient-to-br from-amber-200 to-orange-200 dark:from-amber-900/70 dark:to-orange-900/70',
            ],
        ],

        // ── Downloads (by user) ──
        [
            'id' => 'raziskovalec',
            'name' => 'Raziskovalec',
            'description' => 'Rad raziskuješ in prenašaš priprave',
            'category' => 'downloads',
            'requirement' => '10 prenesenih priprav',
            'color' => [
                'bg' => 'bg-sky-50 dark:bg-sky-950/50',
                'border' => 'border-sky-200 dark:border-sky-800',
                'text' => 'text-sky-700 dark:text-sky-300',
                'iconBg' => 'bg-sky-100 dark:bg-sky-900/50',
            ],
        ],
        [
            'id' => 'zbiratelj',
            'name' => 'Zbiratelj',
            'description' => 'Zbiralec odličnih učnih gradiv',
            'category' => 'downloads',
            'requirement' => '50 prenesenih priprav',
            'color' => [
                'bg' => 'bg-indigo-50 dark:bg-indigo-950/50',
                'border' => 'border-indigo-200 dark:border-indigo-800',
                'text' => 'text-indigo-700 dark:text-indigo-300',
                'iconBg' => 'bg-indigo-100 dark:bg-indigo-900/50',
            ],
        ],
        [
            'id' => 'modra-sovica',
            'name' => 'Modra sovica',
            'description' => 'Pravi poznavalec učnih gradiv',
            'category' => 'downloads',
            'requirement' => '200+ prenesenih priprav',
            'color' => [
                'bg' => 'bg-violet-50 dark:bg-violet-950/50',
                'border' => 'border-violet-200 dark:border-violet-800',
                'text' => 'text-violet-700 dark:text-violet-300',
                'iconBg' => 'bg-violet-100 dark:bg-violet-900/50',
            ],
        ],

        // ── Loyalty (membership age) ──
        [
            'id' => 'novinec',
            'name' => 'Novinec',
            'description' => 'Dobrodošli v skupnosti!',
            'category' => 'loyalty',
            'requirement' => 'Registracija',
            'color' => [
                'bg' => 'bg-lime-50 dark:bg-lime-950/50',
                'border' => 'border-lime-200 dark:border-lime-800',
                'text' => 'text-lime-700 dark:text-lime-300',
                'iconBg' => 'bg-lime-100 dark:bg-lime-900/50',
            ],
        ],
        [
            'id' => '1-leto',
            'name' => '1 leto z nami',
            'description' => 'Že eno leto si del naše skupnosti',
            'category' => 'loyalty',
            'requirement' => '1 leto članstva',
            'color' => [
                'bg' => 'bg-pink-50 dark:bg-pink-950/50',
                'border' => 'border-pink-200 dark:border-pink-800',
                'text' => 'text-pink-700 dark:text-pink-300',
                'iconBg' => 'bg-pink-100 dark:bg-pink-900/50',
            ],
        ],
        [
            'id' => '2-leti',
            'name' => '2 leti z nami',
            'description' => 'Zvest član skupnosti že dve leti',
            'category' => 'loyalty',
            'requirement' => '2 leti članstva',
            'color' => [
                'bg' => 'bg-pink-50 dark:bg-pink-950/50',
                'border' => 'border-pink-200 dark:border-pink-800',
                'text' => 'text-pink-700 dark:text-pink-300',
                'iconBg' => 'bg-pink-100 dark:bg-pink-900/50',
            ],
        ],
        [
            'id' => 'veteran',
            'name' => 'Veteran',
            'description' => 'Dolgoletni steber skupnosti',
            'category' => 'loyalty',
            'requirement' => '5+ let članstva',
            'color' => [
                'bg' => 'bg-purple-50 dark:bg-purple-950/50',
                'border' => 'border-purple-200 dark:border-purple-800',
                'text' => 'text-purple-700 dark:text-purple-300',
                'iconBg' => 'bg-purple-100 dark:bg-purple-900/50',
            ],
        ],

        // ── Special ──
        [
            'id' => 'vsestranski',
            'name' => 'Vsestranski',
            'description' => 'Priprave iz različnih predmetnih področij',
            'category' => 'special',
            'requirement' => 'Priprave v 3+ različnih predmetih',
            'color' => [
                'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/50',
                'border' => 'border-fuchsia-200 dark:border-fuchsia-800',
                'text' => 'text-fuchsia-700 dark:text-fuchsia-300',
                'iconBg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50',
            ],
        ],
        [
            'id' => 'pomocnik',
            'name' => 'Pomočnik',
            'description' => 'Aktiven komentator in pomočnik kolegom',
            'category' => 'special',
            'requirement' => '10+ komentarjev',
            'color' => [
                'bg' => 'bg-rose-50 dark:bg-rose-950/50',
                'border' => 'border-rose-200 dark:border-rose-800',
                'text' => 'text-rose-700 dark:text-rose-300',
                'iconBg' => 'bg-rose-100 dark:bg-rose-900/50',
            ],
        ],
        [
            'id' => 'navdih',
            'name' => 'Navdih',
            'description' => 'Tvoja priprava je navdihnila mnogo učiteljev',
            'category' => 'special',
            'requirement' => 'Priprava s 100+ prenosi',
            'color' => [
                'bg' => 'bg-orange-50 dark:bg-orange-950/50',
                'border' => 'border-orange-200 dark:border-orange-800',
                'text' => 'text-orange-700 dark:text-orange-300',
                'iconBg' => 'bg-orange-100 dark:bg-orange-900/50',
            ],
        ],
        [
            'id' => 'pionir',
            'name' => 'Pionir',
            'description' => 'Prvi, ki je naložil pripravo za določen predmet in razred',
            'category' => 'special',
            'requirement' => 'Prva priprava v kombinaciji predmet+razred',
            'color' => [
                'bg' => 'bg-cyan-50 dark:bg-cyan-950/50',
                'border' => 'border-cyan-200 dark:border-cyan-800',
                'text' => 'text-cyan-700 dark:text-cyan-300',
                'iconBg' => 'bg-cyan-100 dark:bg-cyan-900/50',
            ],
        ],
    ];

    /** @return array<int, array{id: string, name: string, description: string, category: string, requirement: string, color: array{bg: string, border: string, text: string, iconBg: string}}> */
    public static function all(): array
    {
        return self::$badges;
    }

    /** @return array{id: string, name: string, description: string, category: string, requirement: string, color: array{bg: string, border: string, text: string, iconBg: string}}|null */
    public static function find(string $id): ?array
    {
        foreach (self::$badges as $badge) {
            if ($badge['id'] === $id) {
                return $badge;
            }
        }

        return null;
    }

    /**
     * @return array<int, array{id: string, name: string, description: string, category: string, requirement: string, color: array{bg: string, border: string, text: string, iconBg: string}}>
     */
    public static function forCategory(string $category): array
    {
        return array_values(array_filter(self::$badges, fn ($b) => $b['category'] === $category));
    }

    /** @return string[] */
    public static function categories(): array
    {
        return ['contribution', 'downloads', 'loyalty', 'special'];
    }

    /** @return array<string, string> */
    public static function categoryLabels(): array
    {
        return [
            'contribution' => 'Prispevki',
            'downloads' => 'Prenosi',
            'loyalty' => 'Zvestoba',
            'special' => 'Posebne',
        ];
    }
}
