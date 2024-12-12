const getRating = function (productId) {
        axios
            .get(`/rating`, {
                params: {
                    productId: productId
                }
            })
            .then(response => {
                if (!response.data.error) {
                    $(`.rating__star[data-value=${response.data}]`).addClass('rating__star_active')
                }
            })
            .catch(error => {
                throw new Error("Ошибка " + error)
            })
    },
    setNewRating = function (value, productId) {
        axios
            .post(`/rating`, {
                value: value,
                productId: productId
            })
            .catch(error => {
                throw new Error("Ошибка " + error)
            })
    }

$(document).ready(function () {
    if ($('.rating').length) {
        getRating($('.rating').data('id'))
    }


    $(document).on('click', '.rating__star', function () {
        $('.rating__star').removeClass('rating__star_active')
        $(this).addClass('rating__star_active')
        setNewRating($(this).data('value'), $('.rating').data('id'))
    })
})