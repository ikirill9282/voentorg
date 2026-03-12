<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Заказ')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Номер заказа')
                            ->disabled(),
                        Select::make('status')
                            ->label('Статус')
                            ->required()
                            ->options([
                                Order::STATUS_NEW => 'Новый',
                                Order::STATUS_PROCESSING => 'В обработке',
                                Order::STATUS_COMPLETED => 'Выполнен',
                                Order::STATUS_CANCELLED => 'Отменён',
                            ]),
                        Select::make('payment_status')
                            ->label('Статус оплаты')
                            ->required()
                            ->options([
                                Order::PAYMENT_PENDING => 'Ожидает',
                                Order::PAYMENT_PAID => 'Оплачен',
                                Order::PAYMENT_FAILED => 'Ошибка',
                            ]),
                        Select::make('payment_method')
                            ->label('Метод оплаты')
                            ->required()
                            ->options([
                                'online_payment' => 'Оплата онлайн',
                                'bank_transfer' => 'Банковский перевод',
                                'cash_on_delivery' => 'Оплата при получении',
                                'invoice' => 'Счет для юр. лица',
                            ]),
                        Select::make('shipping_method_id')
                            ->label('Метод доставки')
                            ->relationship('shippingMethod', 'name')
                            ->preload(),
                        Select::make('delivery_company_id')
                            ->label('Транспортная компания')
                            ->relationship('deliveryCompany', 'name')
                            ->preload()
                            ->placeholder('Не выбрана'),
                        TextInput::make('delivery_region')
                            ->label('Регион доставки'),
                        TextInput::make('delivery_provider')
                            ->label('Провайдер доставки')
                            ->disabled(),
                        Select::make('pickup_store_id')
                            ->label('Магазин самовывоза')
                            ->relationship('pickupStore', 'name')
                            ->preload()
                            ->placeholder('Не выбран'),
                        TextInput::make('pickup_estimated_days')
                            ->label('Срок доставки (дни)')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('cdek_tracking_number')
                            ->label('СДЭК трек-номер'),
                        TextInput::make('yandex_claim_id')
                            ->label('Яндекс Claim ID')
                            ->disabled(),
                        TextInput::make('payment_id')
                            ->label('ID платежа ВТБ')
                            ->disabled(),
                        TextInput::make('paid_at')
                            ->label('Дата оплаты')
                            ->disabled(),
                        TextInput::make('subtotal')
                            ->label('Сумма товаров')
                            ->numeric(),
                        TextInput::make('shipping_total')
                            ->label('Стоимость доставки')
                            ->numeric(),
                        TextInput::make('bonus_used')
                            ->label('Бонусов списано')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('bonus_earned')
                            ->label('Бонусов начислено')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('total')
                            ->label('Итого')
                            ->numeric(),
                    ])
                    ->columns(3),
                Section::make('Клиент')
                    ->schema([
                        TextInput::make('customer_first_name')
                            ->label('Имя')
                            ->required(),
                        TextInput::make('customer_last_name')
                            ->label('Фамилия'),
                        TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        TextInput::make('customer_phone')
                            ->label('Телефон')
                            ->required(),
                        TextInput::make('customer_address_line_1')
                            ->label('Адрес'),
                        TextInput::make('customer_address_line_2')
                            ->label('Адрес (доп.)'),
                        TextInput::make('customer_city')
                            ->label('Город'),
                        TextInput::make('customer_region')
                            ->label('Регион'),
                        TextInput::make('customer_postal_code')
                            ->label('Индекс'),
                        TextInput::make('customer_country')
                            ->label('Страна')
                            ->default('RU'),
                    ])
                    ->columns(2),
                Section::make('Позиции заказа')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Товар')
                                    ->disabled(),
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->disabled(),
                                TextInput::make('price')
                                    ->label('Цена')
                                    ->numeric()
                                    ->disabled(),
                                TextInput::make('quantity')
                                    ->label('Кол-во')
                                    ->numeric()
                                    ->disabled(),
                                TextInput::make('line_total')
                                    ->label('Сумма')
                                    ->numeric()
                                    ->disabled(),
                            ])
                            ->columns(5)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
                Textarea::make('comment')
                    ->label('Комментарий')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
