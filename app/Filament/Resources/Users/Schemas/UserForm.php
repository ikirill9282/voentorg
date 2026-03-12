<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Личные данные')
                    ->schema([
                        TextInput::make('last_name')
                            ->label('Фамилия')
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->label('Имя')
                            ->maxLength(255),
                        TextInput::make('patronymic')
                            ->label('Отчество')
                            ->maxLength(255),
                        TextInput::make('callsign')
                            ->label('Позывной')
                            ->maxLength(255),
                        DatePicker::make('birthday')
                            ->label('Дата рождения'),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(80)
                            ->unique(ignoreRecord: true),
                        TextInput::make('telegram_username')
                            ->label('Telegram')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null),
                        Toggle::make('is_admin')
                            ->label('Администратор')
                            ->default(false),
                    ])
                    ->columns(2),
                Section::make('Бонусная программа')
                    ->schema([
                        TextInput::make('bonus_balance')
                            ->label('Баланс бонусов')
                            ->numeric()
                            ->default(0),
                        TextInput::make('total_spent')
                            ->label('Сумма покупок')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('loyalty_tier')
                            ->label('Уровень лояльности')
                            ->disabled(),
                        TextInput::make('external_id')
                            ->label('Внешний ID (1С)')
                            ->maxLength(36),
                    ])
                    ->columns(2),
            ]);
    }
}
