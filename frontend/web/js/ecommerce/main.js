window.dataLayer = window.dataLayer || [];

function ecommerceCartItem(element, isNodeElement = true) {
    if (!element) {
        return;
    }

    const dataCart = transformCart(element, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "view_item",
        ecommerce: {
            currency: "RUB",
            value: dataCart.price,
            items: [
                dataCart
            ]
        }
    });
}

function ecommerceCartList(list_id, list_name, itemList, isNodeElement = true) {
    if (!list_id || !list_name || !itemList) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "view_item_list",
        ecommerce: {
            item_list_id: list_id,
            item_list_name: list_name,
            items: dataCartList
        }
    });
}

function ecommerceSelectItem(list_id, list_name, element, isNodeElement = true) {
    if (!list_id || !list_name || !element) {
        return;
    }

    const dataCart = transformCart(element, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "select_item",
        ecommerce: {
            item_list_id: list_id,
            item_list_name: list_name,
            items: [
                dataCart
            ]
        }
    });
}

function ecommerceAddToCart(element, isNodeElement = true) {
    if (!element) {
        return;
    }

    const dataCart = transformCart(element, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "add_to_cart",
        ecommerce: {
            currency: "RUB",
            value: dataCart.price,
            items: [
                dataCart
            ]
        }
    });
}

function ecommerceRemoveFromCart(element, isNodeElement = true) {
    if (!element) {
        return;
    }

    const dataCart = transformCart(element, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "remove_from_cart",
        ecommerce: {
            currency: "RUB",
            value: dataCart.price,
            items: [
                dataCart
            ]
        }
    });
}

function ecommerceViewCart(itemList, total, isNodeElement = true,) {
    if (!itemList || !total) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "view_cart",
        ecommerce: {
            currency: "RUB",
            value: total,
            items: dataCartList
        }
    });
}

function ecommerceAddToWishlist(element, isNodeElement = true) {
    if (!element) {
        return;
    }

    const dataCart = transformCart(element, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "add_to_wishlist",
        ecommerce: {
            currency: "RUB",
            value: dataCart.price,
            items: [
                dataCart
            ]
        }
    });
}

function ecommerceBeginCheckout(itemList, total, coupon = '', isNodeElement = true) {
    if (!itemList || !total) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "begin_checkout",
        ecommerce: {
            currency: "RUB",
            value: total,
            coupon: coupon,
            items: dataCartList
        }
    });
}

function ecommercePaymentInfo(itemList, total, coupon = '', paymentType, isNodeElement = true) {
    if (!itemList || !total || !paymentType) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "add_payment_info",
        ecommerce: {
            currency: "RUB",
            value: total,
            coupon: coupon,
            payment_type: paymentType,
            items: dataCartList
        }
    });
}

function ecommercePurchase(orderId, itemList, total, shipping = 0, coupon = '', isNodeElement = true) {
    if (!itemList || !total || !orderId) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "purchase",
        ecommerce: {
            transaction_id: orderId,
            value: total,
            shipping: shipping,
            currency: "RUB",
            coupon: coupon,
            items: dataCartList
        }
    });
}

function ecommerceAddShippingInfo(itemList, total, shippingTier, coupon = '', isNodeElement = true) {
    if (!itemList || !total || !shippingTier) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "add_shipping_info",
        ecommerce: {
            value: total,
            shipping_tier: shippingTier,
            currency: "RUB",
            coupon: coupon,
            items: dataCartList
        }
    });
}

function ecommerceRefund(orderId, itemList, total, shipping = 0, coupon = '', isNodeElement = true) {
    if (!orderId || !itemList || !total) {
        return;
    }

    const dataCartList = transformCartList(itemList, isNodeElement);

    window.dataLayer.push({ecommerce: null});
    window.dataLayer.push({
        event: "refund",
        ecommerce: {
            transaction_id: orderId,
            value: total,
            shipping: shipping,
            currency: "RUB",
            coupon: coupon,
            items: dataCartList
        }
    });
}

function dataLayerError(code, desc = '', page = '', urlRequest = '') {
    if (!code) {
        return;
    }

    window.dataLayer.push({
        event: "error",
        error_code: code,
        error_desc: desc,
        error_place: page,
        error_attr: urlRequest
    });
}

function transformCartList(itemList, isNodeElement) {
    return itemList.map(el => transformCart(el, isNodeElement));
}

function transformCart(element, isNodeElement) {
    if (!element) {
        return;
    }

    let dataProduct = isNodeElement ? {} : {
        ...element,
        name: element.title,
    };

    if (isNodeElement) {
        const attrs = element.attributes;

        for (let key in attrs) {

            if (attrs[key].name && attrs[key].name.indexOf('data-product') !== -1) {
                let keyData = attrs[key].name.replace('data-product-', '');

                if (keyData === 'genres') {
                    let category = attrs[key].value.split('/');

                    category.forEach((el, index) => {
                        dataProduct[`category${++index}`] = el;
                    })
                }
                if (keyData === 'oldprice' || keyData === 'price' || keyData === 'quantity' || keyData === 'index') {
                    dataProduct[keyData] = Number(attrs[key].value.replace(/[^0-9]/g, ''));
                } else {
                    dataProduct[keyData] = attrs[key].value;
                }
            }
        }
        dataProduct.discount = dataProduct.oldprice ? dataProduct.oldprice - dataProduct.price : 0;
    } else {
        dataProduct.genres.forEach((el, index) => {
            dataProduct[`category${++index}`] = el.name;
        })
        dataProduct.discount = dataProduct.price.discount ? dataProduct.price.discount : 0;
        dataProduct.price = dataProduct.price.new ? dataProduct.price.new : 0;
    }

    const {
        id,
        name,
        price = 0,
        coupon = '',
        discount = 0,
        index = 0,
        brand = '',
        category1 = '',
        category2 = '',
        category3 = '',
        category4 = '',
        category5 = '',
        list_id = '',
        list_name = '',
        quantity = 1
    } = dataProduct;

    if (!id || !name) {
        return;
    }

    return {
        item_id: id, // ir Артикул
        item_name: name, // ir название
        coupon: coupon, // nr название акции, в которой участвует товар.
        discount: discount, // nr скидка
        index: index, //nr позиция товара в списке, понадобится для списков
        item_brand: brand, //nr название издателя
        item_category: category1, //nr категория (язык)
        item_category2: category2, //nr под-категория (уровень)
        item_category3: category3, //nr под-категория (курс)
        item_category4: category4, //nr под-категория
        item_category5: category5, //nr под-категория
        item_list_id: list_id, //nr идентификатор списка товаров,в котором участвует, например new-новинки или pop-популярное
        item_list_name: list_name, //nr название списка товаров, в котором участвует
        price: price, // цена единицы товара после скидки
        quantity: quantity //nr кол-во. Служит, например, если на странице представлен набор из единичных позиций.
    };
}

//перенести в другой файл
function dataLayerSearch(search) {
    if (!search) {
        return;
    }

    dataLayer.push({
        event: "search",
        'search_term': search
    });
}

const dataLayerItem = (nodeElement) => {
    ecommerceCartItem(nodeElement);
}

const dataLayerList = (nodeContainerList, rootVisible = null) => {
    if (nodeContainerList && nodeContainerList.hasAttributes()) {
        const list_id = nodeContainerList.getAttribute('data-productList-id');
        const list_name = nodeContainerList.getAttribute('data-productList-name');

        const observer = new IntersectionObserver(callbackEcommerce, {
            root: rootVisible,
            threshold: 0.75
        });

        nodeContainerList.querySelectorAll('.js-mini-card').forEach((el, index) => {
            el.setAttribute('data-product-index', el.getAttribute('data-product-index') ? el.getAttribute('data-product-index') : index);
            el.setAttribute('data-product-list_id', el.getAttribute('data-product-list_id') ? el.getAttribute('data-product-list_id') : list_id);
            el.setAttribute('data-product-list_name', el.getAttribute('data-product-list_name') ? el.getAttribute('data-product-list_name') : list_name);
            observer.observe(el);
        })

        let timer = null;
        let visibleNodelist = [];

        function callbackEcommerce(event) {
            event.forEach((el) => {
                if (el.isIntersecting) {
                    visibleNodelist.push(el.target);
                    observer.unobserve(el.target);
                }
            })

            if (visibleNodelist.length > 0) {
                clearTimeout(timer);
                timer = setTimeout(() => {

                    ecommerceCartList(list_id, list_name, visibleNodelist);
                    visibleNodelist = [];
                }, 1000);
            }
        }
    }
}

window.dataAddCart = window.dataAddCart ? window.dataAddCart : {};
window.dataRemoveCart = window.dataRemoveCart ? window.dataRemoveCart : {};

const dataLayerAddCart = (element, id, isNodeElement = true, quantity = 0) => {
    if (!element || !id) {
        return;
    }

    if (quantity) {
        if (isNodeElement) {
            element.setAttribute('data-product-quantity', quantity);
            ecommerceAddToCart(element, isNodeElement);
        } else {
            ecommerceAddToCart({...element, quantity}, isNodeElement);
        }
        return;
    }

    window.dataAddCart[id] = window.dataAddCart[id] ? window.dataAddCart[id] : {timerAdd: null, quantityAdd: 0};
    window.dataAddCart[id].quantityAdd = window.dataAddCart[id].quantityAdd + 1;

    clearTimeout(window.dataAddCart[id].timerAdd);
    window.dataAddCart[id].timerAdd = setTimeout(() => {
        if (isNodeElement) {
            element.setAttribute('data-product-quantity', window.dataAddCart[id].quantityAdd);
            ecommerceAddToCart(element, isNodeElement);
        } else {
            ecommerceAddToCart({...element, quantity: window.dataAddCart[id].quantityAdd}, isNodeElement);
        }
        delete window.dataAddCart[id];
    }, 500);
}

const dataLayerRemoveCart = (element, id, isNodeElement = true, quantity = 0) => {
    if (!element || !id) {
        return;
    }

    if (quantity) {
        if (isNodeElement) {
            element.setAttribute('data-product-quantity', quantity);
            ecommerceRemoveFromCart(element, isNodeElement);
        } else {
            ecommerceRemoveFromCart({...element, quantity}, isNodeElement);
        }
        return;
    }

    window.dataRemoveCart[id] = window.dataRemoveCart[id] ? window.dataRemoveCart[id] : {
        timerRemove: null,
        quantityRemove: 0
    };
    window.dataRemoveCart[id].quantityRemove = window.dataRemoveCart[id].quantityRemove + 1;

    clearTimeout(window.dataRemoveCart[id].timerRemove);
    window.dataRemoveCart[id].timerRemove = setTimeout(() => {
        if (isNodeElement) {
            element.setAttribute('data-product-quantity', window.dataRemoveCart[id].quantityRemove);
            ecommerceRemoveFromCart(element, isNodeElement);
        } else {
            ecommerceRemoveFromCart({...element, quantity: window.dataRemoveCart[id].quantityRemove}, isNodeElement);
        }
        delete window.dataRemoveCart[id];
    }, 500);
}

const dataLayerViewCart = (itemList, total, isNodeElement = false) => {
    const transformList = itemList.map((item, index) => {
        return {
            ...item.productInfo,
            list_id: 'cart',
            list_name: 'Корзина',
            index: index,
            quantity: item.quantity.cart
        }
    });
    ecommerceViewCart(transformList, total, isNodeElement);
}

const dataLayerSelectItem = (list_id, list_name, element, isNodeElement = true) => {
    if (!isNodeElement) {
        const transformElement = {
            ...element,
            list_id: list_id,
            list_name: list_name,
            quantity: 1
        };
        ecommerceSelectItem(list_id, list_name, transformElement, isNodeElement);
    } else {
        ecommerceSelectItem(list_id, list_name, element, isNodeElement);
    }
}

const dataLayerAddToWishlist = (element, isNodeElement = true) => {
    if (!isNodeElement) {
        ecommerceAddToWishlist({...element, quantity: 1}, isNodeElement);
    } else {
        ecommerceAddToWishlist(element, isNodeElement);
    }
}

const dataLayerBeginCheckout = (itemList, total, coupon = '', isNodeElement = false) => {
    const transformList = dataTransformList(itemList);
    ecommerceBeginCheckout(transformList, total, coupon, isNodeElement);
}

const dataLayerPaymentInfo = (itemList, total, coupon = '', paymentType, isNodeElement = false) => {
    const transformList = dataTransformList(itemList);
    ecommercePaymentInfo(transformList, total, coupon, paymentType, isNodeElement);
}

const dataLayerPurchase = (orderId, itemList, total, shipping = 0, coupon = '', isNodeElement = false) => {
    const transformList = dataTransformList(itemList);
    ecommercePurchase(orderId, transformList, total, shipping, coupon, isNodeElement);
}

const dataLayerRefund = (orderId, itemList, total, shipping = 0, coupon = '', isNodeElement = false) => {
    const transformList = itemList.map(item => {
        return {
            ...item,
            quantity: item.quantityCart,
            price: {
                discount: item.oldPrice - item.price,
                new: item.price
            }
        }
    });

    ecommerceRefund(orderId, transformList, total, shipping, coupon, isNodeElement);
}

const dataLayerAddShippingInfo = (itemList, total, shippingTier, coupon = '', isNodeElement = false) => {
    const transformList = dataTransformList(itemList);
    ecommerceAddShippingInfo(transformList, total, shippingTier, coupon, isNodeElement);
}

function dataTransformList(itemList) {
    if (Array.isArray(itemList)) {
        return itemList.map((item) => {
            return {
                ...item.productInfo,
                quantity: item.quantity.cart
            }
        });
    }

    return [{...itemList.productInfo, quantity: itemList.quantity.cart}]
}

document.addEventListener('click', event => {
    const elementClick = event.target
    if (elementClick.classList.contains('js-mini-card-link') || elementClick.parentElement && elementClick.parentElement.classList.contains('js-mini-card-link')) {
        const element = elementClick.closest('.js-mini-card');
        const list_id = element.getAttribute('data-product-list_id');
        const list_name = element.getAttribute('data-product-list_name');
        dataLayerSelectItem(list_id, list_name, element, true);
    }
});

// просмотр баннеров

function gtmActionPromotion(banner, title = 'Карусель баннеров', event = 'view_promotion') {
    const bannerData = banner.dataset;
    let items = [{
            promotion_id: bannerData.bannerId,
            promotion_name: bannerData.bannerName,
            creative_name: title,
            creative_slot: bannerData.bannerSlot
        }]

    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event,
        ecommerce: {
            items
        }
    });
}

const viewBanner = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            let obj = entry.target
            
            observer.unobserve(obj)

            const title = 'Карусель баннеров'

            gtmActionPromotion(obj, title, 'view_promotion')
        }
    })
}, { threshold: 0.7});

if (document.querySelector('.js-banner-analytics')) {
    [...document.querySelectorAll('.js-banner-analytics')]
    .forEach(banner => {
        viewBanner.observe(banner)

        const title = 'Карусель баннеров'

        banner.addEventListener('click', () => {
            gtmActionPromotion(banner, title, 'select_promotion')
        })
    })
}

