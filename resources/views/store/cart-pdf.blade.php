<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; }
        .totals p { margin: 4px 0; font-size: 14px; }
        .totals .total { font-size: 16px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Корзина — COLCHUGA</h1>
    <p>Дата: {{ now()->format('d.m.Y H:i') }}</p>

    @if ($cart['is_empty'])
        <p>Корзина пуста.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Артикул</th>
                    <th>Наименование</th>
                    <th>Размер</th>
                    <th>Цвет</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart['items'] as $item)
                    <tr>
                        <td>{{ $item['sku'] ?? '' }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['size'] ?? '-' }}</td>
                        <td>{{ $item['color'] ?? '-' }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td class="text-right">{{ number_format($item['price'], 0, '', ' ') }} ₽</td>
                        <td class="text-right">{{ number_format($item['line_total'], 0, '', ' ') }} ₽</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p>Товары ({{ $cart['total_quantity'] }}): {{ number_format($cart['subtotal'], 0, '', ' ') }} ₽</p>
            @if ($cart['discount'] > 0)
                <p>Скидка: -{{ number_format($cart['discount'], 0, '', ' ') }} ₽</p>
            @endif
            <p class="total">Итого: {{ number_format($cart['total'], 0, '', ' ') }} ₽</p>
        </div>
    @endif
</body>
</html>
