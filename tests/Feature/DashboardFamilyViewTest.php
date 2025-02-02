<?php

use App\Enums\PanelId;
use App\Filament\Pages\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    PanelId::FAMILY->setCurrentPanel();
});

it('shows user filter', function () {
    livewire(Dashboard::class)->assertSeeText('Users');
});
