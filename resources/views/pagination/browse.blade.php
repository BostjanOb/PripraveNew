@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="flex items-center justify-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground opacity-40">
                    <x-icon-regular.angle-left class="size-4" />
                </span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    wire:loading.attr="disabled"
                    class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground"
                    aria-label="@lang('pagination.previous')"
                >
                    <x-icon-regular.angle-left class="size-4" />
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-muted-foreground">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="flex size-10 items-center justify-center rounded-xl border border-teal-400 bg-teal-500 text-sm font-semibold text-white shadow-md shadow-teal-200/50 dark:shadow-teal-900/30">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                type="button"
                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                                class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-sm font-semibold text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground"
                                aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    wire:loading.attr="disabled"
                    class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground"
                    aria-label="@lang('pagination.next')"
                >
                    <x-icon-regular.angle-right class="size-4" />
                </button>
            @else
                <span class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground opacity-40">
                    <x-icon-regular.angle-right class="size-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
