<?php

namespace App\Filament\Resources\TagResource;

use App\Models\Tag;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (?Tag $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (?Tag $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('name')
                    ->required(),

                ColorPicker::make('color')
                    ->default(
                        fn () => Str::of('#')
                            ->append(bin2hex(random_bytes(3)))
                            ->upper()
                            ->value()
                    )
                    ->required(),
            ]);
    }
}
