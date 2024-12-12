function setCartIds(id) {
    return $.ajax({
        method: "POST",
        url: '/cart/item-cart-id',
        data: {
            [param]: token,
            productId: id,
        },
        dataType: 'json',
        success: function (response) {
            if (response.id !== 0) {
                document.querySelectorAll('#minus-' + id).forEach(buttonMinus => {
                    buttonMinus.setAttribute('data-product', response.id);
                })
                document.querySelectorAll('#plus-' + id).forEach(buttonPlus => {
                    buttonPlus.setAttribute('data-product', response.id);
                })
            }
        }
    })
}

function addToCart(id, quantity)
{
    return $.ajax({
        method: "POST",
        url: '/cart/add-to-cart',
        data: {
            [param]: token,
            productId: id,
            quantity: quantity
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === true) {
                getCurrentCartCount()
            }
        }
    })
}

function setQuantity(id, quantity)
{
    $.ajax({
        method: "POST",
        url: '/cart/set-quantity',
        data: {
            [param]: token,
            id: id,
            quantity: quantity
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === true) {
                getCurrentCartCount()
            }
        }
    })
}

function removeToCart(id)
{
    $.ajax({
        method: "DELETE",
        url: '/cart/delete',
        data: {
            [param]: token,
            ids: [id]
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === true) {
                getCurrentCartCount()
            }
        }
    })
}

$(document).ready(function () {
    getCurrentCartCount()

    $(document).on('click', 'button.button-tocart, #big-card-tocart', function (e)  {
        let el = $(this)

        el.parent('.add-to-cart-wrapper').find('.button-tocart ').addClass('loading')
        const id = el.data('product');
        addToCart(id, 1).then(() => {
            if (el.parent('.add-to-cart-wrapper')) {
                let id = Number(el.parent('.add-to-cart-wrapper').attr('data-product-id'));
                dataLayerAddCart(this.closest('.add-to-cart-wrapper'), id);
            }
            setCartIds(el.data('product')).then(() => {
                el.removeClass('active')
                el.addClass('hidden')

                document.querySelectorAll(`.add-to-cart-wrapper[data-product-id="${id}"]`).forEach(element => {
                    const counter = element.querySelector('.counter');
                    const buttonTocart = element.querySelector(`.button-tocart[data-product="${id}"]`);

                    counter.classList.remove('counter_hidden');
                    counter.classList.remove('loading');
                    counter.classList.add('counter_active');
                    buttonTocart.classList.remove('loading');
                    buttonTocart.classList.remove('active');
                    buttonTocart.classList.add('hidden');
                })
            })
        })
    })

    $(document).on('click', 'button[id^="minus-"]:not(.disable)', function (e)  {
        let el = $(this)
        let idProduct = el.attr('id').split('-')
        let id = el.attr('data-product')
        let counter = $('#counter-' + idProduct[1])
        let counterVal = parseInt(counter.val())
        let countRange = parseInt(counter.data('range'))

        document.querySelectorAll('#counter-' + idProduct[1]).forEach(counterInput => {
            const container = counterInput.closest(`.add-to-cart-wrapper[data-product-id="${idProduct[1]}"]`);
            const containerInput = container.querySelector('.counter');
            const buttonPlus = containerInput.querySelector('#plus-' + idProduct[1]);
            const buttonToCart = container.querySelector(`.button-tocart[data-product="${idProduct[1]}"]`);

            if (counterVal > 1) {
                counterInput.value = counterVal - 1;
            }

            if (counterInput.value < countRange) {
                buttonPlus.classList.remove('disable');
            }

            if (counterVal - 1 === 0) {
                containerInput.classList.remove('counter_active');
                containerInput.classList.add('counter_hidden');
                buttonToCart.classList.remove('hidden');

                removeToCart(id);
            } else {
                setQuantity(id, counterVal - 1);
            }
        })

        if (el.parent('.counter').parent('.add-to-cart-wrapper')) {
            dataLayerRemoveCart(this.closest('.add-to-cart-wrapper'), idProduct[1]);
        }
    })

    $(document).on('click', 'button[id^="plus-"]:not(.disable)', function (e)  {
        let el = $(this)
        let idProduct = el.attr('id').split('-')
        let id = el.attr('data-product')
        let counter = $('#counter-' + idProduct[1])
        let counterVal = parseInt(counter.val())
        let countRange = parseInt(counter.attr('data-range'))

        document.querySelectorAll('#counter-' + idProduct[1]).forEach(counterInput => {
            const containerInput = counterInput.parentElement.parentElement;
            const buttonPlus = containerInput.querySelector('#plus-' + idProduct[1]);

            if (counterVal + 1 === countRange) {
                buttonPlus.classList.add('disable');
            }

            if (counterVal < countRange) {
                counterInput.value = counterVal + 1;
            }
        })

        setQuantity(id, counter.val())

        if (el.parent('.counter').parent('.add-to-cart-wrapper')) {
            dataLayerAddCart(this.closest('.add-to-cart-wrapper'), idProduct[1]);
        }
    })
})