/**
 * COLCHUGA Store Frontend JS
 * Matches WordPress "kolchuga" theme interaction patterns exactly
 */

import Swiper from 'swiper';
import { Navigation, Thumbs, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import 'swiper/css/pagination';

import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';

/* ---------- FancyBox Init ---------- */
Fancybox.bind('[data-fancybox]', {
    Toolbar: { display: ['close'] },
});

/* ---------- Toast notifications ---------- */
function showToast(message, type = 'success', options = {}) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;

    const text = document.createElement('span');
    text.textContent = message;
    toast.appendChild(text);

    if (options.linkUrl) {
        const link = document.createElement('a');
        link.href = options.linkUrl;
        link.className = 'toast__link';
        link.textContent = options.linkText || 'Перейти';
        toast.appendChild(link);
    }

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast--removing');
        toast.addEventListener('animationend', () => toast.remove());
    }, 4000);
}

function updateCartCounter(count) {
    const counter = document.querySelector('.basket-counter');
    const span = counter?.querySelector('span');
    if (counter && span) {
        span.textContent = count;
        counter.style.display = count > 0 ? '' : 'none';
    }
}

/* ---------- AJAX Add-to-Cart ---------- */
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        const form = e.target;

        // Match add-to-cart forms: product card forms and product page forms
        const isCardForm = form.querySelector('.card__btns');
        const isProductForm = form.closest('.product__info') && form.classList.contains('cart');

        if (!isCardForm && !isProductForm) return;

        // Only intercept if form action is cart.items.store
        if (!form.action || !form.action.includes('/cart/items')) return;

        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { throw data; });
            }
            return response.json();
        })
        .then(data => {
            showToast(data.message || 'Товар добавлен в корзину.', 'success', { linkUrl: '/cart', linkText: 'Перейти в корзину →' });
            updateCartCounter(data.cart_count);

            // Replace button with "Уже в корзине" for card forms
            if (isCardForm) {
                const btns = form.querySelector('.card__btns');
                if (btns) {
                    btns.className = 'card__btns card__btns--in-cart';
                    btns.innerHTML = '<a href="/cart" class="card__btns__btn--in-cart"><span class="card__btns__btn-text">Уже в корзине &#10003;</span></a>';
                }
            }
        })
        .catch(err => {
            const message = err?.message || err?.errors?.quantity?.[0] || err?.errors?.product_id?.[0] || 'Ошибка при добавлении в корзину.';
            showToast(message, 'error');
        });
    });
});

/* ---------- Catalog dropdown (hover with delay) ---------- */
const catalogWrapper = document.querySelector('.header__nav-catalog-wrapper') || document.querySelector('.header__catalog-wrapper');
if (catalogWrapper) {
    const catalogMenu = catalogWrapper.querySelector('.header__catalog-dropdown') || catalogWrapper.querySelector('.header__catalog__menu');
    if (catalogMenu) {
        let catalogHideTimeout = null;

        function showCatalogDropdown() {
            clearTimeout(catalogHideTimeout);
            catalogMenu.style.display = 'block';
        }

        function hideCatalogDropdown() {
            catalogHideTimeout = setTimeout(() => {
                catalogMenu.style.display = 'none';
            }, 200);
        }

        catalogWrapper.addEventListener('mouseenter', showCatalogDropdown);
        catalogWrapper.addEventListener('mouseleave', hideCatalogDropdown);
        catalogMenu.addEventListener('mouseenter', showCatalogDropdown);
        catalogMenu.addEventListener('mouseleave', hideCatalogDropdown);
    }
}

/* ---------- Search modal (WP theme: .open / ._active) ---------- */
const searchToggle = document.querySelector('.header__search') || document.querySelector('.header__bottom-right__search');
const searchModal = document.querySelector('.search-modal');
const searchModalCont = document.querySelector('.search-modal__container');
const searchBody = document.querySelector('.search-modal__body');
const searchClose = document.querySelector('.search-modal__close');

if (searchToggle && searchModal) {
    searchToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        searchModal.classList.add('open');
        setTimeout(() => {
            searchModalCont?.classList.add('_active');
        }, 0);
        document.querySelector('.search-input')?.focus();
    });

    searchClose?.addEventListener('click', function () {
        searchModalCont?.classList.remove('_active');
        setTimeout(() => {
            searchModal.classList.remove('open');
        }, 1000);
    });

    searchBody?.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    searchModal.addEventListener('click', function () {
        searchModalCont?.classList.remove('_active');
        setTimeout(() => {
            searchModal.classList.remove('open');
        }, 500);
    });

    /* Live AJAX search */
    const searchInput = document.querySelector('.search-modal .search-input');
    const searchResultDiv = document.querySelector('.search-result');
    let searchTimer = null;

    if (searchInput && searchResultDiv) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(searchTimer);

            if (q.length < 2) {
                searchResultDiv.innerHTML = '';
                return;
            }

            searchTimer = setTimeout(() => {
                fetch(`/api/search?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(products => {
                    if (!products.length) {
                        searchResultDiv.innerHTML = '<p style="text-align:center;color:#888;margin-top:20px;">Ничего не найдено</p>';
                        return;
                    }
                    searchResultDiv.innerHTML = products.map(p => `
                        <div class="card">
                            <div class="card__img">
                                <a href="${p.url}"><img src="${p.image}" alt="${p.name}"></a>
                            </div>
                            <div class="card__info">
                                <div class="card__title"><a href="${p.url}">${p.name}</a></div>
                                <div class="card__number">${p.sku || ''}</div>
                            </div>
                            <div class="card__price">
                                <span class="price">${p.price}</span>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(() => {
                    searchResultDiv.innerHTML = '';
                });
            }, 300);
        });
    }
}

/* ---------- Mobile burger menu: panel + accordion ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const menu = document.querySelector('.header__bottom-burger__menu');
    const overlay = document.querySelector('.burger-overlay');
    const btnBurger = document.querySelector('.header__burger') || document.querySelector('.header__bottom-burger');
    const btnClose = menu?.querySelector('.burger-menu__close');

    if (!menu) return;

    function openMenu() {
        overlay?.classList.add('active');
        document.documentElement.classList.add('burger-menu-open');
        document.body.classList.add('burger-menu-open');
    }

    function closeMenu() {
        overlay?.classList.remove('active');
        document.documentElement.classList.remove('burger-menu-open');
        document.body.classList.remove('burger-menu-open');
    }

    btnBurger?.addEventListener('click', function (e) {
        e.stopPropagation();
        openMenu();
    });
    btnClose?.addEventListener('click', function (e) {
        e.preventDefault();
        closeMenu();
    });
    overlay?.addEventListener('click', function (e) {
        e.stopPropagation();
        closeMenu();
    });

    // Also close when clicking on the shifted wrapper area (visible right of menu)
    document.querySelector('.wrapper')?.addEventListener('click', function (e) {
        if (document.body.classList.contains('burger-menu-open')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.body.classList.contains('burger-menu-open')) {
            closeMenu();
        }
    });

    // Accordion toggle for all expandable items
    menu.querySelectorAll('.burger-menu__toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const expandable = btn.closest('.burger-menu__item--expandable, .burger-menu__subitem--expandable');
            if (expandable) {
                expandable.classList.toggle('open');
            }
        });
    });
});

/* ---------- Contact modal (WP theme: .modal.open) ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const formBtns = document.querySelectorAll('.form-btn[data-modal="contact"]');
    const modal = document.querySelector('.modal');
    const modalClose = document.querySelector('.modal__close');

    if (formBtns.length && modal && modalClose) {
        formBtns.forEach(formBtn => {
            formBtn.addEventListener('click', function (e) {
                e.preventDefault();
                modal.classList.add('open');
            });
        });

        modalClose.addEventListener('click', function () {
            modal.classList.remove('open');
        });

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('open');
            }
        });
    }
});

/* ---------- Message modal ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const messageModalClose = document.querySelector('.message-modal__close');
    const messageModal = document.querySelector('.message-modal');

    messageModalClose?.addEventListener('click', function () {
        messageModal?.classList.remove('open');
    });
});

/* ---------- Contact form validation ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.modal .input');
    const btn = document.querySelector('.form__btn');

    if (inputs.length && btn) {
        function validateEmail(value) {
            const regEmail = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return regEmail.test(value.toLowerCase());
        }

        const inputHandler = () => {
            const name = document.querySelector('.modal input[name="name"]')?.value || '';
            const phone = document.querySelector('.modal input[name="phone"]')?.value || '';
            const email = document.querySelector('.modal input[name="email"]')?.value || '';

            if (name.length > 3 && phone.length > 5 && validateEmail(email)) {
                btn.disabled = false;
                btn.classList.add('btn--active');
            } else {
                btn.disabled = true;
                btn.classList.remove('btn--active');
            }
        };

        inputs.forEach(inp => inp.addEventListener('input', inputHandler));

        // Form submission: show message modal, close contact modal
        const contactForm = document.querySelector('.modal form');
        if (contactForm) {
            contactForm.addEventListener('submit', function (e) {
                // Let the form submit normally to the server
                // The server will redirect back with a flash message
            });
        }
    }
});

/* ---------- Product gallery: thumbnail click → main photo + arrow nav ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const thumbnails = document.querySelectorAll('.thumbnail a');
    const mainPhoto = document.querySelector('.main-photo img');
    const mainPhotoLink = document.querySelector('.main-photo a');

    if (thumbnails.length && mainPhoto) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function (e) {
                e.preventDefault();
                const img = this.querySelector('img');
                if (img && mainPhoto) {
                    mainPhoto.src = img.src;
                    if (mainPhotoLink) {
                        mainPhotoLink.href = this.href;
                    }
                    document.querySelectorAll('.thumbnail img').forEach(i => i.classList.remove('active'));
                    img.classList.add('active');
                }
            });
        });
    }

    /* Thumbnail arrow navigation */
    const slider = document.querySelector('.thumbnail-slider');
    const arrowUp = document.querySelector('.thumb-arrow--up');
    const arrowDown = document.querySelector('.thumb-arrow--down');

    if (slider && arrowUp && arrowDown) {
        const scrollStep = 85;
        arrowUp.addEventListener('click', () => {
            slider.scrollBy({ top: -scrollStep, behavior: 'smooth' });
        });
        arrowDown.addEventListener('click', () => {
            slider.scrollBy({ top: scrollStep, behavior: 'smooth' });
        });
    }
});

/* ---------- Product page: Quantity counter ---------- */
document.querySelectorAll('.product__info-counter').forEach(counter => {
    const decrease = counter.querySelector('.product__info-counter__decrease');
    const increase = counter.querySelector('.product__info-counter__increase');
    const input = counter.querySelector('.qty');

    if (decrease && increase && input) {
        decrease.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
        });
        increase.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            input.value = val + 1;
        });
    }
});

/* ---------- Product page: Variant selection ---------- */
document.querySelectorAll('.variation-option').forEach(option => {
    option.addEventListener('click', (e) => {
        e.preventDefault();
        const list = option.closest('ul, .product-variation-list');
        if (list) {
            list.querySelectorAll('.variation-link').forEach(l => l.classList.remove('active'));
        }
        option.querySelector('.variation-link')?.classList.add('active');

        const value = option.dataset.attributeValue;
        const attrName = list?.dataset?.attributeName;
        if (attrName) {
            const hidden = document.querySelector(`input[name="${attrName}"]`);
            if (hidden) hidden.value = value;
        }

        fetchVariantPrice();
    });
});

async function fetchVariantPrice() {
    const productId = document.querySelector('input[name="product_id"]')?.value;
    if (!productId) return;

    const params = new URLSearchParams();
    document.querySelectorAll('.product-choise input[type="hidden"][name$="_value_id"]').forEach(input => {
        if (input.value) params.append('attributes[]', input.value);
    });

    if (!params.has('attributes[]')) return;

    try {
        const response = await fetch(`/api/products/${productId}/variants?${params}`);
        const data = await response.json();

        if (data.found && data.variant_id) {
            const variantIdInput = document.querySelector('input[name="variant_id"]');
            if (variantIdInput) variantIdInput.value = data.variant_id;

            document.querySelectorAll('.add-to-web .price, .add-to-mob .price').forEach(el => {
                el.textContent = data.price_formatted;
            });

            const oldPriceEls = document.querySelectorAll('.add-to-web .old-price, .add-to-mob .old-price');
            if (data.old_price_formatted) {
                oldPriceEls.forEach(el => { el.textContent = data.old_price_formatted; el.style.display = ''; });
                if (oldPriceEls.length === 0) {
                    const priceEl = document.querySelector('.add-to-web .price');
                    if (priceEl) {
                        const oldEl = document.createElement('span');
                        oldEl.className = 'old-price';
                        oldEl.textContent = data.old_price_formatted;
                        priceEl.parentNode.insertBefore(oldEl, priceEl);
                    }
                }
            } else {
                oldPriceEls.forEach(el => { el.style.display = 'none'; });
            }

            const skuEl = document.querySelector('.article-card');
            if (skuEl && data.sku) skuEl.textContent = 'Артикул: ' + data.sku;

            document.querySelectorAll('.product__info__btn').forEach(addBtn => {
                addBtn.disabled = !data.in_stock;
                const textEl = addBtn.querySelector('.product__info__btn-text');
                if (textEl) {
                    textEl.textContent = data.in_stock ? 'В корзину' : 'Нет в наличии';
                }
            });
        }
    } catch (err) {
        console.error('Failed to fetch variant:', err);
    }
}

/* ---------- Product page: Auto-select first variant values on load ---------- */
(function () {
    const variationLists = document.querySelectorAll('.product-choise ul[data-attribute-name]');
    if (!variationLists.length) return;

    variationLists.forEach(list => {
        const firstOption = list.querySelector('.variation-option');
        if (!firstOption) return;

        // Mark first option as active
        firstOption.querySelector('.variation-link')?.classList.add('active');

        // Set hidden input value
        const attrName = list.dataset.attributeName;
        const value = firstOption.dataset.attributeValue;
        if (attrName && value) {
            const hidden = document.querySelector(`input[name="${attrName}"]`);
            if (hidden) hidden.value = value;
        }
    });

    // Fetch variant price with pre-selected values
    fetchVariantPrice();
})();

/* ---------- Product tabs (3 tabs: chars, desc, info) ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const productTabs = document.querySelectorAll('.navigation-categories-card');
    const tabContents = document.querySelectorAll('.tab-content');

    productTabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            if (!this.classList.contains('active')) {
                productTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const target = this.getAttribute('href');
                tabContents.forEach(content => {
                    content.style.display = content.id === target.replace('#', '') ? 'block' : 'none';
                });
            }
        });
    });
});

/* ---------- Card image slider (mouse position based) ---------- */
document.querySelectorAll('.card__img').forEach(cardImg => {
    const slides = cardImg.querySelectorAll('.card__img-slide');
    const dots = cardImg.querySelectorAll('.card__img-dot');
    if (slides.length <= 1) return;

    let currentIndex = 0;

    function showSlide(index) {
        if (index === currentIndex) return;
        slides[currentIndex].classList.remove('active');
        dots[currentIndex]?.classList.remove('active');
        currentIndex = index;
        slides[currentIndex].classList.add('active');
        dots[currentIndex]?.classList.add('active');
    }

    cardImg.addEventListener('mousemove', function (e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const percent = x / rect.width;
        const index = Math.min(Math.floor(percent * slides.length), slides.length - 1);
        showSlide(index);
    });

    cardImg.addEventListener('mouseleave', function () {
        showSlide(0);
    });
});

/* ---------- Product card counter (catalog cards) ---------- */
document.querySelectorAll('.card__btns__counter').forEach(counter => {
    const decrease = counter.querySelector('.card__btns__counter-decrease');
    const increase = counter.querySelector('.card__btns__counter-increase');
    const input = counter.querySelector('.qty');

    if (decrease && increase && input) {
        decrease.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            let val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
        });
        increase.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            let val = parseInt(input.value) || 1;
            input.value = val + 1;
        });
    }
});

/* ---------- Custom select dropdown (catalog sort) ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const selectWrapper = document.querySelector('.custom-select-wrapper');
    if (!selectWrapper) return;

    const select = selectWrapper.querySelector('.custom-select');
    const trigger = selectWrapper.querySelector('.custom-select__trigger');
    const options = selectWrapper.querySelectorAll('.custom-option');

    trigger?.addEventListener('click', function () {
        select?.classList.toggle('open');
    });

    options.forEach(option => {
        option.addEventListener('click', function () {
            const value = this.dataset.value;
            trigger.querySelector('span').textContent = this.textContent;
            select?.classList.remove('open');

            // Update URL with sort parameter
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set('sort', value);
            } else {
                url.searchParams.delete('sort');
            }
            window.location = url.toString();
        });
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!selectWrapper.contains(e.target)) {
            select?.classList.remove('open');
        }
    });
});

/* ---------- Catalog filter button (mobile sidebar toggle) ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const btnFilter = document.querySelector('.catalog-filter__btn');
    const sidebar = document.querySelector('.sidebar');

    if (btnFilter) {
        btnFilter.addEventListener('click', function () {
            sidebar?.classList.toggle('open');
        });
    }
});

/* ---------- Footer accordion (mobile) ---------- */
const accBtns = document.querySelectorAll('.accordion-item__btn');
if (accBtns) {
    accBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            this.classList.toggle('active');
            const btnRight = this.querySelector('.accordion-item__btn-icon-right');
            const btnUp = this.querySelector('.accordion-item__btn-icon-up');
            if (this.classList && this.classList.contains('active')) {
                if (btnRight) btnRight.style.cssText = 'display: none';
                if (btnUp) btnUp.style.cssText = 'display: block';
            } else {
                if (btnRight) btnRight.style.cssText = 'display: block';
                if (btnUp) btnUp.style.cssText = 'display: none';
            }

            const list = this.nextElementSibling;
            if (list) {
                if (list.style.maxHeight) {
                    list.style.maxHeight = null;
                } else {
                    list.style.maxHeight = list.scrollHeight + 'px';
                }
            }
        });
    });
}

/* ---------- Checkout: Shipping method toggle ---------- */
/* Shipping method toggle is now handled inline in checkout.blade.php */

/* ---------- Cart: Quantity buttons ---------- */
document.querySelectorAll('.basket-product__btns__counter').forEach(counter => {
    const decrease = counter.querySelector('.basket-product__btns__counter-decrease');
    const increase = counter.querySelector('.basket-product__btns__counter-increase');
    const input = counter.querySelector('.qty');
    const form = counter.querySelector('form');

    if (decrease && increase && input) {
        decrease.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val > 1) {
                input.value = val - 1;
                form?.submit();
            }
        });
        increase.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            input.value = val + 1;
            form?.submit();
        });
    }
});

/* ---------- Cart: Coupon sidebar – show/hide "Применить" button on input ---------- */
document.querySelectorAll('.totals .coupon input[name="coupon_code"]').forEach(input => {
    const btn = input.closest('.coupon')?.querySelector('button');
    if (!btn) return;
    input.addEventListener('input', function () {
        if (this.value.trim().length > 0) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
});

/* ---------- Cart: Coupon apply buttons ---------- */
document.querySelectorAll('.coupon-apply-btn, .coupon-apply-btn-sidebar').forEach(btn => {
    btn.addEventListener('click', function () {
        const couponWrapper = this.closest('.coupon');
        const couponInput = couponWrapper?.querySelector('input[name="coupon_code"]');
        if (!couponInput || !couponInput.value.trim()) return;

        const url = this.dataset.url;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // Reset previous states
        couponWrapper?.classList.remove('success', 'fail', 'exist');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ coupon_code: couponInput.value.trim() }),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok) {
                couponWrapper?.classList.add('success');
                setTimeout(() => location.reload(), 800);
            } else {
                const msg = data?.errors?.coupon_code?.[0] || '';
                if (msg.includes('уже') || msg.includes('применен')) {
                    couponWrapper?.classList.add('exist');
                } else {
                    couponWrapper?.classList.add('fail');
                }
            }
        })
        .catch(() => {
            couponWrapper?.classList.add('fail');
        });
    });
});

