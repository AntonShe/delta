import AbstractBalloon from "./AbstractBalloon"
const {Fancybox} = require('@fancyapps/ui')
import "@fancyapps/ui/dist/fancybox.css"

Fancybox.bind('[data-fancybox="gallery"]', {
    Toolbar: {
        display: ["close"],
    },
});

export default class BaseBalloonPvz extends AbstractBalloon {
    render(data: {
        // REQUIRED
        work_hours: string
        photos: string
        address: string
        metro: string
        location_description: string
        is_cash: boolean
        is_card: boolean
        fitting_rooms_count: number
        phone: string

        // OPTIONAL
        delivery_price_from?: number
        delivery_price_to?: number
        delivery_days_from?: number
    }): string {
        let work_hours = JSON.parse(data.work_hours),
            isAllWorkHoursEq = this.isAllWorkHoursEq(work_hours),
            templateListWorkHouse = ''
        
        if (!isAllWorkHoursEq) {
            work_hours.forEach((elem: { [prop: string]: any }) => {
                templateListWorkHouse += `<p>${elem.short.toUpperCase()}: 
                    ${elem.from.slice(0, -3)}—${elem.to.slice(0, -3)}</p>`
            })
        } else {
            templateListWorkHouse += `<p>Ежедневно:</p>
                <p>${work_hours[0].from.slice(0, -3)}—${work_hours[0].to.slice(0, -3)}</p>`
        }

        let hasPhotos = false,
            templatePhotos = ''
        if (data.photos) {
            hasPhotos = true
            let photos = JSON.parse(data.photos)
            templatePhotos += '<div class="balloon-photos">'
            photos.forEach((link: string) => {
                templatePhotos += `<a class="balloon-photos__container" href="${link}" data-fancybox="gallery">
                    <img src="${link}" alt="Фото пункта" class="balloon-photos__photo"></a>`
            })
            templatePhotos += '</div>'
        }

        let templateCalculator = '',
            blockCalculatorPrice = '',
            blockCalculatorDays = ''
        if (typeof data.delivery_price_from !== 'undefined') {
            blockCalculatorPrice = `
                <div class="balloon-calculator__info-container balloon-calculator__info-container_mr-24">
                    ${this.svg.wallet}
                    <p class="balloon-container-text">
                        ${data.delivery_price_from}${data.delivery_price_to ? `-${data.delivery_price_to}` : ''} &#8381;
                    </p>
                </div>`
        }
        if (typeof data.delivery_days_from !== 'undefined') {
            blockCalculatorDays = `
                <div class="balloon-calculator__info-container">
                    ${this.svg.time}
                    <p class="balloon-container-text">
                        от ${data.delivery_days_from} ${wordEndings(data.delivery_days_from, 'дня', 'дней', 'дней')}
                    </p>
                </div>`
        }
        if (blockCalculatorPrice !== '' || blockCalculatorDays !== '') {
            templateCalculator = `
                <div class="balloon-calculator">
                    ${blockCalculatorPrice}
                    ${blockCalculatorDays}
                </div>`
        }

        return `
            ${this.style}
            <div class="balloon-pvz">
                <div class="balloon-close">
                    ${this.svg.cross}
                </div>
                <div class="balloon-header">
                    ${this.svg.icon}
                    <div class="balloon-header__address">
                        <p>${data.address}</p>` + (data.metro ? `
                        <p class="balloon-header__metro"><b>м.${data.metro}</b></p>` : '') + `
                    </div>
                </div>
                ${templateCalculator}
                <dl class="balloon-list">
                    <!-- О пункте -->
                    <dt class="balloon-list__title balloon-list__title_selected" data-type="general-info">О пункте</dt>
                    <dd class="balloon-list__description balloon-list__description_selected" data-type="general-info">
                        <div class="balloon-list__text-container">
                            <div class="balloon-list__img_fs-and-mt3">${this.svg.location}</div> 
                            <p class="balloon-container-text">${data.location_description}</p>
                        </div>
                        <div class="balloon-list__text-container">
                            ${this.svg[data.is_cash ? 'money' : (data.is_card ? 'card' : 'ruble')]}
                            <p class="balloon-container-text">${(data.is_cash && data.is_card) ? 'Оплата картой или наличными' : (data.is_cash ? 'Оплата только наличными' : 'Только предоплата')}</p>
                        </div>
                        <div class="balloon-list__text-container ${!data.fitting_rooms_count ? 'balloon-list__text-container_not-mb' : ''}">
                            ${this.svg.number}
                            <p class="balloon-container-text">${data.phone}</p>
                        </div>` + (data.fitting_rooms_count ? `
                        <div class="balloon-list__text-container balloon-list__text-container_not-mb">
                            ${this.svg.hanger}
                            <p class="balloon-container-text">${data.fitting_rooms_count} ${wordEndings(data.fitting_rooms_count, 'примерочная', 'примерочные', 'примерочных')}</p>
                        </div>` : '') + `
                    </dd>
                    
                    <!-- Режим работы -->
                    <dt class="balloon-list__title" data-type="operating-mode">Режим работы</dt>
                    <dd class="balloon-list__description" data-type="operating-mode">
                        <div class="operating-mode-container">
                            <div class="balloon-list__img_fs-and-mt3">${this.svg.time}</div>
                            <div class="operating-mode-container__days">
                                ${templateListWorkHouse}
                            </div>
                            <div class="operating-mode-container__breaks">
                                <p>Перерывы:</p>
                                <p>11:00-11:15</p>
                                <p>13:00-13:15</p>
                                <p>16:00-16:15</p>
                                <p>18:00-18:15</p>
                            </div>
                        </div>
                    </dd>
                    
                    ` + (hasPhotos ? `  
                    <!-- Фото -->
                    <dt class="balloon-list__title" data-type="photo">Фото</dt>
                    <dd class="balloon-list__description" data-type="photo">
                        ${templatePhotos}
                    </dd>`: ``) + `
                </dl>
            </div>`
    }

    bindListener(): void {
        Array.from(document.getElementsByClassName('balloon-list__title')).forEach(title =>
            title.addEventListener('click', this.selectTitle))

        let cross = document.querySelector('.balloon-close')
        if (cross) cross.addEventListener('click', () => this.closeBalloon())
    }

    unbindListener(): void {
        Array.from(document.getElementsByClassName('balloon-list__title')).forEach(title =>
            title.removeEventListener('click', this.selectTitle))

        let cross = document.querySelector('.balloon-close')
        if (cross) cross.removeEventListener('click', () => this.closeBalloon())
    }

    selectTitle(this: Element, e: Event) {
        let classTitleSelected = 'balloon-list__title_selected',
            classDescriptionSelected = 'balloon-list__description_selected'

        document.getElementsByClassName(classTitleSelected)[0].classList.remove(classTitleSelected)
        document.getElementsByClassName(classDescriptionSelected)[0].classList.remove(classDescriptionSelected)

        this.classList.add(classTitleSelected)
        //@ts-ignore
        document.querySelector(`.balloon-list__description[data-type='${this.dataset.type}']`).classList
            .add(classDescriptionSelected)
    }

    closeBalloon() {
        //@ts-ignore
        this.map.balloon.close()
    }

    private isAllWorkHoursEq(workHours: Array<{[prop: string] : any}>){
        let absoluteVal = workHours[0]
        let isAllEq = true

        workHours.forEach(function (elem, index) {
            if(elem.from != absoluteVal.from || elem.to != absoluteVal.to){
                isAllEq = false
            }
        })

        return isAllEq
    }

    style: string = `
        <style>
            [class*="-balloon"] {
                border-radius: 16px;
                font-family: Golos-R, sans-serif;
                font-size: 15px;
                line-height: 21px;
            }
            [class*="-balloon__content"] > ymaps {
                height: 100% !important;
            }
                  
            .balloon-pvz {
                margin: 14px 12px;
                width: 312px;
                position: relative;
            }
            
            .balloon-close {
                position: absolute;
                top: -3px;
                right: -3px;
                cursor: pointer;
            }
            
            .balloon-container-text {
                margin-left: 8px;
            }
            
            .balloon-header {
                display: flex;
                align-items: center;
            }
            .balloon-header__address {
                margin-left: 12px;
                margin-right: 14px;
            }
            .balloon-header__metro {
                margin-top: 4px;
            }
            
            .balloon-calculator {
                margin-top: 8px;
                display: flex;
                padding-top: 8px;
                border-top: 1px solid #C8C8C8;
            }
            .balloon-calculator__info-container {
                display: flex;
                align-items: center;
            }
            .balloon-calculator__info-container_mr-24 {
                margin-right: 24px;
            }
            
            .balloon-list {
                margin-top: 24px;
                display: flex;
                flex-wrap: wrap;
                position: relative;
            }
            .balloon-list::after {
                content: '';
                width: 100%;
                height: 1px;
                background: #C8C8C8;
                position: absolute;
                top: 23px;
                z-index: 0;
            }
            
            .balloon-list__title {
                margin-right: 16px;
                padding-bottom: 2px;
                order: -1;
                cursor: pointer;
            }
            .balloon-list__title_selected {
                z-index: 1;
                color: #5B2599;
                border-bottom: 2px solid #5B2599;
            }
            
            .balloon-list__description {
                display: none;
            }
            .balloon-list__description_selected {
                width: 100%;
                height: 100%;
                display: block;
                margin-left: 0;
                margin-top: 16px;
            }
            .balloon-list__text-container {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
            }
            .balloon-list__text-container_not-mb {
                margin-bottom: 0;
            }
            .balloon-list__img_fs-and-mt3 {
                align-self: flex-start;
                margin-top: 3px;
            }
            .operating-mode-container {
                display: flex;
            }
            .operating-mode-container__days {
                margin-left: 5px;
            }
            .operating-mode-container__breaks {
                margin-left: 30px;
            }
            
            .balloon-photos {
                display: flex;
            }
            .balloon-photos__container {
                display: flex;
                align-items: center;
                height: 90px;
                max-width: 90px;
                margin-right: 10px;
            }
            .balloon-photos__photo {
                cursor: zoom-in;
                max-height: 100%;
            }
        </style>`

    svg: { [prop: string]: any } = {
        icon: '<svg width="27.6" height="39.6" viewBox="0 0 185 262" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.4998 137.959L20.3666 137.772L20.2211 137.596C8.21702 123.15 1 104.626 1 84.3985C1 38.3515 38.406 1 84.5924 1H99.4324C145.613 1 183.025 38.3515 183.025 84.3985C183.025 104.626 175.808 123.15 163.798 137.596L163.652 137.772L163.519 137.959C158.75 144.607 153.151 151.394 147.103 158.744C146.128 159.932 145.14 161.131 144.14 162.349C137.02 171.021 129.47 180.395 122.495 190.696C109.413 210.002 98.2144 232.738 95.0392 261H88.9917C85.8225 232.744 74.6183 210.008 61.5356 190.696C54.5549 180.395 47.0107 171.021 39.8906 162.349C38.8908 161.131 37.9031 159.932 36.9275 158.75C30.8678 151.394 25.2748 144.607 20.4998 137.959Z" fill="#5B2599" stroke="#FEFEFE" stroke-width="2"/><path d="M102.063 108.734V76.0361H51.3986C51.3986 91.5427 48.0113 97.2872 37.5039 97.2872V109.382C55.2162 109.382 63.9238 95.8935 63.9238 84.2954C65.5599 84.2954 88.841 84.2954 88.841 84.2954V108.734H102.063Z" fill="#FEFEFE"/><path fill-rule="evenodd" clip-rule="evenodd" d="M105.589 34.0312C107.056 34.0312 108.498 34.2191 109.91 34.5766C116.563 37.4065 122.95 43.4661 128.01 52.907C133.67 63.5476 137.136 77.4363 137.154 93.1246C137.148 95.7485 137.057 98.3359 136.875 100.869C135.948 113.467 132.749 124.641 128.016 133.536C123.744 141.529 118.545 147.092 113 150.333C110.625 151.4 108.147 151.964 105.595 151.964C102.614 151.964 99.7298 151.188 96.9908 149.746C94.4215 148.085 91.9856 145.94 89.7132 143.365C86.5743 139.766 83.9445 135.56 81.8418 131.149C79.9754 127.198 78.4363 122.938 77.2365 118.611C77.091 118.084 76.9517 117.551 76.8184 117.018H82.7507C83.5506 119.472 84.4716 121.835 85.5381 124.023C87.1379 127.337 89.1073 130.361 91.4402 132.761C94.0762 135.5 96.9787 137.22 99.9782 137.796C103.414 138.457 106.983 137.608 110.419 135.075C114.321 132.185 117.89 127.252 120.593 120.338C123.562 112.818 125.295 103.511 125.295 93.2216C125.295 82.9324 123.562 73.6248 120.593 66.1048C117.86 59.209 114.321 54.2582 110.419 51.386C107.013 48.8712 103.414 48.0047 99.9782 48.6652C96.9787 49.2409 94.0762 50.9618 91.4402 53.7008C89.1073 56.1004 87.1379 59.1241 85.5381 62.4387C85.1927 63.1416 84.8655 63.8688 84.5444 64.6081H78.1757C79.1877 61.4025 80.4056 58.2697 81.8296 55.3066C83.9323 50.877 86.5319 46.6898 89.7011 43.0904C92.9733 39.3879 96.5909 36.5642 100.451 34.8069C102.129 34.2979 103.838 34.0312 105.589 34.0312Z" fill="#65D0DD"/> </svg>',
        cross: '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#170927"/></svg>',
        location: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.99992 1.3335C5.41992 1.3335 3.33325 3.42016 3.33325 6.00016C3.33325 9.50016 7.99992 14.6668 7.99992 14.6668C7.99992 14.6668 12.6666 9.50016 12.6666 6.00016C12.6666 3.42016 10.5799 1.3335 7.99992 1.3335ZM4.66659 6.00016C4.66659 4.16016 6.15992 2.66683 7.99992 2.66683C9.83992 2.66683 11.3333 4.16016 11.3333 6.00016C11.3333 7.92016 9.41325 10.7935 7.99992 12.5868C6.61325 10.8068 4.66659 7.90016 4.66659 6.00016Z" fill="#797979"/><path d="M7.99992 7.66683C8.92039 7.66683 9.66659 6.92064 9.66659 6.00016C9.66659 5.07969 8.92039 4.3335 7.99992 4.3335C7.07944 4.3335 6.33325 5.07969 6.33325 6.00016C6.33325 6.92064 7.07944 7.66683 7.99992 7.66683Z" fill="#797979"/></svg>',
        money: '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.667 9.83332V4.49999C12.667 3.76666 12.067 3.16666 11.3337 3.16666H2.00033C1.26699 3.16666 0.666992 3.76666 0.666992 4.49999V9.83332C0.666992 10.5667 1.26699 11.1667 2.00033 11.1667H11.3337C12.067 11.1667 12.667 10.5667 12.667 9.83332ZM11.3337 9.83332H2.00033V4.49999H11.3337V9.83332ZM6.66699 5.16666C5.56033 5.16666 4.66699 6.05999 4.66699 7.16666C4.66699 8.27332 5.56033 9.16666 6.66699 9.16666C7.77366 9.16666 8.66699 8.27332 8.66699 7.16666C8.66699 6.05999 7.77366 5.16666 6.66699 5.16666ZM15.3337 5.16666V12.5C15.3337 13.2333 14.7337 13.8333 14.0003 13.8333H2.66699C2.66699 13.1667 2.66699 13.2333 2.66699 12.5H14.0003V5.16666C14.7337 5.16666 14.667 5.16666 15.3337 5.16666Z" fill="#797979"/></svg>',
        card: '<svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.3334 0.666504H1.66671C0.926707 0.666504 0.340041 1.25984 0.340041 1.99984L0.333374 9.99984C0.333374 10.7398 0.926707 11.3332 1.66671 11.3332H12.3334C13.0734 11.3332 13.6667 10.7398 13.6667 9.99984V1.99984C13.6667 1.25984 13.0734 0.666504 12.3334 0.666504ZM12.3334 9.99984H1.66671V5.99984H12.3334V9.99984ZM12.3334 3.33317H1.66671V1.99984H12.3334V3.33317Z" fill="#797979"/></svg>',
        ruble: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.00001 1.3335C4.32001 1.3335 1.33334 4.32016 1.33334 8.00016C1.33334 11.6802 4.32001 14.6668 8.00001 14.6668C11.68 14.6668 14.6667 11.6802 14.6667 8.00016C14.6667 4.32016 11.68 1.3335 8.00001 1.3335ZM8.00001 13.3335C5.06001 13.3335 2.66668 10.9402 2.66668 8.00016C2.66668 5.06016 5.06001 2.66683 8.00001 2.66683C10.94 2.66683 13.3333 5.06016 13.3333 8.00016C13.3333 10.9402 10.94 13.3335 8.00001 13.3335Z" fill="#797979"/><path d="M6.16093 10.3998H5.33334V9.5046H6.16093V8.91412H5.33334V7.92365H6.16093V4.6665H8.32185C9.09426 4.6665 9.67664 4.86015 10.069 5.24746C10.4674 5.62841 10.6667 6.14269 10.6667 6.79031C10.6667 7.43793 10.4674 7.9554 10.069 8.34269C9.67664 8.72365 9.09426 8.91412 8.32185 8.91412H7.26438V9.5046H9.37932V10.3998H7.26438V11.3332H6.16093V10.3998ZM8.2299 7.92365C8.72032 7.92365 9.06361 7.83159 9.25978 7.64746C9.46208 7.45698 9.56323 7.17127 9.56323 6.79031C9.56323 6.40936 9.46208 6.12682 9.25978 5.94269C9.06361 5.75222 8.72032 5.65698 8.2299 5.65698H7.26438V7.92365H8.2299Z" fill="#797979"/></svg>',
        number: '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.36 3.83333C4.4 4.42667 4.5 5.00667 4.66 5.56L3.86 6.36C3.58667 5.56 3.41333 4.71333 3.35333 3.83333H4.36ZM10.9333 11.8467C11.5 12.0067 12.08 12.1067 12.6667 12.1467V13.14C11.7867 13.08 10.94 12.9067 10.1333 12.64L10.9333 11.8467ZM5 2.5H2.66667C2.3 2.5 2 2.8 2 3.16667C2 9.42667 7.07333 14.5 13.3333 14.5C13.7 14.5 14 14.2 14 13.8333V11.5067C14 11.14 13.7 10.84 13.3333 10.84C12.5067 10.84 11.7 10.7067 10.9533 10.46C10.8867 10.4333 10.8133 10.4267 10.7467 10.4267C10.5733 10.4267 10.4067 10.4933 10.2733 10.62L8.80667 12.0867C6.92 11.12 5.37333 9.58 4.41333 7.69333L5.88 6.22667C6.06667 6.04 6.12 5.78 6.04667 5.54667C5.8 4.8 5.66667 4 5.66667 3.16667C5.66667 2.8 5.36667 2.5 5 2.5Z" fill="#797979"/></svg>',
        hanger: '<svg width="16" height="16" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.4002 12.6333L8.66684 8.33334V7.72667C9.76684 7.4 10.5335 6.28 10.2868 5.02667C10.1135 4.15334 9.42017 3.42667 8.54684 3.22667C7.02684 2.88 5.66684 4.03334 5.66684 5.5H7.00017C7.00017 4.94667 7.44684 4.5 8.00017 4.5C8.55351 4.5 9.00017 4.94667 9.00017 5.5C9.00017 6.06 8.54017 6.51334 7.98017 6.5C7.62017 6.49334 7.33351 6.8 7.33351 7.16V8.33334L1.60017 12.6333C1.08684 13.02 1.36017 13.8333 2.00017 13.8333H8.00017H14.0002C14.6402 13.8333 14.9135 13.02 14.4002 12.6333ZM4.00017 12.5L8.00017 9.5L12.0002 12.5H4.00017Z" fill="#797979"/></svg>',
        time: '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.99325 1.8335C4.31325 1.8335 1.33325 4.82016 1.33325 8.50016C1.33325 12.1802 4.31325 15.1668 7.99325 15.1668C11.6799 15.1668 14.6666 12.1802 14.6666 8.50016C14.6666 4.82016 11.6799 1.8335 7.99325 1.8335ZM7.99992 13.8335C5.05325 13.8335 2.66659 11.4468 2.66659 8.50016C2.66659 5.5535 5.05325 3.16683 7.99992 3.16683C10.9466 3.16683 13.3333 5.5535 13.3333 8.50016C13.3333 11.4468 10.9466 13.8335 7.99992 13.8335ZM8.33325 5.16683H7.33325V9.16683L10.8333 11.2668L11.3333 10.4468L8.33325 8.66683V5.16683Z" fill="#797979"/></svg>',
        wallet: '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 5.35333V3.83333C14 3.1 13.4 2.5 12.6667 2.5H3.33333C2.59333 2.5 2 3.1 2 3.83333V13.1667C2 13.9 2.59333 14.5 3.33333 14.5H12.6667C13.4 14.5 14 13.9 14 13.1667V11.6467C14.3933 11.4133 14.6667 10.9933 14.6667 10.5V6.5C14.6667 6.00667 14.3933 5.58667 14 5.35333ZM13.3333 6.5V10.5H8.66667V6.5H13.3333ZM3.33333 13.1667V3.83333H12.6667V5.16667H8.66667C7.93333 5.16667 7.33333 5.76667 7.33333 6.5V10.5C7.33333 11.2333 7.93333 11.8333 8.66667 11.8333H12.6667V13.1667H3.33333Z" fill="#797979"/><path d="M10.6667 9.5C11.219 9.5 11.6667 9.05228 11.6667 8.5C11.6667 7.94772 11.219 7.5 10.6667 7.5C10.1145 7.5 9.66675 7.94772 9.66675 8.5C9.66675 9.05228 10.1145 9.5 10.6667 9.5Z" fill="#797979"/></svg>'
    }
}

function wordEndings(	// Возвращает слово в нужной форме (с правильным окончанием)
    n: number,			// Число
    thing1: string, 	// Форма для числа 1
    thing2: string, 	// Форма для числа 2
    thing5: string 	    // Форма для числа 5
) {
    if (n === 0) return thing5

    let n1 = n % 100,
        n2 = n % 10

    if ((n1 >= 11) && (n1 <= 19)) return thing5
    if ((n2 >= 2) && (n2 <= 4)) return thing2
    if (n2 === 1) return thing1

    return thing5
}