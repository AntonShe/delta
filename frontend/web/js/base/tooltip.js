class Tooltip {
    constructor() {
        this.tooltip = document.createElement('span');
        this.name = 'tooltip-info';

        this.tooltip.classList.add(this.name);
        this.tooltip.classList.toggle(`${this.name}--active`, false);

        this.listeners = [];

        document.body.appendChild(this.tooltip);

        this.onHide = this.onHide.bind(this);
    }

    delegate(eventName, element, cssSelector, callback) {
        const fn = event => {
            if (!event.target.matches(cssSelector)) {
                return;
            }

            callback(event);
        };

        element.addEventListener(eventName, fn);
        this.listeners.push({fn, element, eventName});

        return this;
    }

    onShow = (event) => {
        const hoverElemen = event.target;

        this.tooltip.innerHTML = hoverElemen.getAttribute('data-tooltip-label');
        this.tooltip.classList.toggle(`${this.name}--active`, true);

        const hoverElementRect = hoverElemen.getBoundingClientRect();
        const {height: tooltipHeight} = this.tooltip.getBoundingClientRect();

        let coordinateHoverElement = hoverElementRect.top + window.pageYOffset;

        let left = hoverElementRect.left;

        let top = coordinateHoverElement + hoverElemen.clientHeight;

        // если тултип не влезает по высоте, то поднимаем его над элементом
        if (hoverElemen.clientHeight + hoverElementRect.top + tooltipHeight > document.documentElement.clientHeight) {
            top = coordinateHoverElement - tooltipHeight;
        }

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
    }

    onHide() {
        this.tooltip.classList.toggle(`${this.name}--active`, false);
    }

    attach(root) {
        this
            .delegate('mouseover', root, '[data-tooltip-label]', this.onShow)
            .delegate('mouseout', root, '[data-tooltip-label]', this.onHide);

    }

    detach() {
        for (let {fn, element, eventName} of this.listeners) {
            element.removeEventListener(eventName, fn);
        }

    }
}

const tooltip = new Tooltip();
tooltip.attach(document.body);