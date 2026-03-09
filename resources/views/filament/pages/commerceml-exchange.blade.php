<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Статус --}}
        <div class="fi-wi-stats-overview-stat rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Статус</span>
            </div>
            <div class="mt-1 text-xl font-semibold {{ $stats['enabled'] ? 'text-green-600' : 'text-red-600' }}">
                {{ $stats['enabled'] ? 'Включён' : 'Отключён' }}
            </div>
        </div>

        {{-- Последний обмен --}}
        <div class="fi-wi-stats-overview-stat rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Последний обмен</span>
            </div>
            <div class="mt-1 text-xl font-semibold text-gray-950 dark:text-white">
                {{ $stats['last_sync'] }}
            </div>
        </div>

        {{-- Привязано к 1С --}}
        <div class="fi-wi-stats-overview-stat rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Привязано к 1С</span>
            </div>
            <div class="mt-1 text-xl font-semibold text-gray-950 dark:text-white">
                {{ $stats['total_linked'] }} товаров
            </div>
            @if ($stats['total_unlinked'] > 0)
                <div class="text-sm text-amber-600">{{ $stats['total_unlinked'] }} не привязано</div>
            @endif
        </div>

        {{-- Последняя ошибка --}}
        <div class="fi-wi-stats-overview-stat rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Последняя ошибка</span>
            </div>
            <div class="mt-1 text-xl font-semibold {{ $stats['last_error_message'] ? 'text-red-600' : 'text-green-600' }}">
                {{ $stats['last_error'] }}
            </div>
            @if ($stats['last_error_message'])
                <div class="text-sm text-red-500 truncate" title="{{ $stats['last_error_message'] }}">
                    {{ Str::limit($stats['last_error_message'], 60) }}
                </div>
            @endif
        </div>
    </div>

    {{-- Endpoint info --}}
    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Endpoint для настройки в 1С:</div>
        <code class="text-sm font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $stats['endpoint'] }}</code>
    </div>

    {{-- Таблица логов --}}
    <div class="mb-2 text-lg font-semibold text-gray-950 dark:text-white">История обменов</div>
    {{ $this->table }}
</x-filament-panels::page>
