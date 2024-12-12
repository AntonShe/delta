import { IDelivery } from "./delivery";
import { IProduct } from "./product";
import { IUserDetail } from "./user";

export interface IOrder {
    managerComment: string;
    storageDate: string;
    paymentType: number;
    transaction: [] | ITransaction;
    subUser: [] | ISubUser;
    delivery: IDelivery;
    products: IProduct[];
    status: IStatus[];
    user: IUserDetail
}

export interface IStatus {
    id: number;
    status: number;
    statusPayment: number;
    userId: number;
    dateUpdate: string;
    date_create: string;
    date_storage: string;
    deliveryDate: string;
    deliveryProfileId: number;
    getterName: string;
    getterPhone: string;
    managerComment: string;
    managerId: string | number;
    orderNumber: number;
    orderPrice: number;
    paymentType: number;
    possibleDeliveryDate: string;
    sessionKey: string;
}

export interface ISubUser {
    subFirstName?: string;
    subLastName?: string;
}

export interface ITransaction {
    isPending: boolean;
    trans_id: string
}
