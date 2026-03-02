<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('sheets_export')
                ->label('Экспорт в Google Таблицу')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Экспорт товаров')
                ->modalDescription('Все данные в Google Таблице будут перезаписаны текущими товарами сайта. Продолжить?')
                ->action(function () {
                    try {
                        Artisan::call('sheets:export');
                        $output = Artisan::output();

                        Notification::make()
                            ->title('Экспорт завершён')
                            ->body(nl2br(e(trim($output))))
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Ошибка экспорта')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('sheets_import')
                ->label('Импорт из Google Таблицы')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Импорт товаров')
                ->modalDescription('Товары на сайте будут обновлены данными из Google Таблицы. Новые товары будут созданы. Продолжить?')
                ->action(function () {
                    try {
                        Artisan::call('sheets:import');
                        $output = Artisan::output();

                        Notification::make()
                            ->title('Импорт завершён')
                            ->body(nl2br(e(trim($output))))
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Ошибка импорта')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
