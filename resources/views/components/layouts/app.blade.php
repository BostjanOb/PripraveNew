@props(['title' => config('app.name', 'Priprave.net')])

<!DOCTYPE html>
<html
    lang="sl"
    x-data="{ isDark: localStorage.getItem('dark') === 'true', mobileOpen: false }"
    :class="{ 'dark': isDark }"
    x-init="$watch('isDark', v => localStorage.setItem('dark', v))"
>
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
    <header class="sticky top-0 z-50 border-b border-border bg-card/80 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="relative flex size-9 items-center justify-center rounded-lg bg-primary">
                    {{-- BookOpen icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-primary-foreground">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    {{-- Pencil icon accent --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute -right-1 -top-1 size-3.5 rotate-45 text-accent">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </div>
                <span class="font-serif text-lg font-bold tracking-tight text-foreground">
                    Priprave<span class="text-primary">.net</span>
                </span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ url('/brskanje') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    Brskanje
                </a>
                <a href="{{ url('/pomoc') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    Pomoč
                </a>
            </nav>

            {{-- Desktop actions --}}
            <div class="hidden items-center gap-2 md:flex">
                {{-- Dark mode toggle --}}
                <flux:button
                    variant="ghost"
                    square
                    @click="isDark = !isDark"
                    ::aria-label="isDark ? 'Preklopi na svetli način' : 'Preklopi na temni način'"
                    class="flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                >
                    <template x-if="isDark">
                        <x-icon-regular.sun-alt class="size-3.5" />
                    </template>
                    <template x-if="!isDark">
                        <x-icon-regular.moon class="size-3.5 -rotate-12" />
                    </template>
                </flux:button>

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
                    {{-- X icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </template>
                <template x-if="!mobileOpen">
                    {{-- Bars3 icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </template>
            </flux:button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" class="border-t border-border bg-card px-4 pb-4 pt-2 md:hidden">
            <nav class="flex flex-col gap-1">
                <a href="{{ url('/brskanje') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-secondary hover:text-foreground">
                    Brskanje
                </a>
                <a href="{{ url('/pomoc') }}" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-secondary hover:text-foreground">
                    Pomoč
                </a>
            </nav>
            <div class="mt-3 flex flex-col gap-2">
                <flux:button
                    variant="ghost"
                    @click="isDark = !isDark"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-secondary hover:text-foreground"
                >
                    <template x-if="isDark">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </template>
                    <template x-if="!isDark">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                    </template>
                    <span x-text="isDark ? 'Svetli način' : 'Temni način'"></span>
                </flux:button>

                <flux:button as="a" href="{{ url('/dodajanje') }}" variant="outline" size="sm" class="inline-flex w-full items-center justify-center gap-1.5 rounded-md border border-border bg-background px-3 !py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Dodaj pripravo
                </flux:button>

                @auth
                    <a href="{{ url('/profil') }}" class="flex items-center gap-2 rounded-lg border border-border bg-background px-3 py-2 transition-colors hover:bg-secondary">
                        <div class="flex size-7 items-center justify-center rounded-full bg-primary/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Prijava
                    </flux:button>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="flex flex-1 items-center justify-center px-4 py-12">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-border bg-card">
        <div class="mx-auto max-w-6xl px-4 py-12">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-primary-foreground">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <span class="text-base font-bold text-foreground">
                            Priprave<span class="text-primary">.net</span>
                        </span>
                    </a>
                    <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
                        Spletna stran z učnimi pripravami za predšolsko vzgojo, razredni
                        pouk in nadaljnje šolanje.
                    </p>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-foreground">Priprave</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/brskanje') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Brskanje</a></li>
                        <li><a href="{{ url('/dodajanje') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Dodajanje</a></li>
                        <li><a href="{{ url('/brskanje?stopnja=predskolska') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Predšolska vzgoja</a></li>
                        <li><a href="{{ url('/brskanje?stopnja=osnovna') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Osnovna šola</a></li>
                        <li><a href="{{ url('/brskanje?stopnja=srednja') }}" class="text-sm text-muted-foreground transition-colors hover:text-foreground">Srednja šola</a></li>
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
