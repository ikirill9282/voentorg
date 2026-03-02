@extends('layouts.store', ['title' => $post->title, 'mainClass' => 'blog-page'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Наш блог', 'url' => route('blog.index')],
        ['title' => $post->title],
    ]])

    <section class="blog-page__wrapper">
        <div class="container">
            <h2 class="blog-page__title">{{ $post->title }}</h2>

            @if ($post->featured_image)
                <div class="blog-page__img">
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                </div>
            @endif

            @if (str_contains($post->content, '<p'))
                {!! $post->content !!}
            @else
                @foreach (preg_split('/\n{2,}/', trim($post->content)) as $paragraph)
                    <p>{!! nl2br(trim($paragraph)) !!}</p>
                @endforeach
            @endif
        </div>
    </section>

    @if ($relatedPosts->count())
        <section class="blog-section">
            <div class="container">
                <div class="blog-section__cards">
                    @foreach ($relatedPosts as $related)
                        <div class="blog-section__card">
                            @if ($related->featured_image)
                                <img class="blog-section__card-img" src="{{ $related->featured_image }}" alt="{{ $related->title }}">
                            @endif
                            <div class="blog-section__card-info">
                                <h5>{{ $related->title }}</h5>
                                <p>{{ Str::limit(strip_tags($related->content), 200) }}</p>
                                <a class="blog-section__card-info__btn" href="{{ route('blog.show', $related->slug) }}">Читать</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
