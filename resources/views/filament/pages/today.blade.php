<x-filament-panels::page>
    {{ $this->form }}
    <livewire:today-incomes :$filters />
    <livewire:today-expenses :$filters />
    <livewire:today-transfers :$filters />
</x-filament-panels::page>
