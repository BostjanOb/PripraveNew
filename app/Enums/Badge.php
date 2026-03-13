<?php

namespace App\Enums;

enum Badge: string
{
    // Contribution
    case PrviKorak = 'prvi-korak';
    case Prispevkar = 'prispevkar';
    case AktivniAvtor = 'aktivni-avtor';
    case ZvezdaSkupnosti = 'zvezda-skupnosti';
    case MojsterPriprav = 'mojster-priprav';

    // Downloads
    case Raziskovalec = 'raziskovalec';
    case Zbiratelj = 'zbiratelj';
    case ModraSovica = 'modra-sovica';

    // Loyalty
    case Novinec = 'novinec';
    case TriLeta = '3-leta';
    case PetLet = '5-let';
    case DesetLet = '10-let';

    // Special
    case Vsestranski = 'vsestranski';
    case Pomocnik = 'pomocnik';
    case Navdih = 'navdih';
    case Pionir = 'pionir';

    public function label(): string
    {
        return match ($this) {
            self::PrviKorak => 'Prvi korak',
            self::Prispevkar => 'Prispevkar',
            self::AktivniAvtor => 'Aktivni avtor',
            self::ZvezdaSkupnosti => 'Zvezda skupnosti',
            self::MojsterPriprav => 'Mojster priprav',
            self::Raziskovalec => 'Raziskovalec',
            self::Zbiratelj => 'Zbiratelj',
            self::ModraSovica => 'Modra sovica',
            self::Novinec => 'Novinec',
            self::TriLeta => '3 leta z nami',
            self::PetLet => '5 let z nami',
            self::DesetLet => '10 let z nami',
            self::Vsestranski => 'Vsestranski',
            self::Pomocnik => 'Pomočnik',
            self::Navdih => 'Navdih',
            self::Pionir => 'Pionir',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PrviKorak => 'Naloži svojo prvo pripravo',
            self::Prispevkar => 'Redno deliš svoje priprave s kolegi',
            self::AktivniAvtor => 'Pogost in zanesljiv prispevek k skupnosti',
            self::ZvezdaSkupnosti => 'Eden izmed najbolj aktivnih članov skupnosti',
            self::MojsterPriprav => 'Legendarni prispevek k skupnosti učiteljev',
            self::Raziskovalec => 'Rad raziskuješ in prenašaš priprave',
            self::Zbiratelj => 'Zbiralec odličnih učnih gradiv',
            self::ModraSovica => 'Pravi poznavalec učnih gradiv',
            self::Novinec => 'Dobrodošli v skupnosti!',
            self::TriLeta => 'Že tri leta si del naše skupnosti',
            self::PetLet => 'Zvest član skupnosti že pet let',
            self::DesetLet => 'Dolgoletni steber skupnosti',
            self::Vsestranski => 'Priprave iz različnih predmetnih področij',
            self::Pomocnik => 'Aktiven komentator in pomočnik kolegom',
            self::Navdih => 'Tvoja priprava je navdihnila mnogo učiteljev',
            self::Pionir => 'Prvi, ki je naložil pripravo za določen predmet in razred',
        };
    }

    public function requirement(): string
    {
        return match ($this) {
            self::PrviKorak => '1 naložena priprava',
            self::Prispevkar => '5 naloženih priprav',
            self::AktivniAvtor => '15 naloženih priprav',
            self::ZvezdaSkupnosti => '50 naloženih priprav',
            self::MojsterPriprav => '100+ naloženih priprav',
            self::Raziskovalec => '10 prenesenih priprav',
            self::Zbiratelj => '50 prenesenih priprav',
            self::ModraSovica => '200+ prenesenih priprav',
            self::Novinec => 'Registracija',
            self::TriLeta => '3 leta članstva',
            self::PetLet => '5 let članstva',
            self::DesetLet => '10 let članstva',
            self::Vsestranski => 'Priprave v 3+ različnih predmetih',
            self::Pomocnik => '10+ komentarjev',
            self::Navdih => 'Priprava s 100+ prenosi',
            self::Pionir => 'Prva priprava v kombinaciji predmet+razred',
        };
    }

    public function category(): BadgeCategory
    {
        return match ($this) {
            self::PrviKorak, self::Prispevkar, self::AktivniAvtor, self::ZvezdaSkupnosti, self::MojsterPriprav => BadgeCategory::Contribution,
            self::Raziskovalec, self::Zbiratelj, self::ModraSovica => BadgeCategory::Downloads,
            self::Novinec, self::TriLeta, self::PetLet, self::DesetLet => BadgeCategory::Loyalty,
            self::Vsestranski, self::Pomocnik, self::Navdih, self::Pionir => BadgeCategory::Special,
        };
    }

    /**
     * FontAwesome icon name (to be added to resources/svg).
     */
    public function icon(): string
    {
        return match ($this) {
            self::PrviKorak => 'icon-regular.shoe-prints',
            self::Prispevkar => 'icon-regular.hand-holding-heart',
            self::AktivniAvtor => 'icon-regular.pen-nib',
            self::ZvezdaSkupnosti => 'icon-regular.star',
            self::MojsterPriprav => 'icon-regular.crown',
            self::Raziskovalec => 'icon-regular.magnifying-glass',
            self::Zbiratelj => 'icon-regular.box-archive',
            self::ModraSovica => 'icon-regular.owl',
            self::Novinec => 'icon-regular.seedling',
            self::TriLeta => 'icon-regular.cake-candles',
            self::PetLet => 'icon-regular.award',
            self::DesetLet => 'icon-regular.medal',
            self::Vsestranski => 'icon-regular.palette',
            self::Pomocnik => 'icon-regular.comments',
            self::Navdih => 'icon-regular.fire',
            self::Pionir => 'icon-regular.flag',
        };
    }

    /**
     * @return array{bg: string, border: string, text: string, iconBg: string}
     */
    public function color(): array
    {
        return match ($this) {
            self::PrviKorak, self::Prispevkar => [
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/50',
                'border' => 'border-emerald-200 dark:border-emerald-800',
                'text' => 'text-emerald-700 dark:text-emerald-300',
                'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/50',
            ],
            self::AktivniAvtor => [
                'bg' => 'bg-teal-50 dark:bg-teal-950/50',
                'border' => 'border-teal-200 dark:border-teal-800',
                'text' => 'text-teal-700 dark:text-teal-300',
                'iconBg' => 'bg-teal-100 dark:bg-teal-900/50',
            ],
            self::ZvezdaSkupnosti => [
                'bg' => 'bg-amber-50 dark:bg-amber-950/50',
                'border' => 'border-amber-200 dark:border-amber-800',
                'text' => 'text-amber-700 dark:text-amber-300',
                'iconBg' => 'bg-amber-100 dark:bg-amber-900/50',
            ],
            self::MojsterPriprav => [
                'bg' => 'bg-amber-50 dark:bg-amber-950/50',
                'border' => 'border-amber-300 dark:border-amber-700',
                'text' => 'text-amber-800 dark:text-amber-200',
                'iconBg' => 'bg-gradient-to-br from-amber-200 to-orange-200 dark:from-amber-900/70 dark:to-orange-900/70',
            ],
            self::Raziskovalec => [
                'bg' => 'bg-sky-50 dark:bg-sky-950/50',
                'border' => 'border-sky-200 dark:border-sky-800',
                'text' => 'text-sky-700 dark:text-sky-300',
                'iconBg' => 'bg-sky-100 dark:bg-sky-900/50',
            ],
            self::Zbiratelj => [
                'bg' => 'bg-indigo-50 dark:bg-indigo-950/50',
                'border' => 'border-indigo-200 dark:border-indigo-800',
                'text' => 'text-indigo-700 dark:text-indigo-300',
                'iconBg' => 'bg-indigo-100 dark:bg-indigo-900/50',
            ],
            self::ModraSovica => [
                'bg' => 'bg-violet-50 dark:bg-violet-950/50',
                'border' => 'border-violet-200 dark:border-violet-800',
                'text' => 'text-violet-700 dark:text-violet-300',
                'iconBg' => 'bg-violet-100 dark:bg-violet-900/50',
            ],
            self::Novinec => [
                'bg' => 'bg-lime-50 dark:bg-lime-950/50',
                'border' => 'border-lime-200 dark:border-lime-800',
                'text' => 'text-lime-700 dark:text-lime-300',
                'iconBg' => 'bg-lime-100 dark:bg-lime-900/50',
            ],
            self::TriLeta, self::PetLet => [
                'bg' => 'bg-pink-50 dark:bg-pink-950/50',
                'border' => 'border-pink-200 dark:border-pink-800',
                'text' => 'text-pink-700 dark:text-pink-300',
                'iconBg' => 'bg-pink-100 dark:bg-pink-900/50',
            ],
            self::DesetLet => [
                'bg' => 'bg-purple-50 dark:bg-purple-950/50',
                'border' => 'border-purple-200 dark:border-purple-800',
                'text' => 'text-purple-700 dark:text-purple-300',
                'iconBg' => 'bg-purple-100 dark:bg-purple-900/50',
            ],
            self::Vsestranski => [
                'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/50',
                'border' => 'border-fuchsia-200 dark:border-fuchsia-800',
                'text' => 'text-fuchsia-700 dark:text-fuchsia-300',
                'iconBg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50',
            ],
            self::Pomocnik => [
                'bg' => 'bg-rose-50 dark:bg-rose-950/50',
                'border' => 'border-rose-200 dark:border-rose-800',
                'text' => 'text-rose-700 dark:text-rose-300',
                'iconBg' => 'bg-rose-100 dark:bg-rose-900/50',
            ],
            self::Navdih => [
                'bg' => 'bg-orange-50 dark:bg-orange-950/50',
                'border' => 'border-orange-200 dark:border-orange-800',
                'text' => 'text-orange-700 dark:text-orange-300',
                'iconBg' => 'bg-orange-100 dark:bg-orange-900/50',
            ],
            self::Pionir => [
                'bg' => 'bg-cyan-50 dark:bg-cyan-950/50',
                'border' => 'border-cyan-200 dark:border-cyan-800',
                'text' => 'text-cyan-700 dark:text-cyan-300',
                'iconBg' => 'bg-cyan-100 dark:bg-cyan-900/50',
            ],
        };
    }

    /**
     * @return Badge[]
     */
    public static function forCategory(BadgeCategory $category): array
    {
        return array_values(array_filter(self::cases(), fn (self $badge) => $badge->category() === $category));
    }

    /**
     * Contribution milestones: badge => threshold upload count.
     *
     * @return array<int, array{badge: Badge, threshold: int}>
     */
    public static function contributionMilestones(): array
    {
        return [
            ['badge' => self::PrviKorak, 'threshold' => 1],
            ['badge' => self::Prispevkar, 'threshold' => 5],
            ['badge' => self::AktivniAvtor, 'threshold' => 15],
            ['badge' => self::ZvezdaSkupnosti, 'threshold' => 50],
            ['badge' => self::MojsterPriprav, 'threshold' => 100],
        ];
    }

    /**
     * Get the highest contribution badge for a given upload count.
     */
    public static function highestContributionBadge(int $uploadCount): ?self
    {
        $result = null;

        foreach (self::contributionMilestones() as $milestone) {
            if ($uploadCount >= $milestone['threshold']) {
                $result = $milestone['badge'];
            }
        }

        return $result;
    }
}
