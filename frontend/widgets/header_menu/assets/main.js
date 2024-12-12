if (window.innerWidth > 1024) {
    document.querySelector(".header__left-btn").addEventListener('mouseenter', function () {
        document.querySelector('.header__dropdown').classList.add('active');
    });

    window.addEventListener('scroll', function () {
        document.querySelector('.header__dropdown').classList.remove('active');
    });

    document.querySelector("#catalog-dropdown").addEventListener('mouseleave', function () {
        document.querySelector('.header__dropdown').classList.remove('active');
    });
} else if (window.innerWidth > 760) {
    document.querySelector(".header__left-btn").addEventListener('click', function () {
        document.querySelector('.header__dropdown').classList.add('active');
    });
} else {
    document.querySelector("#mobile-catalog").addEventListener('click', function (e) {
        document.querySelector('.header__dropdown').classList.toggle('active');
    });
    document.querySelectorAll(".dropdown__item_first").forEach(item => {
        item.addEventListener('click', () => {
            document.querySelector(".dropdown__list_first").classList.add('hide');
            item.querySelector('.submenu').classList.add('active');
        })
    })

    window.addEventListener('click', e => {
        const target = e.target;

        if (!!target.closest('.js-location-container') || !!target.closest('.js-cities-list') || !!target.closest('.js-input-autocomplete__list') || !!target.closest('.js-city-list-close')) {
            return
        }

        if (!target.closest('.header__dropdown') && !target.closest('#mobile-catalog') && !target.closest('.header__left-btn') && !target.closest('.header__search-mobile')) {
            document.querySelector('.header__dropdown').classList.remove('active');
            document.querySelector(".dropdown__list_first").classList.remove('hide');
            document.querySelectorAll(".submenu").forEach(item => {
                item.classList.remove('active');
            })
        }

        if (target.closest('.dropdown__item-back')) {
            document.querySelector(".dropdown__list_first").classList.remove('hide');
            document.querySelectorAll(".submenu").forEach(item => {
                item.classList.remove('active');
            })
        }
    });
}