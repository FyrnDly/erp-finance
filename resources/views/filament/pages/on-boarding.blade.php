<x-filament-panels::page>
    @if ($isManager)
        <form wire:submit="create">
            {{ $this->form }}
        </form>

        <x-filament-actions::modals />
    @endif
</x-filament-panels::page>
