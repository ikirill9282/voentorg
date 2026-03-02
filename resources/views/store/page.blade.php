@extends('layouts.store', ['title' => $page->title, 'mainClass' => 'basket'])

@section('content')
    @include('store.partials.breadcrumbs', ['breadcrumbs' => [['title' => $page->title]]])

    <section class="page__wrapper the_page">
        <div class="container">
            <h2 class="basket__title">{{ $page->title }}</h2>
            <section class="policy__wrapper">
                {!! $page->content !!}
            </section>
        </div>
    </section>
@endsection
