@extends('layouts.store', ['title' => 'COLCHUGA — Снаряжение, которое работает', 'mainClass' => ''])

@section('content')
    {{-- 1. Hero section --}}
    <section class="top">
        <div class="container">
            <div class="top__wrapper">
                <div class="top__inner">
                    <div class="top__image">
                        <img src="{{ asset('legacy/assets/colchuga.ru/wp-content/uploads/2025/02/1-scaled.jpg') }}" alt="gazelle">
                    </div>
                    <div class="top__content">
                        <img class="top__content-img" src="{{ asset('legacy/assets/colchuga.ru/wp-content/uploads/2024/12/group-32.png') }}">
                        <h1 class="top__content-title">СНАРЯЖЕНИЕ, <br> КОТОРОЕ РАБОТАЕТ</h1>
                        <p class="top__content-text">Произведено в России</p>
                        <a href="{{ route('shop.index') }}" class="top__content-btn">
                            <span>Перейти в каталог</span>
                            <img src="{{ asset('legacy/assets/colchuga.ru/wp-content/uploads/2024/12/image-81-traced.png') }}" alt="icon">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Catalog section --}}
    <section class="catalog">
        <div class="container">
            <a href="{{ route('shop.index') }}"><h2 class="catalog__title"><span>Основной</span> каталог</h2></a>
            <div class="catalog__cards">
                @foreach ($categories as $category)
                    <div class="catalog__card-wrapper">
                        @if ($category->children->isNotEmpty())
                            {{-- Категория с подкатегориями: клик = toggle --}}
                            <div class="catalog__card catalog__card--has-children"
                                 data-category-id="{{ $category->id }}">
                                @if ($category->image)
                                    <img class="catalog__card-img"
                                         src="{{ asset($category->image) }}"
                                         alt="{{ $category->name }}">
                                @endif
                                <h5>{{ $category->name }}</h5>
                            </div>
                            {{-- Панель подкатегорий (скрыта) --}}
                            <div class="catalog__subcategories" id="subcats-{{ $category->id }}">
                                @foreach ($category->children as $child)
                                    <a href="{{ route('shop.category', $child->slug) }}"
                                       class="catalog__subcard">
                                        @if ($child->image)
                                            <img class="catalog__subcard-img"
                                                 src="{{ asset($child->image) }}"
                                                 alt="{{ $child->name }}">
                                        @endif
                                        <h6>{{ $child->name }}</h6>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            {{-- Обычная категория: ссылка напрямую --}}
                            <a href="{{ route('shop.category', $category->slug) }}"
                               class="catalog__card">
                                @if ($category->image)
                                    <img class="catalog__card-img"
                                         src="{{ asset($category->image) }}"
                                         alt="{{ $category->name }}">
                                @endif
                                <h5>{{ $category->name }}</h5>
                            </a>
                        @endif
                    </div>
                @endforeach

                {{-- "Смотреть все" — всегда последняя --}}
                <div class="catalog__card-wrapper">
                    <a href="{{ route('shop.index') }}" class="catalog__card catalog__card--viewall">
                        <h5>Смотреть все</h5>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 2.5 Популярное --}}
    @if ($popularProducts->isNotEmpty())
    <section class="popular">
        <div class="container">
            <h2 class="popular__title">Популярное</h2>
            <div class="popular__cards">
                @foreach ($popularProducts as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 3. About section --}}
    <section class="about">
        <div class="container">
            <div class="about__inner containerAbout">
                <div class="about__info">
                    <h2>О компании</h2>
                    <p>Бренд «Colchuga» является подразделением группы компаний «Щит и Меч». Начало специальной военной операции подтолкнуло нашу команду к организации собственного производства тактической экипировки.</p>
                    <p>С 2022 года мы разрабатываем и производим средства индивидуальной защиты, разгрузочные системы, тактическую одежду и многие другие элементы снаряжения. Нашей основной задачей является создание современного продукта, обладающего наилучшим качеством, износостойкостью, удобством и одновременно дешевизной. Десятки тысяч отправленных изделий, профессиональный подход, опыт и доступность наших продуктов делают нас объективными лидерами на рынке.</p>
                </div>
                <div class="about__progress">
                    <div class="about__video-wrapper">
                        <!-- TODO: заменить VIDEO_ID на реальный ID видео -->
                        <iframe src="https://www.youtube.com/embed/VIDEO_ID" title="О компании COLCHUGA" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. YouTube / Video showcase (Agilite-style) --}}
    <section class="video-showcase">
        {{-- Main: video left + info right --}}
        <div class="video-showcase__hero">
            <div class="video-showcase__player">
                <!-- TODO: заменить VIDEO_ID -->
                <iframe src="https://www.youtube.com/embed/VIDEO_ID" title="COLCHUGA" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="video-showcase__info">
                {{-- YouTube icon --}}
                <svg class="video-showcase__yt-icon" viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg">
                    <path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/>
                    <path d="M45 24L27 14v20" fill="#fff"/>
                </svg>
                <div class="video-showcase__stats">
                    <span class="video-showcase__count">Подписывайтесь</span>
                    <span class="video-showcase__label">на наш канал</span>
                </div>
                <a href="#" target="_blank" rel="noopener" class="video-showcase__btn">Смотреть</a>
            </div>
        </div>

        {{-- Video thumbnails row --}}
        <div class="video-showcase__thumbs">
            <a href="#" target="_blank" rel="noopener" class="video-showcase__thumb">
                <img src="{{ asset('legacy/assets/colchuga.ru/wp-content/uploads/2025/03/1.jpg') }}" alt="Видео 1">
                <div class="video-showcase__thumb-play">
                    <svg viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/><path d="M45 24L27 14v20" fill="#fff"/></svg>
                </div>
            </a>
            <a href="#" target="_blank" rel="noopener" class="video-showcase__thumb">
                <img src="{{ asset('legacy/assets/colchuga.ru/wp-content/uploads/2025/03/2.jpg') }}" alt="Видео 2">
                <div class="video-showcase__thumb-play">
                    <svg viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/><path d="M45 24L27 14v20" fill="#fff"/></svg>
                </div>
            </a>
            <a href="#" target="_blank" rel="noopener" class="video-showcase__thumb">
                <img src="{{ asset('legacy/assets/colchuga.ru/wp-content/uploads/2025/03/3.jpg') }}" alt="Видео 3">
                <div class="video-showcase__thumb-play">
                    <svg viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/><path d="M45 24L27 14v20" fill="#fff"/></svg>
                </div>
            </a>
        </div>
    </section>

    {{-- 5. Магазин в Ростове — full-width banner --}}
    <section class="home-banners">
        <a href="{{ route('page.contacts') }}" class="home-banners__item home-banners__item--placeholder">
            <div class="home-banners__overlay">
                <h3 class="home-banners__title">Магазин в Ростове</h3>
            </div>
        </a>
    </section>

    {{-- 5. Blog section --}}
    <section class="blog-section">
        <div class="container">
            <h2 class="blog-section__title">Новости</h2>
            <div class="blog-section__cards">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="blog-section__card">
                        @if ($post->featured_image)
                            <img class="blog-section__card-img" src="{{ $post->featured_image }}" alt="photo">
                        @endif
                        <div class="blog-section__card-info">
                            <h5>{{ $post->title }}</h5>
                            @if ($post->excerpt)
                                <p>{{ $post->excerpt }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
