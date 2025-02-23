<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\SendUserCreatedMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Str::password(symbols: false);

        Mail::to('one@ex.co')->send(
            new SendUserCreatedMail($data['password'])->afterCommit()
        );

        return $data;
    }
}
