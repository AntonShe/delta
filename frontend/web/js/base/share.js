function copyLink() {
    $(document).on('click', '.js-copy-share-link', function() {
        const currentUrl = window.location.href;

        let el = document.createElement('textarea');
        el.value = currentUrl;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';

        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    })
}

function getShareLink(name) {
    const currentUrl = window.location.href;

    if (name === 'vk') {
        return `https://vkontakte.ru/share.php?url=${currentUrl}&noparse=true`
    }
    if (name === 'ok') {
        return `https://connect.ok.ru/offer?url=${currentUrl}`
    }
    if (name === 'tg') {
        return `https://t.me/share/url?url=${currentUrl}`;
    }
}

function setHrefLink() {
    const items = $('.js-share-btn');

    for (let i = 0; i < items.length; i++) {
        let item = items[i];
        let id = item.getAttribute("id");

        if (id === 'vk') {
            item.setAttribute("href", getShareLink('vk'));
        }
        if (id === 'ok') {
            item.setAttribute("href", getShareLink('ok'));
        }
        if (id === 'tg') {
            item.setAttribute("href", getShareLink('tg'));
        }
    }
}

$(document).ready(function () {
    setHrefLink()
    copyLink()
})
