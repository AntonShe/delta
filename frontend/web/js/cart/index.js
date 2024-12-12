const param = $('meta[name="csrf-param"]').attr("content")
const token = $('meta[name="csrf-token"]').attr("content")

function getCurrentCartCount() {
    $.ajax({
        method: "POST",
        url: '/cart/get-total-count',
        data: {
            [param]: token,
        },
        dataType: 'json',
        success: function (response) {
            if (response.total > 0) {
                $('.js-cart-count')
                    .removeClass('hidden')
                    .text(response.total)
            } else {
                $('.js-cart-count').addClass('hidden')
            }
        }
    })
}

function showAndHiddenButtonDescription() {
    if (document.querySelector('.js-show-more-content') && document.querySelector('.js-show-more-button')) {
        let descriptionContent = document.querySelector('.js-show-more-content');
        let descriptionButton = document.querySelector('.js-show-more-button');
        if (descriptionContent.scrollHeight <= descriptionContent.offsetHeight) {
            descriptionButton.style.display = 'none';
        }
    }
}

function addDataLayerCart() {
    if (document.querySelector('.js-big-card') && document.querySelector('.js-big-card').hasAttributes()) {
        const product = document.querySelector('.js-big-card');
        dataLayerItem(product);
    }

    if (document.querySelector('.js-shelf-cards') && document.querySelector('.js-shelf-cards').hasAttributes()) {
        const containerList = document.querySelectorAll('.js-shelf-cards');
        containerList.forEach(elList => {
            dataLayerList(elList);
        })
    }

    if (document.querySelector('.js-card-catalog')) {
        dataLayerList(document.querySelector('.js-card-catalog'), null)
    }
}

$(document).ready(function () {
    getCurrentCartCount()
    showAndHiddenButtonDescription()
    addDataLayerCart()
})