const setFavorite = function(id) {
        axios
            .post(`/favorite`, {
                productId: id
            })
            .then(response => {
                if (!response.data.error) {
                    if (response.data.status) {
                        $('.js-button-like[data-product="' + id + '"]').addClass('button-like-active')
                        $('.js-button-like[data-product="' + id + '"] ~ .js-button-like-tooltip').text('Убрать из избранного')
                    }
                }
                $('.js-button-like[data-product="' + id + '"]').removeClass('button-like-loading');
            })
            .catch(error => {
                $('.js-button-like[data-product="' + id + '"]').removeClass('button-like-loading')
                throw new Error("Ошибка " + error)
            })
    },
    unsetFavorite = function(id) {
        axios
            .delete(`/favorite`, {
                data: {
                    productId: id
                }
            })
            .then(response => {
                if (!response.data.error) {
                    if (response.data.status) {
                        $('.js-button-like[data-product="' + id + '"]').removeClass('button-like-active')
                        $('.js-button-like[data-product="' + id + '"] ~ .js-button-like-tooltip').text('В избранное')
                    }
                }
            })
            .catch(error => {
                throw new Error("Ошибка " + error)
            })
    }

$(document).ready(function () {
    $(document).on('click', '.js-button-like', function (e) {
        let elem = e.target

        if (elem.classList.contains('button-like-loading')) {
            return;
        }

        if (elem.classList.contains('button-like-active')) {
            unsetFavorite(e.target.dataset.product)
        } else {
            elem.classList.add('button-like-loading')
            setFavorite(e.target.dataset.product)

            if (elem.closest('.js-mini-card') || elem.closest('.js-big-card')) {
                const element = elem.closest('.js-mini-card') ? elem.closest('.js-mini-card') : elem.closest('.js-big-card');
                
                dataLayerAddToWishlist(element, true);
            }
        }
    })
})