<x-layouts.app :title="$user->display_name . ' – Moj profil'" mainClass="flex-1">
    <div class="relative">

        {{-- ── Hero section ── --}}
        <div class="relative overflow-hidden border-b border-border bg-card">
            {{-- Decorative confetti dots --}}
            <svg class="pointer-events-none absolute -right-6 -top-4 size-48 opacity-25" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/><circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/><circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/><circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/><circle cx="180" cy="30" r="4" fill="#0ea5e9" opacity=".6"/><circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/><circle cx="80" cy="55" r="4" fill="#10b981" opacity=".4"/><circle cx="125" cy="65" r="3" fill="#f59e0b" opacity=".5"/><circle cx="165" cy="50" r="5" fill="#6366f1" opacity=".4"/><circle cx="15" cy="100" r="4" fill="#ec4899" opacity=".5"/><circle cx="55" cy="95" r="3" fill="#0ea5e9" opacity=".6"/><circle cx="100" cy="105" r="4" fill="#a855f7" opacity=".5"/><circle cx="145" cy="90" r="3" fill="#10b981" opacity=".4"/><circle cx="185" cy="110" r="5" fill="#f59e0b" opacity=".5"/>
            </svg>
            <svg class="pointer-events-none absolute -left-8 bottom-0 size-36 rotate-90 opacity-20" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/><circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/><circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/><circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/><circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/><circle cx="80" cy="55" r="4" fill="#10b981" opacity=".4"/><circle cx="125" cy="65" r="3" fill="#f59e0b" opacity=".5"/>
            </svg>

            <div class="relative mx-auto max-w-6xl px-4 py-8 md:py-12">
                <div class="flex flex-col items-center gap-6 md:flex-row md:items-start md:gap-8">

                    {{-- Avatar --}}
                    <div class="flex size-24 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-100 to-emerald-200 text-2xl font-bold text-teal-700 shadow-sm md:size-28 md:text-3xl">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->display_name }}" class="size-full rounded-2xl object-cover" />
                        @else
                            {{ $user->initials }}
                        @endif
                    </div>

                    {{-- User info --}}
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="font-serif text-2xl font-bold text-foreground md:text-3xl">
                            {{ $user->display_name }}
                        </h1>

                        <div class="mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-sm text-muted-foreground md:justify-start">
                            <span class="flex items-center gap-1.5">
                                <x-icon-regular.calendar class="size-3.5" />
                                Član od {{ $user->created_at->translatedFormat('F Y') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <x-icon-regular.envelope class="size-3.5" />
                                {{ $user->email }}
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap justify-center gap-2 md:justify-start">
                            <flux:button as="a" href="{{ route('profile.edit') }}" 
                                variant="outline" 
                                icon="icon-regular.cog"
                                class="h-9!">
                                Uredi profil
                            </flux:button>

                            <flux:button as="a" href="{{ url('/dodajanje') }}" 
                                variant="primary" 
                                icon="icon-regular.plus"
                                class="h-9!">
                                Dodaj pripravo
                            </flux:button>
                        </div>
                    </div>
                </div>

                {{-- Stats cards --}}
                <div class="mt-8 grid grid-cols-3 gap-3">
                    <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/50">
                            <x-icon-regular.upload class="size-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ $uploadCount }}</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-300">Naloženih</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-800 dark:bg-sky-950/30">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/50">
                            <x-icon-regular.download class="size-5 text-sky-600 dark:text-sky-400" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-sky-700 dark:text-sky-400">{{ $downloadCount }}</p>
                            <p class="text-xs text-sky-600 dark:text-sky-300">Prenesenih</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-800 dark:bg-rose-950/30">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 dark:bg-rose-900/50">
                            <x-icon-regular.heart class="size-5 text-rose-600 dark:text-rose-400" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-rose-700 dark:text-rose-400">{{ $savedCount }}</p>
                            <p class="text-xs text-rose-600 dark:text-rose-300">Shranjenih</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Badges section ── --}}
        <div class="border-b border-border bg-card">
            <div class="mx-auto max-w-6xl px-4 py-8">
                <h2 class="mb-4 font-serif text-lg font-bold text-foreground">Značke</h2>
                <x-badge-progress :progress="$contributionProgress" />
                <x-badge-grid :allBadges="$allBadges" :earnedBadgeIds="$earnedBadgeIds" :categories="$categories" :categoryLabels="$categoryLabels" />
            </div>
        </div>

        {{-- ── Tabs section ── --}}
        <div class="relative mx-auto max-w-6xl px-4 py-8 md:py-12"
             x-data="{ activeTab: 'uploaded' }">

            <div class="mb-6 flex h-auto w-full justify-start gap-1 rounded-xl border border-border bg-card p-1.5">
                <button
                    @click="activeTab = 'uploaded'"
                    :class="activeTab === 'uploaded' ? 'bg-emerald-500 text-white shadow-md' : 'text-muted-foreground hover:text-foreground hover:bg-secondary'"
                    class="flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all"
                >
                    <x-icon-regular.upload class="size-3.5" />
                    Naložene priprave
                    <span :class="activeTab === 'uploaded' ? 'bg-emerald-600 text-emerald-100' : 'bg-emerald-100 text-emerald-700'"
                          class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-bold">
                        {{ $uploadCount }}
                    </span>
                </button>

                <button
                    @click="activeTab = 'downloaded'"
                    :class="activeTab === 'downloaded' ? 'bg-sky-500 text-white shadow-md' : 'text-muted-foreground hover:text-foreground hover:bg-secondary'"
                    class="flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all"
                >
                    <x-icon-regular.download class="size-3.5" />
                    Prenesene
                    <span :class="activeTab === 'downloaded' ? 'bg-sky-600 text-sky-100' : 'bg-sky-100 text-sky-700'"
                          class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-bold">
                        {{ $downloadCount }}
                    </span>
                </button>

                <button
                    @click="activeTab = 'saved'"
                    :class="activeTab === 'saved' ? 'bg-rose-500 text-white shadow-md' : 'text-muted-foreground hover:text-foreground hover:bg-secondary'"
                    class="flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-semibold transition-all"
                >
                    <x-icon-regular.heart class="size-3.5" />
                    Shranjene
                    <span :class="activeTab === 'saved' ? 'bg-rose-600 text-rose-100' : 'bg-rose-100 text-rose-700'"
                          class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-bold">
                        {{ $savedCount }}
                    </span>
                </button>
            </div>

            <div x-show="activeTab === 'uploaded'" x-cloak>
                @livewire('uploaded-documents-tab')
            </div>
            <div x-show="activeTab === 'downloaded'" x-cloak>
                @livewire('downloaded-documents-tab')
            </div>
            <div x-show="activeTab === 'saved'" x-cloak>
                @livewire('saved-documents-tab')
            </div>
        </div>

    </div>
</x-layouts.app>
