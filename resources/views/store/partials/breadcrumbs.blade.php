<nav class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('store.home') }}">Главная</a></li>
            @foreach ($breadcrumbs ?? [] as $crumb)
                @if (is_null($crumb)) @continue @endif
                @if ($crumb['url'] ?? false)
                    <li><a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a></li>
                @else
                    <li>{{ $crumb['title'] }}</li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>
