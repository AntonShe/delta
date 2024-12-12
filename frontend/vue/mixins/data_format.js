export const data_format = {
    data() {
        return {

        }
    },
    methods: {
        dataFormatToDaysAndMonth(date) {
            let initialData = new Date(date);
            let days = initialData.getDate();
            let month = initialData.getMonth();
            switch (month) {
                case 0:
                    return `${days} января`;
                case 1:
                    return `${days} февраля`;
                case 2:
                    return `${days} марта`;
                case 3:
                    return `${days} апреля`;
                case 4:
                    return `${days} мая`;
                case 5:
                    return `${days} июня`;
                case 6:
                    return `${days} июля`;
                case 7:
                    return `${days} августа`;
                case 8:
                    return `${days} сентября`;
                case 9:
                    return `${days} октября`;
                case 10:
                    return `${days} ноября`;
                case 11:
                    return `${days} декабря`;

            }
            return `${days} ${month}`;

        },
        dataFormatToHourAndMinutes(date) {
            let initialData = new Date(date);
            let hours = initialData.getHours();
            let minutes = initialData.getMinutes();
            if (minutes < 10) {
                minutes = `0${minutes}`;
            }
            return `${hours}:${minutes}`;
        }
    }
};