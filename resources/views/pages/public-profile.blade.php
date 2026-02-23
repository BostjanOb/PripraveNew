<x-layouts.app :title="$user->display_name . ' – Profil'" mainClass="flex-1">
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
                <div class="flex flex-col items-center gap-6 md:flex-row md:items-center md:gap-8">

                    {{-- Avatar --}}
                    <div class="flex size-20 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-100 to-emerald-200 text-xl font-bold text-teal-700 shadow-sm md:size-24 md:text-2xl">
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

                        <div class="mt-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-sm text-muted-foreground md:justify-start">
                            <span class="flex items-center gap-1.5">
                                <x-icon-regular.calendar class="size-3.5" />
                                Član od {{ $user->created_at->translatedFormat('F Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Upload count stat --}}
                    <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/50 md:px-6">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/50">
                            <x-icon-regular.upload class="size-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300">{{ $uploadCount }}</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">Naloženih priprav</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Badges section ── --}}
        @if(count($earnedBadgeIds) > 0)
            <div class="border-b border-border bg-card">
                <div class="mx-auto max-w-6xl px-4 py-6">
                    <h2 class="mb-3 text-sm font-semibold text-foreground">Značke</h2>
                    <x-badge-grid
                        :allBadges="$allBadges"
                        :earnedBadgeIds="$earnedBadgeIds"
                        :compact="true"
                        :showAll="false"
                    />
                </div>
            </div>
        @endif

        {{-- ── Uploaded documents ── --}}
        <div class="relative mx-auto max-w-6xl px-4 py-8 md:py-12">

            {{-- Decorative elements --}}
            <x-decorations.paperclip class="pointer-events-none absolute -left-1 top-32 hidden w-5 -rotate-12 opacity-20 lg:block xl:left-4" />
            <x-decorations.small-plant class="pointer-events-none absolute -right-2 bottom-16 hidden w-11 opacity-20 lg:block xl:right-4" />

            <div class="mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-emerald-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>
                <h2 class="text-sm font-semibold text-foreground">
                    Naložene priprave ({{ $uploadCount }})
                </h2>
            </div>

            @livewire('public-uploaded-documents', ['userId' => $user->id])
        </div>

    </div>
</x-layouts.app>
