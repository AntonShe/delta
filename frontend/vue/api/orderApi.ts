import Api from "./api";
import { IDelivery } from "../type";

export interface IDeliveryProfile {
    courier: Omit<IDelivery, 'courierComment'>
    point: Omit<IDelivery, 'courierComment'>
}

class OrderApi extends Api {
    constructor() {
        super('/delivery-profile');
    }

    getCityList(): Promise<string[]> {
        return this.get('/get-city-list');
    }

    getDeliveryProfile(): Promise<IDeliveryProfile> {
        return this.get('/index');
    }
}

export default new OrderApi();
