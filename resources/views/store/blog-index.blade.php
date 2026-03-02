@extends('layouts.store', ['title' => 'Наш блог', 'mainClass' => 'blog-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => 'Блог']]])

    <section class="blog-section">
        <div class="container">
            <h2 class="blog-section__title">Блог, последние новости</h2>

            @if ($posts->isNotEmpty())
                <div class="blog-section__cards">
                    @foreach ($posts as $post)
                        <div class="blog-section__card">
                            @if ($post->featured_image)
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                            @else
                                <div style="height:200px;display:flex;align-items:center;justify-content:center;background:#ebeaf0;color:#807f81;">Нет фото</div>
                            @endif
                            <div class="blog-section__card-info">
                                <h5>{{ $post->title }}</h5>
                                @if ($post->excerpt)
                                    <p>{{ $post->excerpt }}</p>
                                @endif
                                <a href="{{ route('blog.show', $post->slug) }}" class="blog-section__card-info__btn">Читать</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination">
                    {{ $posts->links() }}
                </div>
            @else
                <p style="text-align:center;padding:40px;color:#807f81;">Посты не найдены.</p>
            @endif
        </div>
    </section>
@endsection
