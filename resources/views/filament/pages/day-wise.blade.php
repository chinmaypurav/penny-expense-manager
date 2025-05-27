<x-filament-panels::page>
    {{ $this->form }}
    <livewire:day-wise-incomes :$filters />
    <livewire:day-wise-expenses :$filters />
    <livewire:day-wise-transfers :$filters />
</x-filament-panels::page>
