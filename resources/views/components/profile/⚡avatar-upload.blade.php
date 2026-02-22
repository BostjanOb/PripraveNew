<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $avatar;

    public function saveAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $this->avatar->store('avatars', 'public');

        $user->forceFill(['avatar_path' => $path])->save();

        $this->avatar = null;
        $this->dispatch('avatar-updated');
    }

    public function render()
    {
        return $this->view(['user' => auth()->user()]);
    }
}

?>
<div class="rounded-2xl border border-border bg-card p-6 md:p-8">
    <div class="mb-5 flex items-center gap-2">
        <div class="flex size-8 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/50">
            <x-icon-regular.camera class="size-4 text-teal-600 dark:text-teal-400" />
        </div>
        <h2 class="font-serif text-lg font-bold text-foreground">Profilna slika</h2>
    </div>

    <div class="flex flex-col items-center gap-5 sm:flex-row">
        <div class="flex size-24 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-100 to-emerald-200 text-2xl font-bold text-teal-700 shadow-sm">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->display_name }}" class="size-full rounded-2xl object-cover" />
            @else
                {{ $user->initials }}
            @endif
        </div>

        <div class="text-center sm:text-left">
            <form wire:submit="saveAvatar" class="space-y-3">
                <flux:field>
                    <flux:input type="file" wire:model="avatar" accept="image/*" />
                    <flux:error name="avatar" />
                </flux:field>
                <flux:button type="submit" size="sm" icon="icon-regular.camera">
                    Zamenjaj sliko
                </flux:button>
            </form>
            <p class="mt-2 text-xs text-muted-foreground">JPG, PNG ali GIF. Največ 2 MB.</p>
        </div>
    </div>
</div>
