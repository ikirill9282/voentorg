@extends('layouts.store', ['title' => $title ?? 'Каталог', 'mainClass' => 'catalog-filter'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => $category
        ? [['title' => 'Каталог', 'url' => route('shop.index')], ['title' => $category->name]]
        : [['title' => 'Каталог']],
    ])

    <div class="container">
        {{-- Sort dropdown --}}
        <div class="custom-select-wrapper" style="margin-bottom: 15px;">
            <div class="custom-select">
                <div class="custom-select__trigger">
                    <span>
                        @php
                            $sortLabels = [
                                '' => 'Сортировка по умолчанию',
                                'price_asc' => 'По возрастанию цены',
                                'price_desc' => 'По убыванию цены',
                                'name_asc' => 'По названию',
                                'newest' => 'По новинке',
                            ];
                            $currentSort = request('sort', '');
                        @endphp
                        {{ $sortLabels[$currentSort] ?? 'Сортировка по умолчанию' }}
                    </span>
                    <div class="arrow"></div>
                </div>
                <div class="custom-options">
                    @foreach ($sortLabels as $sortValue => $sortLabel)
                        <span class="custom-option {{ $currentSort === $sortValue ? 'selected' : '' }}" data-value="{{ $sortValue }}">{{ $sortLabel }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Category navigation links --}}
        <div class="categories">
            @foreach ($categories as $cat)
                <a href="{{ route('shop.category', $cat->slug) }}"
                   class="navigation-categories {{ $category && $category->id === $cat->id ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if (!empty($subcategories) && $subcategories->isNotEmpty())
            <div class="categories">
                @foreach ($subcategories as $subcat)
                    <a href="{{ route('shop.category', $subcat->slug) }}"
                       class="navigation-categories">
                        {{ $subcat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($products->isNotEmpty())
            <div class="cards">
                @foreach ($products as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="pagination">
                {{ $products->links() }}
            </div>
        @else
            <p style="text-align:center;padding:40px;color:#807f81;">Товары не найдены.</p>
        @endif
    </div>
@endsection
