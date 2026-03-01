@props([
    'title' => config('app.name', 'Priprave.net'),
    'mainClass' => 'flex-1'
])
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=source-serif-4:400,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">

    {{-- Header --}}
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 border-b border-border bg-card/80 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="relative flex items-center justify-center">
                    <img src="/images/icon.png" class="h-9"/>
                </div>
                <span class="font-serif text-lg font-bold tracking-tight text-foreground">
                    Priprave<span class="text-primary">.net</span>
                </span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('browse') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    Brskanje
                </a>
                <a href="{{ url('/pomoc') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    Pomoč
                </a>
            </nav>

            {{-- Desktop actions --}}
            <div class="hidden items-center gap-2 md:flex">
                {{-- Dark mode toggle --}}
                <flux:dropdown x-data x-cloak position="bottom" align="end">
                    <flux:button
                        variant="ghost"
                        square
                        aria-label="Barvna shema"
                        class="group flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                    >
                        <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" />
                        <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" />
                        <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                        <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Svetli</flux:menu.item>
                        <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Temni</flux:menu.item>
                        <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">Sistemski</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:button as="a" href="{{ url('/dodajanje') }}"
                    variant="outline"
                    icon="icon-regular.plus"
                    class="h-9!">
                    Dodaj pripravo
                </flux:button>

                @auth
                    <flux:dropdown position="bottom" align="end">
                        <flux:button
                            variant="subtle"
                            class="flex items-center gap-2 rounded-full border border-border bg-background py-1 pl-1 pr-3 transition-colors hover:bg-secondary"
                        >
                            <div class="flex size-7 items-center justify-center rounded-full bg-primary/10">
                                <x-icon-regular.user class="size-3.5" />
                            </div>
                            <span class="text-sm font-medium text-foreground">{{ auth()->user()->name }}</span>
                        </flux:button>
                        <flux:navmenu>
                            <flux:navmenu.item href="{{ url('/profil') }}" icon="icon-regular.user">
                                Moj profil
                            </flux:navmenu.item>
                            <flux:navmenu.item href="{{ route('profile.edit') }}" icon="icon-regular.cog">
                                Uredi profil
                            </flux:navmenu.item>
                            @if (auth()->user()->role === 'admin')
                                <flux:navmenu.item href="{{ url('/admin') }}" icon="icon-regular.shield-check">
                                    Admin panel
                                </flux:navmenu.item>
                            @endif
                            <flux:navmenu.separator />
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <flux:navmenu.item as="button"
                                    icon="icon-regular.arrow-right-from-bracket"
                                    type="submit">Odjava</flux:navmenu.item>
                            </form>
                        </flux:navmenu>
                    </flux:dropdown>
                @else
                    <flux:button as="a" href="{{ route('login') }}"
                        variant="primary"
                        icon="icon-regular.user"
                        class="h-9!">
                        Prijava
                    </flux:button>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <flux:button
                variant="ghost"
                square
                @click="mobileOpen = !mobileOpen"
                aria-label="Preklopi meni"
                class="flex size-9 items-center justify-center rounded-md text-muted-foreground md:hidden"
            >
                <template x-if="mobileOpen">
                    <x-icon-regular.x  class="size-4"/>
                </template>
                <template x-if="!mobileOpen">
                    <x-icon-regular.bars class="size-4"/>
                </template>
            </flux:button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" class="border-t border-border bg-card px-4 pb-4 pt-2 md:hidden">
            <nav class="flex flex-col gap-1">
                <a href="{{ route('browse') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-secondary hover:text-foreground">
                    Brskanje
                </a>
                <a href="{{ url('/pomoc') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-secondary hover:text-foreground">
                    Pomoč
                </a>
            </nav>
            <div class="mt-3 flex flex-col gap-2">
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="w-full">
                    <flux:radio value="light" icon="sun">Svetli</flux:radio>
                    <flux:radio value="dark" icon="moon">Temni</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">Sistemski</flux:radio>
                </flux:radio.group>

                <flux:button as="a" href="{{ url('/dodajanje') }}" variant="outline" size="sm" class="inline-flex w-full items-center justify-center gap-1.5 rounded-md border border-border bg-background px-3 !py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                    <x-icon-regular.plus class="size-3.5" />
                    Dodaj pripravo
                </flux:button>

                @auth
                    <a href="{{ url('/profil') }}" class="flex items-center gap-2 rounded-lg border border-border bg-background px-3 py-2 transition-colors hover:bg-secondary">
                        <div class="flex size-7 items-center justify-center rounded-full bg-primary/10">
                            <x-icon-regular.user class="size-3.5" />
                        </div>
                        <span class="text-sm font-medium text-foreground">{{ auth()->user()->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button type="submit" variant="ghost" align="start" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-muted-foreground hover:bg-secondary hover:text-foreground">
                            Odjava
                        </flux:button>
                    </form>
                @else
                    <flux:button as="a" href="{{ route('login') }}" variant="primary" size="sm" class="inline-flex w-full items-center justify-center gap-1.5 rounded-md bg-primary px-3 !py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                        <x-icon-regular.user class="size-3.5" />
                        Prijava
                    </flux:button>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="{{ $mainClass }}">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-border bg-card">
        <div class="mx-auto max-w-6xl px-4 py-12">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <div class="flex items-center justify-center">
                            <img src="/images/icon.png" class="h-8"/>
                        </div>
                        <span class="text-base font-bold text-foreground">
                            Priprave<span class="text-primary">.net</span>
                        </span>
                    </a>
                    <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
                        Spletna stran z učnimi gradivi za predšolsko vzgojo, razredni
                        pouk in nadaljnje šolanje.
                    </p>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-foreground">Priprave</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('browse') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Brskanje</a></li>
                        <li><a href="{{ url('/dodajanje') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Dodajanje</a></li>
                        <li><a href="{{ route('browse', ['stopnja' => 'pv']) }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Predšolska vzgoja</a></li>
                        <li><a href="{{ route('browse', ['stopnja' => 'os']) }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Osnovna šola</a></li>
                        <li><a href="{{ route('browse', ['stopnja' => 'ss']) }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Srednja šola</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-foreground">Profil</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('register') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Registracija</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Prijava</a></li>
                        <li><a href="{{ url('/profil') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Moj profil</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-foreground">O strani</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/pomoc') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Pomoč</a></li>
                        <li><a href="{{ url('/pogoji-uporabe') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Pogoji uporabe</a></li>
                        <li><a href="{{ url('/kontakt') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Kontakt</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-border pt-6 md:flex-row">
                <p class="text-xs text-muted-foreground">&copy; {{ date('Y') }} Priprave.net &ndash; vse pravice pridržane</p>
                <p class="text-xs text-muted-foreground">Vodja projekta: Jasna</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @fluxScripts
</body>
</html>
