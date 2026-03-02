<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Имя')
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email')
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Телефон')
                    ->disabled(),
                Textarea::make('message')
                    ->label('Сообщение')
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->label('Прочитано'),
            ]);
    }
}
