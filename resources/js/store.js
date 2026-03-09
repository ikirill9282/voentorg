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

/* ---------- Product gallery: thumbnail click → main photo + arrow nav + hover zoom ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const thumbnails = document.querySelectorAll('.thumbnail a');
    const mainPhoto = document.querySelector('.main-photo img');
    const mainPhotoLink = document.querySelector('.main-photo a');
    const mainPhotoContainer = document.querySelector('.main-photo');

    if (thumbnails.length && mainPhotoContainer) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function (e) {
                e.preventDefault();
                const img = this.querySelector('img');
                document.querySelectorAll('.thumbnail img').forEach(i => i.classList.remove('active'));
                if (img) img.classList.add('active');

                // Check if this is a video thumbnail
                const videoUrl = this.dataset.videoUrl;
                if (videoUrl) {
                    // Replace main-photo content with video
                    mainPhotoContainer.innerHTML = `<video src="${videoUrl}" autoplay muted playsinline preload="auto" loop class="main-photo__video"></video>`;
                } else {
                    // Replace with image (restore fancybox link)
                    const src = img ? img.src : this.href;
                    const href = this.href;
                    mainPhotoContainer.innerHTML = `<a href="${href}" data-fancybox="gallery"><img src="${src}" alt=""></a>`;
                }
            });
        });
    }

    /* Desktop hover-zoom on main photo (works with dynamically replaced images) */
    if (mainPhotoContainer && window.innerWidth > 768) {
        mainPhotoContainer.addEventListener('mousemove', function (e) {
            const img = mainPhotoContainer.querySelector('img');
            if (!img) return; // skip zoom for video
            const rect = mainPhotoContainer.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            img.style.transformOrigin = `${x}% ${y}%`;
            img.style.transform = 'scale(2)';
        });
        mainPhotoContainer.addEventListener('mouseleave', function () {
            const img = mainPhotoContainer.querySelector('img');
            if (!img) return;
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center center';
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

    /* Mobile: Swiper carousel for product gallery */
    if (window.innerWidth <= 768 && document.querySelector('.product__slider')) {
        initMobileGallerySwiper();
    }
});

function initMobileGallerySwiper() {
    const slider = document.querySelector('.product__slider');
    if (!slider || slider.dataset.swiperInit) return;

    const thumbnailNav = slider.querySelector('.thumbnail-nav');
    const mainPhoto = slider.querySelector('.main-photo');
    if (!thumbnailNav || !mainPhoto) return;

    // Collect all media from thumbnails
    const mediaItems = [];
    slider.querySelectorAll('.thumbnail a').forEach(a => {
        const img = a.querySelector('img');
        const videoUrl = a.dataset.videoUrl;
        if (videoUrl) {
            mediaItems.push({ type: 'video', videoUrl, thumb: img?.src || '', alt: img?.alt || '' });
        } else if (img) {
            mediaItems.push({ type: 'image', src: img.src, href: a.href, alt: img.alt });
        }
    });
    if (mediaItems.length === 0) return;

    // Hide original elements
    thumbnailNav.style.display = 'none';
    mainPhoto.style.display = 'none';

    // Build Swiper HTML
    const swiperContainer = document.createElement('div');
    swiperContainer.className = 'swiper product-gallery-swiper';
    swiperContainer.innerHTML = `
        <div class="swiper-wrapper">
            ${mediaItems.map(item => {
                if (item.type === 'video') {
                    return `<div class="swiper-slide"><video src="${item.videoUrl}" autoplay muted playsinline preload="auto" loop style="width:100%;height:auto;display:block;"></video></div>`;
                }
                return `<div class="swiper-slide"><a href="${item.href}" data-fancybox="gallery-mobile"><img src="${item.src}" alt="${item.alt}"></a></div>`;
            }).join('')}
        </div>
        <div class="swiper-pagination"></div>
    `;
    slider.appendChild(swiperContainer);

    // Init Swiper
    new Swiper('.product-gallery-swiper', {
        modules: [Pagination],
        slidesPerView: 1,
        spaceBetween: 0,
        pagination: {
            el: '.product-gallery-swiper .swiper-pagination',
            clickable: true,
        },
    });

    // FancyBox for mobile with white background
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind('[data-fancybox="gallery-mobile"]', {
            Backdrop: { background: '#fff' },
            Toolbar: { display: { left: [], middle: [], right: ['close'] } },
        });
    }

    slider.dataset.swiperInit = 'true';
}

/* ---------- Horizontal video: autoplay on scroll ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const videos = document.querySelectorAll('[data-autoplay-scroll]');
    if (!videos.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.play().catch(() => {});
            } else {
                entry.target.pause();
            }
        });
    }, { threshold: 0.5 });

    videos.forEach(video => observer.observe(video));
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
            if (typeof updateTotalPrice === 'function') updateTotalPrice();
        });
        increase.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            input.value = val + 1;
            if (typeof updateTotalPrice === 'function') updateTotalPrice();
        });
        input.addEventListener('change', () => {
            if (typeof updateTotalPrice === 'function') updateTotalPrice();
        });
    }
});

/* ---------- Product page: Variant selection (available combinations) ---------- */
let _variantUnitPrice = null; // store unit price for total calculation

document.querySelectorAll('.variation-option').forEach(option => {
    option.addEventListener('click', (e) => {
        e.preventDefault();
        if (option.classList.contains('unavailable')) return; // ignore clicks on unavailable

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

        fetchAvailableVariants();
    });
});

function getSelectedValues() {
    const selected = [];
    document.querySelectorAll('.product-choise input[type="hidden"][name$="_value_id"]').forEach(input => {
        if (input.value) selected.push(input.value);
    });
    return selected;
}

async function fetchAvailableVariants() {
    const productId = document.querySelector('input[name="product_id"]')?.value;
    if (!productId) return;

    const selected = getSelectedValues();
    const params = new URLSearchParams();
    selected.forEach(v => params.append('selected[]', v));

    try {
        const response = await fetch(`/api/products/${productId}/variants/available?${params}`);
        const data = await response.json();

        // Mark unavailable options
        if (data.available) {
            const allAvailableIds = new Set();
            Object.values(data.available).forEach(ids => ids.forEach(id => allAvailableIds.add(id)));

            document.querySelectorAll('.variation-option').forEach(opt => {
                const valId = parseInt(opt.dataset.attributeValue);
                if (allAvailableIds.has(valId)) {
                    opt.classList.remove('unavailable');
                } else {
                    opt.classList.add('unavailable');
                }
            });
        }

        // Update price/sku/stock if exact match found
        if (data.found && data.variant_id) {
            const variantIdInput = document.querySelector('input[name="variant_id"]');
            if (variantIdInput) variantIdInput.value = data.variant_id;

            _variantUnitPrice = data.price;

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

            updateTotalPrice();
        } else {
            // No exact match — clear variant_id, keep buttons enabled but show hint
            const variantIdInput = document.querySelector('input[name="variant_id"]');
            if (variantIdInput) variantIdInput.value = '';
            _variantUnitPrice = null;
            updateTotalPrice();
        }
    } catch (err) {
        console.error('Failed to fetch available variants:', err);
    }
}

/* ---------- Total price calculation ---------- */
function updateTotalPrice() {
    const totalEls = document.querySelectorAll('.total-price');
    if (!_variantUnitPrice) {
        totalEls.forEach(el => el.style.display = 'none');
        return;
    }
    const qtyInputs = document.querySelectorAll('.qty');
    qtyInputs.forEach((input, i) => {
        const qty = parseInt(input.value) || 1;
        const totalEl = totalEls[i];
        if (!totalEl) return;
        if (qty > 1) {
            const total = qty * _variantUnitPrice;
            totalEl.textContent = `${qty} × ${number_format(_variantUnitPrice)} = ${number_format(total)} ₽`;
            totalEl.style.display = '';
        } else {
            totalEl.style.display = 'none';
        }
    });
}

function number_format(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

/* ---------- Product page: Auto-select first variant values on load ---------- */
(function () {
    const variationLists = document.querySelectorAll('.product-choise ul[data-attribute-name]');
    if (!variationLists.length) return;

    variationLists.forEach(list => {
        const firstOption = list.querySelector('.variation-option');
        if (!firstOption) return;

        firstOption.querySelector('.variation-link')?.classList.add('active');

        const attrName = list.dataset.attributeName;
        const value = firstOption.dataset.attributeValue;
        if (attrName && value) {
            const hidden = document.querySelector(`input[name="${attrName}"]`);
            if (hidden) hidden.value = value;
        }
    });

    fetchAvailableVariants();
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

/* ---------- Catalog cards: Color/Size selection ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const cardsContainer = document.querySelector('.cards');
    if (!cardsContainer) return;

    cardsContainer.addEventListener('click', function (e) {
        const swatch = e.target.closest('.card__color-swatch');
        if (swatch) {
            e.preventDefault();
            e.stopPropagation();
            const card = swatch.closest('.card');
            card.querySelectorAll('.card__color-swatch').forEach(s => s.classList.remove('active'));
            swatch.classList.add('active');
            resolveCardVariant(card);
            return;
        }

        const sizeBtn = e.target.closest('.card__size-btn');
        if (sizeBtn) {
            e.preventDefault();
            e.stopPropagation();
            const card = sizeBtn.closest('.card');
            card.querySelectorAll('.card__size-btn').forEach(s => s.classList.remove('active'));
            sizeBtn.classList.add('active');
            resolveCardVariant(card);
            return;
        }
    });
});

function resolveCardVariant(card) {
    const variants = JSON.parse(card.dataset.variants || '[]');
    if (!variants.length) return;

    const activeSwatch = card.querySelector('.card__color-swatch.active');
    const activeSize = card.querySelector('.card__size-btn.active');
    const colorId = activeSwatch ? activeSwatch.dataset.colorId : null;
    const sizeId = activeSize ? activeSize.dataset.sizeId : null;

    const match = variants.find(v =>
        (!colorId || v.color_id == colorId) &&
        (!sizeId || v.size_id == sizeId)
    );

    if (match) {
        const variantInput = card.querySelector('input[name="variant_id"]');
        if (variantInput) variantInput.value = match.id;

        if (match.price) {
            const priceEl = card.querySelector('.price');
            if (priceEl) {
                priceEl.textContent = number_format(match.price) + ' ₽';
            }
        }
    }
}

/* ---------- Catalog banner Swipers ---------- */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.catalog-banner__swiper').forEach(el => {
        new Swiper(el, {
            modules: [Pagination],
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoplay: { delay: 5000 },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
        });
    });
});

/* ---------- Каталог: toggle подкатегорий ---------- */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.catalog__card--has-children').forEach(card => {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.categoryId;
            const panel = document.getElementById('subcats-' + id);
            if (!panel) return;

            // Закрыть другие открытые панели
            document.querySelectorAll('.catalog__subcategories.open').forEach(p => {
                if (p !== panel) p.classList.remove('open');
            });

            // Toggle текущей
            panel.classList.toggle('open');
        });
    });
});

/* ---------- Cookie consent banner ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const banner = document.getElementById('cookieBanner');
    const btn = document.getElementById('cookieAccept');
    if (!banner || document.cookie.includes('cookies_accepted=1')) {
        banner?.remove();
        return;
    }
    banner.style.display = '';
    btn?.addEventListener('click', function () {
        document.cookie = 'cookies_accepted=1; path=/; max-age=' + (365 * 24 * 60 * 60);
        banner.remove();
    });
});

/* ---------- Messenger bar (TG/Max) ---------- */
document.addEventListener('DOMContentLoaded', function () {
    const bar = document.getElementById('messengerBar');
    const close = document.getElementById('messengerBarClose');
    if (!bar || sessionStorage.getItem('messengerBarClosed')) {
        bar?.remove();
        return;
    }
    close?.addEventListener('click', function () {
        sessionStorage.setItem('messengerBarClosed', '1');
        bar.remove();
    });
});

/* ---------- С8: Табы торговых представителей ---------- */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reps-tabs__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.reps-tabs__btn').forEach(function (b) { b.classList.remove('active'); });
            document.querySelectorAll('.reps-city').forEach(function (c) { c.classList.remove('active'); });
            btn.classList.add('active');
            var city = btn.dataset.city;
            var panel = document.querySelector('[data-city-content="' + city + '"]');
            if (panel) panel.classList.add('active');
        });
    });
});

/* ---------- С8: Анимация при скролле (IntersectionObserver) ---------- */
document.addEventListener('DOMContentLoaded', function () {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('[data-animate]').forEach(function (el) { el.classList.add('animated'); });
        return;
    }
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('[data-animate]').forEach(function (el) { observer.observe(el); });
});

/* ---------- С8: Swiper для карточек магазинов ---------- */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.store-swiper').forEach(function (el) {
        new Swiper(el, {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
        });
    });
});

