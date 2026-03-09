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
                @foreach ($products as $index => $product)
                    @if (!empty($bannersByPosition[$index]))
                        @foreach ($bannersByPosition[$index] as $banner)
                            <div class="catalog-banner catalog-banner--{{ $banner->display_mode }}">
                                @if (count($banner->images) > 1)
                                    <div class="swiper catalog-banner__swiper">
                                        <div class="swiper-wrapper">
                                            @foreach ($banner->images as $slide)
                                                <div class="swiper-slide">
                                                    @if ($banner->link_url)
                                                        <a href="{{ $banner->link_url }}">
                                                            <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] ?? $banner->title }}">
                                                        </a>
                                                    @else
                                                        <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] ?? $banner->title }}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                @elseif (count($banner->images) === 1)
                                    @if ($banner->link_url)
                                        <a href="{{ $banner->link_url }}">
                                            <img src="{{ $banner->images[0]['url'] }}" alt="{{ $banner->images[0]['alt'] ?? $banner->title }}">
                                        </a>
                                    @else
                                        <img src="{{ $banner->images[0]['url'] }}" alt="{{ $banner->images[0]['alt'] ?? $banner->title }}">
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @endif
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="pagination">
                {{ $products->links() }}
            </div>
        @else
            <p style="text-align:center;padding:40px;color:#807f81;">Товары не найдены.</p>
        @endif

        {{-- RuTube banner --}}
        <div class="section-footer-banner">
            <div class="section-footer-banner__content">
                <h3 class="section-footer-banner__title">Смотрите наши обзоры</h3>
                <p class="section-footer-banner__subtitle">Обзоры, тесты и рекомендации по экипировке</p>
                <div class="section-footer-banner__links">
                    <a href="https://rutube.ru/" class="section-footer-banner__link" target="_blank" rel="noopener">RuTube</a>
                </div>
            </div>
        </div>
    </div>
@endsection
