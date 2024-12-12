import { IProduct } from "./product";

export interface ICart {
    date_create: string;
    date_update: string;
    discount_sum: number;
    final_price: number;
    id: number;
    items: Record<string, IItemsCart>[];
    raw_price: number;
    session_key: string;
    user_id: number;
}

export interface IItemsCart {
    cart_id: number;
    date_create: string;
    date_update: string;
    default_price: number;
    final_price: number;
    id: number;
    isAvailable: boolean;
    productInfo: IProduct;
    product_id: number;
    quantity: IQuantity;
}

export interface IQuantity {
    available: number;
    cart: number;
}