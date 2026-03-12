@extends('layouts.store', ['title' => $product->name, 'mainClass' => 'product'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Каталог', 'url' => route('shop.index')],
        $product->category ? ['title' => $product->category->name, 'url' => route('shop.category', $product->category->slug)] : null,
        ['title' => $product->name],
    ]])

    <div class="container">
        <div class="product__inner">
            {{-- Mobile-only info (hidden on desktop via CSS) --}}
            <div class="product__info-second">
                <p class="product__info-article">
                    @if ($product->category)
                        <span class="category-article">{{ $product->category->name }}</span>
                    @endif
                    @if ($product->sku)
                        <span class="article-card">Артикул: {{ $product->sku }}</span>
                    @endif
                </p>
                <h3 class="product-title">{{ $product->name }}</h3>
                @if ($product->short_description)
                    <p class="dopzagolovok">{{ $product->short_description }}</p>
                @endif
            </div>

            {{-- Gallery: thumbnail sidebar + main photo with fancybox --}}
            <div class="product__slider">
                @if ($product->images->isNotEmpty())
                    <div class="thumbnail-nav">
                        <button type="button" class="thumb-arrow thumb-arrow--up" aria-label="Прокрутить вверх">
                            <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 7L7 1L1 7" stroke="#404141" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="thumbnail-slider">
                            <div class="thumbnail">
                                @foreach ($product->images->reject(fn($img) => $img->isHorizontalVideo()) as $image)
                                    @if ($image->isVideo())
                                        <a href="#" class="thumb-video" data-video-url="{{ $image->video_url }}" data-fancybox="gallery">
                                            <img src="{{ $image->video_thumbnail ?: $image->path }}" alt="{{ $image->alt ?: $product->name }}" @if ($loop->first) class="active" @endif>
                                            <span class="thumb-play-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
                                            </span>
                                        </a>
                                    @else
                                        <a href="{{ $image->path }}" data-fancybox="gallery">
                                            <img src="{{ $image->path }}" alt="{{ $image->alt ?: $product->name }}" @if ($loop->first) class="active" @endif>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="thumb-arrow thumb-arrow--down" aria-label="Прокрутить вниз">
                            <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="#404141" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    @php $firstGalleryItem = $product->images->reject(fn($img) => $img->isHorizontalVideo())->first(); @endphp
                    <div class="main-photo">
                        @if ($firstGalleryItem && $firstGalleryItem->isVideo())
                            <video src="{{ $firstGalleryItem->video_url }}" autoplay muted playsinline preload="auto" loop class="main-photo__video"></video>
                        @elseif ($firstGalleryItem)
                            <a href="{{ $firstGalleryItem->path }}" data-fancybox="gallery">
                                <img src="{{ $firstGalleryItem->path }}" alt="{{ $firstGalleryItem->alt ?: $product->name }}">
                            </a>
                        @endif
                    </div>
                @else
                    <div style="height:500px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;color:#ccc;">Нет фото</div>
                @endif
            </div>

            {{-- Product info (desktop) --}}
            <div class="product__info">
                <p class="product__info-article">
                    @if ($product->category)
                        <span class="category-article">{{ $product->category->name }}</span>
                    @endif
                    @if ($product->sku)
                        <span class="article-card">Артикул: {{ $product->sku }}</span>
                    @endif
                </p>
                <h3 class="product-title">{{ $product->name }}</h3>
                @if ($product->short_description)
                    <p class="dopzagolovok">{{ $product->short_description }}</p>
                @endif

                <form method="POST" action="{{ route('cart.items.store') }}" class="cart" data-product-id="{{ $product->id }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" value="">

                    @php
                        $variationGroups = collect();
                        if ($product->relationLoaded('variants') && $product->variants->isNotEmpty()) {
                            $allAttrValues = $product->variants->flatMap(fn($v) => $v->attributeValues);
                            $variationGroups = $allAttrValues->groupBy(fn($av) => $av->pivot->product_attribute_id ?? $av->attribute->id);
                        }

                        // Group attributes by group_name for display
                        $displayGroups = collect();
                        $ungrouped = collect();
                        foreach ($variationGroups as $attrId => $values) {
                            $attr = $values->first()->attribute;
                            $uniqueValues = $values->unique('id')->sortBy('sort_order');
                            $item = ['attr' => $attr, 'values' => $uniqueValues];
                            if ($attr->group_name) {
                                $displayGroups->push($item);
                            } else {
                                $ungrouped->push($item);
                            }
                        }
                    @endphp

                    {{-- Ungrouped attributes (Цвет, Размер, etc.) --}}
                    @foreach ($ungrouped as $group)
                        <div class="product-choise">
                            <p class="title-attribute">
                                <span>{{ $group['attr']->name }}</span>
                                @if (in_array($group['attr']->slug, ['razmer', 'size']))
                                    <a href="javascript:void(0)" class="sizelist">Таблица размеров</a>
                                @endif
                            </p>
                            <input type="hidden" name="{{ $group['attr']->slug }}_value_id" value="">
                            <ul class="product-variation-attribute_pa_{{ $group['attr']->slug }}" data-attribute-name="{{ $group['attr']->slug }}_value_id">
                                @foreach ($group['values'] as $val)
                                    <li data-attribute-value="{{ $val->id }}" class="variation-option">
                                        <a class="variation-link" href="#" id="{{ $val->slug }}">{{ $val->value }}</a>
                                        <span>{{ $val->value }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    {{-- Grouped attributes (Комплектация → Класс защиты, Комплект быстрого доступа, etc.) --}}
                    @php $renderedGroups = collect(); @endphp
                    @foreach ($displayGroups as $group)
                        @if (!$renderedGroups->contains($group['attr']->group_name))
                            @php $renderedGroups->push($group['attr']->group_name); @endphp
                            <div class="product-choise">
                                <p class="title-attribute title-attribute--group">{{ $group['attr']->group_name }}</p>
                            </div>
                        @endif
                        <div class="product-choise">
                            <p class="title-attribute title-attribute--sub">{{ $group['attr']->name }}</p>
                            <input type="hidden" name="{{ $group['attr']->slug }}_value_id" value="">
                            <ul class="product-variation-attribute_pa_{{ $group['attr']->slug }}" data-attribute-name="{{ $group['attr']->slug }}_value_id">
                                @foreach ($group['values'] as $val)
                                    <li data-attribute-value="{{ $val->id }}" class="variation-option">
                                        <a class="variation-link" href="#" id="{{ $val->slug }}">{{ $val->value }}</a>
                                        <span>{{ $val->value }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    {{-- Desktop add to cart --}}
                    <div class="add-to-web">
                        <p>Цена:</p>
                        <p class="price">{{ number_format($product->price, 0, '', ' ') }} &#8381;</p>
                        <span class="total-price" style="display:none;"></span>
                        <div class="product__info__btns-wrapper">
                            <div class="product__info-counter">
                                <span class="product__info-counter__decrease">-</span>
                                <input type="number" class="qty" name="quantity" value="1" min="1">
                                <span class="product__info-counter__increase">+</span>
                            </div>
                            <button type="submit" class="product__info__btn single_add_to_cart_button button alt">
                                <span class="product__info__btn-text">В корзину</span>
                                <img src="{{ asset('wp-theme/images/icons/basket-white.svg') }}" alt="icon">
                            </button>
                        </div>
                    </div>

                    {{-- Mobile add to cart --}}
                    <div class="add-to-mob">
                        <p>Цена:</p>
                        <p class="price">{{ number_format($product->price, 0, '', ' ') }} &#8381;</p>
                        <span class="total-price" style="display:none;"></span>
                        <div class="product__info__btns-wrapper">
                            <div class="product__info-counter">
                                <span class="product__info-counter__decrease">-</span>
                                <input type="number" class="qty" name="quantity_mob" value="1" min="1">
                                <span class="product__info-counter__increase">+</span>
                            </div>
                            <button type="submit" class="product__info__btn single_add_to_cart_button button alt">
                                <span class="product__info__btn-text">В корзину</span>
                                <img src="{{ asset('wp-theme/images/icons/basket-white.svg') }}" alt="icon">
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Telegram ask question button --}}
                <a href="https://t.me/shieldandswordrus" target="_blank" class="ask-question-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.05 1.577c-.393-.016-.784.08-1.117.235-.484.186-4.92 1.902-9.41 3.64L2.694 8.81c-.544.214-.974.51-.974 1.012 0 .382.228.674.667.874l4.857 1.935c.293.12.614.04.87-.118l7.862-5.048c.234-.152.513.03.352.234L10.97 13.6a.596.596 0 00-.074.66l2.638 4.86c.255.474.59.67.91.67.32 0 .604-.195.835-.557l8.87-16.155c.34-.603.403-1.262.18-1.784-.222-.523-.698-.835-1.28-.717z" fill="currentColor"/>
                    </svg>
                    Задать вопрос по товару
                </a>

                {{-- Store availability --}}
                @if ($product->stores->isNotEmpty())
                    <div class="store-availability">
                        <button type="button" class="store-availability__toggle" onclick="this.parentElement.classList.toggle('open')">
                            <span>Наличие в магазинах</span>
                            <svg width="12" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L7 7L13 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="store-availability__list">
                            @foreach ($product->stores as $store)
                                <div class="store-availability__item">
                                    <span class="store-availability__dot {{ $store->pivot->in_stock ? 'store-availability__dot--in' : 'store-availability__dot--out' }}"></span>
                                    <div class="store-availability__info">
                                        <span class="store-availability__name">{{ $store->name }}</span>
                                        @if ($store->address)
                                            <span class="store-availability__address">{{ $store->address }}</span>
                                        @endif
                                    </div>
                                    <span class="store-availability__status">
                                        {{ $store->pivot->in_stock ? 'В наличии' : 'Нет в наличии' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <div class="categories tabsproduct">
            <a href="#tab_chars" class="navigation-categories-card active">Основные характеристики</a>
            <a href="#tab_desc" class="navigation-categories-card">Описание</a>
            <a href="#tab_info" class="navigation-categories-card">Информация</a>
        </div>

        <div id="tab_chars" class="tab-content">
            @if ($product->specifications->isNotEmpty())
                <h2>Характеристики</h2>
                <div>
                    @foreach ($product->specifications as $spec)
                        <p><span>{{ $spec->name }}:</span><b>{{ $spec->value }}</b></p>
                    @endforeach
                </div>
            @else
                <p>Характеристики не указаны.</p>
            @endif
        </div>

        <div id="tab_desc" class="tab-content" style="display:none;">
            @if ($product->description)
                {!! $product->description !!}
            @else
                <p>Описание отсутствует.</p>
            @endif
        </div>

        <div id="tab_info" class="tab-content" style="display:none;">
            <p>Информация о товаре.</p>
        </div>

        {{-- Horizontal videos section --}}
        @php $horizontalVideos = $product->images->filter(fn($img) => $img->isHorizontalVideo()); @endphp
        @if ($horizontalVideos->isNotEmpty())
            <div class="product-videos-section">
                <h2 class="product-videos-section__title">Видео</h2>
                @foreach ($horizontalVideos as $video)
                    <div class="product-video-player">
                        <video src="{{ $video->video_url }}"
                               controls
                               preload="metadata"
                               playsinline
                               muted
                               @if ($video->video_thumbnail) poster="{{ $video->video_thumbnail }}" @endif
                               class="product-video-player__video"
                               data-autoplay-scroll>
                        </video>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($related->isNotEmpty())
        <section class="catalog catalog-filter" style="margin-top: 80px;">
            <div class="container">
                <h2 class="catalog__title">Похожие товары</h2>
                <div class="cards">
                    @foreach ($related as $relatedProduct)
                        @include('store.partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
