<div class="sidebar">
    <form id="filter_form" action="{{ url()->current() }}" method="GET">
        @if (request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
        @endif
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif

        <h3 class="active">Цена</h3>
        <div>
            <p class="range">
                <input type="number" value="{{ request('price_min', 0) }}" name="price_min"> -
                <input type="number" value="{{ request('price_max', 1000000) }}" name="price_max">
            </p>
        </div>

        @foreach ($attributes ?? [] as $attribute)
            @if ($attribute->values->isNotEmpty())
                <h3 class="active">{{ $attribute->name }}
                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L5 5L9 1" stroke="#404141" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </h3>
                <div>
                    @php
                        $paramName = $attribute->slug === 'color' ? 'color' : ($attribute->slug === 'razmer' ? 'size' : $attribute->slug);
                        $selectedValues = (array) request($paramName, []);
                    @endphp
                    @foreach ($attribute->values as $value)
                        <p class="checkbox">
                            <input type="checkbox" name="{{ $paramName }}[]" id="attr_{{ $value->id }}" value="{{ $value->id }}"
                                {{ in_array($value->id, $selectedValues) ? 'checked' : '' }}>
                            <label for="attr_{{ $value->id }}">{{ $value->value }}</label>
                        </p>
                    @endforeach
                </div>
            @endif
        @endforeach

        <button type="submit" class="form-btn">Применить</button>
    </form>
</div>
