document.addEventListener('DOMContentLoaded', () => {
    const swiperModalProduct = new Swiper('.js-big-card__modal-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 0,
        navigation: {
            nextEl: '.js-big-card__modal-button-next',
            prevEl: '.js-big-card__modal-button-prev',
        },
        pagination: {
            el: ".big-card__modal-pagination",
            type: "fraction",
        },
    });

    const imgCard = document.querySelector('.js-big-card-img')
    const swiperImges = document.querySelectorAll('.js-big-card__swiper-img');
    
    if (!imgCard) return

    const mobileWrapper = document.querySelector('.js-big-card-mobile')
    const mobileContent = '.js-big-card-mobile-content'
    const headerMobile = document.querySelector('.js-header-mobile')

    imgCard.addEventListener('click', () => {
        mobileWrapper.classList.remove('hidden')
        headerMobile?.classList.add('hidden')
        document.querySelector('body').classList.add('overflow-hidden')
        swiperModalProduct.slideTo(0)
    })

    swiperImges.forEach(swiperImg => {
        swiperImg.addEventListener('click', () => {
            mobileWrapper.classList.remove('hidden')
            headerMobile?.classList.add('hidden')
            document.querySelector('body').classList.add('overflow-hidden')
            swiperModalProduct.slideTo(swiperImg.getAttribute('data-swiper-to'))
        })
    })

    mobileWrapper.addEventListener('click', (e) => {
        if (!e.target.closest(mobileContent)) {
            mobileWrapper.classList.add('hidden')
            headerMobile?.classList.remove('hidden')
            document.querySelector('body').classList.remove('overflow-hidden')
        }
    })

    const desktopSwiper = document.querySelector('.js-big-card__swiper')

    if (!desktopSwiper) return
    
    const desktopImgCount = desktopSwiper.querySelectorAll('.js-big-card__swiper-img').length

    if (desktopImgCount === 3 && window.innerWidth > 1239) {
        desktopSwiper.classList.remove('big-card__swiper--navigation')
        document.querySelector('.big-card__navigation-button')?.classList.add('hidden')
    }
})