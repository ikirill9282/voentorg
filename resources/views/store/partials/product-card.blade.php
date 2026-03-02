<div class="card">
    <div>
        <div class="card__img">
            <a href="{{ route('shop.product', $product->slug) }}">
                @if ($product->images->isNotEmpty())
                    @foreach ($product->images as $img)
                        <img src="{{ $img->path }}" alt="{{ $img->alt ?: $product->name }}" class="card__img-slide {{ $loop->first ? 'active' : '' }}">
                    @endforeach
                    @if ($product->images->count() > 1)
                        <div class="card__img-dots">
                            @foreach ($product->images as $img)
                                <span class="card__img-dot {{ $loop->first ? 'active' : '' }}"></span>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div style="height:200px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;color:#ccc;">Нет фото</div>
                @endif
            </a>
        </div>
        <span class="card__number">Артикул: {{ $product->sku }}</span>
        <h5 class="card__title">
            <a href="{{ route('shop.product', $product->slug) }}">{{ $product->name }}</a>
        </h5>
        <p class="price">{{ number_format($product->price, 0, '', ' ') }} &#8381;</p>
    </div>
    <form class="cart" action="{{ route('cart.items.store') }}" method="POST">
        @csrf
        <div class="card__btns">
            <div class="try">
                <button type="submit" class="card__btns__btn single_add_to_cart_button button alt">
                    <span class="card__btns__btn-text">В корзину</span>
                    <svg width="46" height="42" viewBox="0 0 46 42" xmlns="http://www.w3.org/2000/svg">
                        <path d="M45.9726 9.97285L43.3748 23.4786C43.1605 24.5943 42.5402 25.6033 41.6219 26.3301C40.7037 27.0568 39.5456 27.4551 38.3495 27.4556H13.6491L14.586 32.3007H37.4765C38.4872 32.3007 39.4753 32.5849 40.3157 33.1173C41.1561 33.6497 41.8111 34.4064 42.1979 35.2917C42.5847 36.177 42.6859 37.1512 42.4887 38.0911C42.2915 39.0309 41.8048 39.8943 41.0901 40.5719C40.3754 41.2495 39.4648 41.7109 38.4735 41.8979C37.4822 42.0848 36.4546 41.9889 35.5208 41.6222C34.587 41.2554 33.7889 40.6344 33.2273 39.8377C32.6658 39.0409 32.3661 38.1041 32.3661 37.1459C32.3662 36.5952 32.4671 36.0488 32.6642 35.5308H20.1436C20.3407 36.0488 20.4415 36.5952 20.4417 37.1459C20.4433 37.9276 20.2453 38.698 19.8646 39.3915C19.484 40.0849 18.9321 40.6808 18.2559 41.1282C17.5797 41.5755 16.7994 41.8611 15.9816 41.9606C15.1638 42.0601 14.3327 41.9705 13.5593 41.6994C12.786 41.4284 12.0932 40.9839 11.5403 40.404C10.9874 39.8241 10.5906 39.126 10.384 38.3692C10.1773 37.6125 10.1669 36.8195 10.3535 36.0581C10.5402 35.2966 10.9184 34.5893 11.4559 33.9965L5.55759 3.23007H1.70348C1.25169 3.23007 0.8184 3.05992 0.498937 2.75704C0.179473 2.45416 0 2.04337 0 1.61504C0 1.1867 0.179473 0.775912 0.498937 0.473033C0.8184 0.170155 1.25169 0 1.70348 0H5.55759C6.35277 0.00164915 7.1223 0.266953 7.73286 0.74995C8.34341 1.23295 8.75654 1.90321 8.90066 2.64462L9.94404 8.07519H44.2904C44.5391 8.07567 44.7846 8.12844 45.0092 8.22967C45.2338 8.3309 45.4319 8.47809 45.5893 8.66064C45.754 8.83745 45.8741 9.04749 45.9405 9.27484C46.0069 9.50219 46.0179 9.74089 45.9726 9.97285Z"/>
                    </svg>
                </button>
            </div>
            <div class="card__btns__counter">
                <span class="card__btns__counter-decrease">-</span>
                <div class="quantity">
                    <input type="number" class="input-text qty text" step="1" min="1" name="quantity" value="1">
                </div>
                <span class="card__btns__counter-increase">+</span>
            </div>
            <input type="hidden" name="product_id" value="{{ $product->id }}">
        </div>
    </form>
</div>
