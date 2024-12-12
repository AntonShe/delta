import Api, { returnDefault } from "./api";
import { ICart } from "../type";

class CartApi extends Api {
    constructor() {
        super('/cart');
    }

    getCart(): Promise<ICart> {
        return this.get('/get-cart');
    }

    async getTotalCart(): Promise<number> {
        const res = await this.get('/get-total-count');
        return res.total;
    }

    updateItemCart(id: number, quantity: number): Promise<returnDefault> | null {
        if (!id || !quantity) {
            return null;
        }

        return this.patch('/quantity', { data: { id, quantity } });
    }

    addToCart(productId: number, quantity: number): Promise<returnDefault> | null {
        if (!productId || !quantity) {
            return null;
        }

        return this.post('/add-to-cart', { data: { productId, quantity } });
    }

    removeItemsCart(ids: number[]): Promise<returnDefault> | null {
        if (!ids || ids.length === 0) {
            return null;
        }

        return this.delete('/delete', { data: { ids } });
    }

}

export default new CartApi();
