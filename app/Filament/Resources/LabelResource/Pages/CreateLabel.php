<?php

namespace App\Filament\Resources\LabelResource\Pages;

use App\Filament\Resources\LabelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLabel extends CreateRecord
{
    protected static string $resource = LabelResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
