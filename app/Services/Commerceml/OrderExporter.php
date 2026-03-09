<?php

namespace App\Services\Commerceml;

use App\Models\Order;
use Psr\Log\LoggerInterface;

class OrderExporter
{
    protected LoggerInterface $log;
    protected int $exportedCount = 0;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Экспорт заказов в XML формате CommerceML.
     */
    public function export(): string
    {
        $statuses = config('commerceml.export_order_statuses', ['new', 'processing']);

        $orders = Order::with('items.product')
            ->whereNull('commerceml_exported_at')
            ->whereIn('status', $statuses)
            ->orderBy('created_at')
            ->get();

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);

        $xml->startElement('КоммерческаяИнформация');
        $xml->writeAttribute('ВерсияСхемы', '2.10');
        $xml->writeAttribute('ДатаФормирования', now()->format('Y-m-d\TH:i:s'));

        foreach ($orders as $order) {
            $this->writeOrder($xml, $order);
        }

        $xml->endElement(); // КоммерческаяИнформация
        $xml->endDocument();

        // Пометить заказы как экспортированные
        if ($orders->isNotEmpty()) {
            Order::whereIn('id', $orders->pluck('id'))
                ->update(['commerceml_exported_at' => now()]);

            $this->exportedCount = $orders->count();
            $this->log->info("OrderExporter: exported {$this->exportedCount} orders");
        }

        return $xml->outputMemory();
    }

    public function getExportedCount(): int
    {
        return $this->exportedCount;
    }

    /**
     * Записать один заказ в XML.
     */
    protected function writeOrder(\XMLWriter $xml, Order $order): void
    {
        $xml->startElement('Документ');

        $xml->writeElement('Ид', $order->order_number);
        $xml->writeElement('Номер', $order->order_number);
        $xml->writeElement('Дата', $order->created_at->format('Y-m-d'));
        $xml->writeElement('Время', $order->created_at->format('H:i:s'));
        $xml->writeElement('ХозОперация', 'Заказ товара');
        $xml->writeElement('Роль', 'Продавец');
        $xml->writeElement('Валюта', 'RUB');
        $xml->writeElement('Курс', '1');
        $xml->writeElement('Сумма', number_format((float) $order->total, 2, '.', ''));

        // Контрагент
        $this->writeContragent($xml, $order);

        // Товары
        $xml->startElement('Товары');
        foreach ($order->items as $item) {
            $this->writeOrderItem($xml, $item);
        }

        // Доставка как услуга
        if ((float) $order->shipping_total > 0) {
            $this->writeShippingItem($xml, $order);
        }

        // Скидка как услуга
        if ((float) $order->discount_amount > 0) {
            $this->writeDiscountItem($xml, $order);
        }

        $xml->endElement(); // Товары

        // Реквизиты заказа
        $this->writeRequisites($xml, $order);

        $xml->endElement(); // Документ
    }

    /**
     * Контрагент (покупатель).
     */
    protected function writeContragent(\XMLWriter $xml, Order $order): void
    {
        $xml->startElement('Контрагенты');
        $xml->startElement('Контрагент');

        $fullName = trim("{$order->customer_last_name} {$order->customer_first_name}");
        $xml->writeElement('Ид', $order->order_number . '#customer');
        $xml->writeElement('Наименование', $fullName);
        $xml->writeElement('Роль', 'Покупатель');
        $xml->writeElement('ПолноеНаименование', $fullName);

        // Адрес
        $address = implode(', ', array_filter([
            $order->customer_postal_code,
            $order->customer_region,
            $order->customer_city,
            $order->customer_address_line_1,
            $order->customer_address_line_2,
        ]));

        if (! empty($address)) {
            $xml->startElement('АдресРегистрации');
            $xml->writeElement('Представление', $address);
            $xml->endElement();
        }

        // Контакты
        $xml->startElement('Контакты');

        if (! empty($order->customer_phone)) {
            $xml->startElement('Контакт');
            $xml->writeElement('Тип', 'ТелефонРабочий');
            $xml->writeElement('Значение', $order->customer_phone);
            $xml->endElement();
        }

        if (! empty($order->customer_email)) {
            $xml->startElement('Контакт');
            $xml->writeElement('Тип', 'Почта');
            $xml->writeElement('Значение', $order->customer_email);
            $xml->endElement();
        }

        $xml->endElement(); // Контакты
        $xml->endElement(); // Контрагент
        $xml->endElement(); // Контрагенты
    }

    /**
     * Позиция товара в заказе.
     */
    protected function writeOrderItem(\XMLWriter $xml, \App\Models\OrderItem $item): void
    {
        $xml->startElement('Товар');

        $externalId = $item->product?->external_id ?? $item->sku ?? "item-{$item->id}";
        $xml->writeElement('Ид', $externalId);
        $xml->writeElement('Артикул', $item->sku ?? '');
        $xml->writeElement('Наименование', $item->name);

        $xml->startElement('БазоваяЕдиница');
        $xml->writeAttribute('Код', '796');
        $xml->writeAttribute('НаименованиеПолное', 'Штука');
        $xml->text('шт');
        $xml->endElement();

        $xml->writeElement('ЦенаЗаЕдиницу', number_format((float) $item->price, 2, '.', ''));
        $xml->writeElement('Количество', $item->quantity);
        $xml->writeElement('Сумма', number_format((float) $item->line_total, 2, '.', ''));

        $xml->endElement(); // Товар
    }

    /**
     * Доставка как позиция заказа.
     */
    protected function writeShippingItem(\XMLWriter $xml, Order $order): void
    {
        $xml->startElement('Товар');
        $xml->writeElement('Ид', 'ORDER_DELIVERY');
        $xml->writeElement('Наименование', 'Доставка');

        $xml->startElement('БазоваяЕдиница');
        $xml->writeAttribute('Код', '796');
        $xml->writeAttribute('НаименованиеПолное', 'Штука');
        $xml->text('шт');
        $xml->endElement();

        $xml->writeElement('ЦенаЗаЕдиницу', number_format((float) $order->shipping_total, 2, '.', ''));
        $xml->writeElement('Количество', '1');
        $xml->writeElement('Сумма', number_format((float) $order->shipping_total, 2, '.', ''));
        $xml->endElement();
    }

    /**
     * Скидка как позиция заказа.
     */
    protected function writeDiscountItem(\XMLWriter $xml, Order $order): void
    {
        $xml->startElement('Товар');
        $xml->writeElement('Ид', 'ORDER_DISCOUNT');
        $xml->writeElement('Наименование', 'Скидка');

        $xml->startElement('БазоваяЕдиница');
        $xml->writeAttribute('Код', '796');
        $xml->writeAttribute('НаименованиеПолное', 'Штука');
        $xml->text('шт');
        $xml->endElement();

        $amount = -1 * abs((float) $order->discount_amount);
        $xml->writeElement('ЦенаЗаЕдиницу', number_format($amount, 2, '.', ''));
        $xml->writeElement('Количество', '1');
        $xml->writeElement('Сумма', number_format($amount, 2, '.', ''));
        $xml->endElement();
    }

    /**
     * Реквизиты заказа (статус, способ оплаты и т.д.)
     */
    protected function writeRequisites(\XMLWriter $xml, Order $order): void
    {
        $xml->startElement('ЗначенияРеквизитов');

        $this->writeRequisite($xml, 'Статус заказа', $this->mapOrderStatus($order->status));
        $this->writeRequisite($xml, 'Способ оплаты', $order->payment_method ?? 'Не указан');
        $this->writeRequisite($xml, 'Статус оплаты', $order->payment_status ?? 'pending');

        if (! empty($order->comment)) {
            $this->writeRequisite($xml, 'Комментарий', $order->comment);
        }

        if ($order->delivery_provider) {
            $this->writeRequisite($xml, 'Способ доставки', $order->delivery_provider);
        }

        $xml->endElement();
    }

    protected function writeRequisite(\XMLWriter $xml, string $name, string $value): void
    {
        $xml->startElement('ЗначениеРеквизита');
        $xml->writeElement('Наименование', $name);
        $xml->writeElement('Значение', $value);
        $xml->endElement();
    }

    /**
     * Маппинг статуса заказа сайта → текст для 1С.
     */
    protected function mapOrderStatus(string $status): string
    {
        return match ($status) {
            'new' => 'Новый',
            'processing' => 'В обработке',
            'completed' => 'Выполнен',
            'cancelled' => 'Отменён',
            default => $status,
        };
    }
}
