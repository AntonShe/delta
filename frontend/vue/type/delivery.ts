export interface IDelivery {
    id: number;
    userId: number;
    latitude: number;
    longitude: number;
    price: number;
    type: number;
    address: string;
    city: string;
    comment: string;
    coordinates: string;
    courierComment: string;
    date_create: string;
    date_update: string;
    entry: string;
    entryCode: string;
    flat: string;
    flor: string;
    userToken: string;
    pointId: string | number;
}
