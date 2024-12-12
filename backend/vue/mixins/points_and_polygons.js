import axios from "axios";

export const points_and_polygons = {
    data() {
        return {
            city_name: null,
            id_region: 77,
            receive_points: [],
            pickup_points: [],
            polygons: [],
            citiesTo: {
                list: [],
                info: []
            },
            citiesFrom: []
        }
    },
    methods: {
        getPointsAndPolygons() {
            this.receive_points = []
            this.pickup_points = []
            this.polygons = []

            if (!_.isEmpty(this.city_name)) {
                return axios.get(`/admin/backend/pvz/points`)
                    .then(response => {
                        this.receive_points = []
                        this.pickup_points = []
                        this.polygons = []

                        if (response.data.error)
                            throw {message: response.data.error, type: 'registered'}
                        if (response.data) {
                            _.forEach(response.data, point => {
                                if (point.is_receive === 1) {
                                    this.receive_points.push(point)
                                    return
                                }
                                if (point.is_courier) {
                                    _.forEach(JSON.parse(point.courier_polygons), collection_of_coords => {
                                        this.polygons.push(collection_of_coords)
                                    })
                                } else if (point.city_name == this.city_name){
                                    this.pickup_points.push(point)
                                }
                            });
                        }

                        return response.data;
                    }).catch((error) => {
                        if (error.type === 'registered') throw error.message
                        throw 'Ошибка при получение ПВЗ и курьерских зон'
                    });
            }
        }
    }
};