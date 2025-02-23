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

        Mail::to($data['email'])->send(
            new SendUserCreatedMail($data['name'], $data['password'])->afterCommit()
        );

        return $data;
    }
}
