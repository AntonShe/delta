import AbstractBalloon from "./AbstractBalloon";

export default class BaseBalloonCourier extends AbstractBalloon {
    render(
        data: {
            // REQUIRED
            isIncludedInPolygon: boolean
            isExactAddress: boolean

            // OPTIONAL
            streetAndHouse?: string
            locality?: string
        }
    ): string {
        let infoDeliveryAvailability = ''
        if (data.isExactAddress) {
            infoDeliveryAvailability = data.isIncludedInPolygon
                ? `<div class="delivery-availability delivery-availability_available">
                        ${this.svgCheckMark}
                        <p class="delivery-availability__text">Доступно для доставки</p>
                    </div>`
                : `<div class="delivery-availability delivery-availability_not-available">
                        ${this.svgCross}
                        <p class="delivery-availability__text">Недоступно для доставки</p>
                    </div>`
        }

        let address = ''
        if (data.streetAndHouse && data.locality) {
            address = data.isExactAddress
                ? `<p>${data.streetAndHouse}</p><p>${data.locality}, Россия</p>`
                : `<p>Мы не можем доставить заказ по адресу: Россия, ${data.locality}, ${data.streetAndHouse}. 
                    Выберите конкретный адрес с номером дома.</p>`
        } else {
            address = '<p>Не удалось определить адрес.</p>'
        }

        if (data.isIncludedInPolygon) {
            return this.style
                + '<div></div>'
                + '<div>Доставить сюда</div>'
        }

        return `${this.style}
                <div class="balloon-zone">
                    ${address}
                    ${infoDeliveryAvailability}
                </div>`
    }

    bindListener(): void {
    }

    unbindListener(): void {
    }

    style: string = `
        <style>
            [class*="-balloon"] {
                border-radius: 16px;
                font-family: Golos-R, sans-serif;
                font-size: 15px;
                line-height: 21px;
            }
            .balloon-zone {
                margin: 6px 4px;
            }
            .delivery-availability {
                margin-top: 4px;
                display: flex;
            }
            .delivery-availability_available {
                color: #00AC1C;
            }
            .delivery-availability_not-available {
                color: #AD0000;
            }
            .delivery-availability__text {
                margin-left: 8px;
            }
            [class*="-balloon__content"] {
                display: flex;
                border-radius: 8px;
                font-family: 'Inter';
                font-size: 14px;
                line-height: 20px;
                font-weight: 600;
                color: #FFFFFF;
                background: #3F3F46;
                height: 36px !important;
                padding: 8px 12px;
            }
        </style>`

    svgCheckMark: string = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.00008 0.666992C4.40008 0.666992 0.666748 4.40033 0.666748 9.00033C0.666748 13.6003 4.40008 17.3337 9.00008 17.3337C13.6001 17.3337 17.3334 13.6003 17.3334 9.00033C17.3334 4.40033 13.6001 0.666992 9.00008 0.666992ZM9.00008 15.667C5.32508 15.667 2.33341 12.6753 2.33341 9.00033C2.33341 5.32533 5.32508 2.33366 9.00008 2.33366C12.6751 2.33366 15.6667 5.32533 15.6667 9.00033C15.6667 12.6753 12.6751 15.667 9.00008 15.667ZM12.8251 5.31699L7.33342 10.8087L5.17508 8.65866L4.00008 9.83366L7.33342 13.167L14.0001 6.50033L12.8251 5.31699Z" fill="#00AC1C"/></svg>'

    svgCross: string = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.00008 0.666992C4.39175 0.666992 0.666748 4.39199 0.666748 9.00033C0.666748 13.6087 4.39175 17.3337 9.00008 17.3337C13.6084 17.3337 17.3334 13.6087 17.3334 9.00033C17.3334 4.39199 13.6084 0.666992 9.00008 0.666992ZM9.00008 15.667C5.32508 15.667 2.33341 12.6753 2.33341 9.00033C2.33341 5.32533 5.32508 2.33366 9.00008 2.33366C12.6751 2.33366 15.6667 5.32533 15.6667 9.00033C15.6667 12.6753 12.6751 15.667 9.00008 15.667ZM11.9917 4.83366L9.00008 7.82533L6.00841 4.83366L4.83342 6.00866L7.82508 9.00033L4.83342 11.992L6.00841 13.167L9.00008 10.1753L11.9917 13.167L13.1667 11.992L10.1751 9.00033L13.1667 6.00866L11.9917 4.83366Z" fill="#AD0000"/></svg>'
}