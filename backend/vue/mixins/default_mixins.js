export const default_mixins = {
    data() {
        return {

        }
    },
    methods: {
        funcWordEnding: function (value, thing1, thing2, thing5) {
            if (value === 0) {
                return thing5;
            }
            let n1 = value % 100;
            let n2 = value % 10;

            if (n1 >= 11 && n1 <= 19) {
                return thing5;
            } else if (n2 >= 2 && n2 <= 4) {
                return thing2;
            } else if (n2 === 1) {
                return thing1;
            }
            return thing5;
        },
        humanizeNumber: function (number) {
            let string = '';
            while (number != 0) {
                let digits;
                if (number > 1000){
                    digits = (number % 1000 < 100 ? '0' : '')
                        + (number % 1000 < 10 ? '0' : '')
                        + number % 1000;
                } else {
                    digits = number % 1000;
                }
                string = digits + ' ' + string;
                number = Math.floor(number / 1000);
            }
            return string;
        },
    }
};