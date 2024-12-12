$(document).ready(function () {
    //region show-more
    $(document).on('click', '.js-show-more-button', function(e) {
        let text = e.target.dataset.text
        const contentBlock = e.target.closest('.js-container')?.querySelector('.js-show-more-content')

        contentBlock.classList.toggle('open-content')

        if (contentBlock.classList.contains('open-content')) {
            e.target.textContent = 'Свернуть'
            e.target.classList.add('open')
        } else {
            e.target.textContent = text
            e.target.classList.remove('open')
        }
    })
    //endregion
    //region swiper
    const swiper = new Swiper('.promotion__swiper', {
        slidesPerView: 'auto',
        spaceBetween: 10,
        loop: true,
        navigation: {
            nextEl: '.promotion__swiper-button-next',
            prevEl: '.promotion__swiper-button-prev',
        },
        breakpoints: {
            320: {
                centeredSlides: true,
            },
            760: {
                slidesPerView: 3,
                spaceBetween: 18,
                slidesPerGroup: 1,
            },
            1760: {
                slidesPerView: 3,
                spaceBetween: 20,
                slidesPerGroup: 1,
            }
        },
        autoplay: {
            delay: 2500,
        }
    });

    if (window.innerWidth > 728) {
        const swiperProduct = new Swiper('.js-big-card__swiper', {
            slidesPerView: 'auto',
            direction: 'vertical',
            spaceBetween: 8,
            navigation: {
                nextEl: '.js-big-card__swiper-next',
                prevEl: '.js-big-card__swiper-prev',
            },
        });
    }

    const swiperLink = new Swiper('.promotion__swiper-links', {
        slidesPerView: 'auto',
        spaceBetween: 8,
        slidesPerGroup: 1,
        loop: false,
    });

    const shelfSwiper = new Swiper('.shelf-cards__swiper', {
        slidesPerView: 'auto',
        spaceBetween: 18,
        slidesPerGroup: 1,
        loop: false,
        navigation: {
            nextEl: '.button-swiper-next',
            prevEl: '.button-swiper-prev',
        },
        breakpoints: {
            1340: {
                spaceBetween: 20,
            }
        },
    });

    const popularSwiper = new Swiper('.popular__swiper', {
        slidesPerView: 'auto',
        spaceBetween: 8,
        slidesPerGroup: 1,
        loop: false,
        navigation: {
            nextEl: '.button-swiper-next',
            prevEl: '.button-swiper-prev',
        },
        breakpoints: {
            1340: {
                spaceBetween: 20,
            }
        },
    });
    //endregion
    //region tabs
    if ($('.js-tabs').length) {
        let buttonActiveClass = 'books-catalog__tabs-caption_active',
            contentActiveClass = 'books-catalog__tabs-content_active'

        $(document).on('click', '.js-tabs-button', function() {
            if ($(this).hasClass(buttonActiveClass)) {
                return false
            }

            $('.js-tabs-button').removeClass(buttonActiveClass)
            $('.js-tabs-content').removeClass(contentActiveClass)
            $(this).addClass(buttonActiveClass)
            $(`.js-tabs-content[data-id="${$(this).data('id')}"]`).addClass(contentActiveClass)
        })
    }
    //endregion
})

document.addEventListener("DOMContentLoaded", function () {

// search mobile
    document.querySelector(".header__search-mobile").addEventListener('click', function () {
        document.querySelector('.header__search').classList.toggle('search-active');

        const bodyElement = document.querySelector('body');

        if (document.querySelector('.header__search').classList.contains('search-active')) {
            document.querySelector("#catalog-dropdown").style.top = 44 + 'px';

            const inputSearch = document.querySelector('#header-search')

            if (inputSearch && bodyElement && inputSearch.value.length > 0 && !bodyElement.classList.contains('overflow-hidden')) {
                bodyElement.classList.add('overflow-hidden');
            }
        } else {
            document.querySelector("#catalog-dropdown").style.top = 0;
            
            if (bodyElement && bodyElement.classList.contains('overflow-hidden')) {
                bodyElement.classList.remove('overflow-hidden');
            }
        }
    });

    window.addEventListener('click', e => {
        const target = e.target;
        if (!target.closest('.header__search') && !target.closest('.header__search-mobile') && !target.closest("#mobile-catalog")) {
            document.querySelector('.header__search').classList.remove('search-active');
        }
    });
});