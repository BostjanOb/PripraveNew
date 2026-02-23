<?php

use App\Models\Document;
use App\Models\Report;
use App\Models\ReportReason;
use Livewire\Component;

new class extends Component
{
    public Document $document;

    public string $context = 'mobile';

    public ?int $reportReasonId = null;

    public string $message = '';

    public bool $submitted = false;

    public function mount(Document $document, string $context = 'mobile'): void
    {
        $this->document = $document;
        $this->context = $context;
    }

    public function submit(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->validate([
            'reportReasonId' => ['required', 'exists:report_reasons,id'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Report::query()->create([
            'document_id' => $this->document->id,
            'user_id' => $user->id,
            'report_reason_id' => $this->reportReasonId,
            'message' => $this->message,
        ]);

        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->reportReasonId = null;
        $this->message = '';
        $this->submitted = false;
    }
};
?>

<div>
    <flux:modal.trigger :name="'report-document-' . $context">
        <button class="flex items-center gap-1 text-xs text-muted-foreground transition-colors hover:text-destructive">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
            </svg>
            Prijavi neustrezno
        </button>
    </flux:modal.trigger>

    <flux:modal :name="'report-document-' . $context" class="md:w-96" x-on:close="$wire.resetForm()">
        @if($submitted)
            <div class="py-6 text-center">
                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-emerald-600 dark:text-emerald-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <flux:heading size="lg">Prijava poslana</flux:heading>
                <p class="mt-2 text-sm text-muted-foreground">
                    Hvala za vašo prijavo. Pregledali jo bomo v najkrajšem možnem času.
                </p>
                <flux:button x-on:click="$flux.modal('report-document-{{ $context }}').close()" variant="ghost" class="mt-5">
                    Zapri
                </flux:button>
            </div>
        @else
            <div>
                <div class="mx-auto mb-2 flex size-12 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-rose-600 dark:text-rose-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <flux:heading size="lg" class="text-center">Prijavi neustrezno vsebino</flux:heading>
                <p class="mt-2 text-center text-sm text-muted-foreground">
                    Če menite, da je ta vsebina neprimerna, nam to sporočite.
                </p>
            </div>

            <form wire:submit="submit" class="mt-4 space-y-4">
                <flux:field>
                    <flux:label>Razlog prijave</flux:label>
                    <flux:select wire:model="reportReasonId" placeholder="Izberite razlog...">
                        @foreach(ReportReason::query()->orderBy('sort_order')->get() as $reason)
                            <flux:select.option :value="$reason->id">{{ $reason->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="reportReasonId" />
                </flux:field>

                <flux:field>
                    <flux:label>Sporočilo</flux:label>
                    <flux:textarea wire:model="message" placeholder="Opišite težavo..." rows="4" />
                    <flux:error name="message" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button x-on:click="$flux.modal('report-document-{{ $context }}').close()" variant="ghost">
                        Prekliči
                    </flux:button>
                    <flux:button type="submit" variant="danger" icon="flag" :disabled="! $reportReasonId">
                        Pošlji prijavo
                    </flux:button>
                </div>
            </form>
        @endif
    </flux:modal>
</div>
