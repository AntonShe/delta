import Api, { returnDefault } from "./api";
import { IProduct } from "../type";

interface favoriteReturn {
    pagination: {
        currentPage: number;
        endPage: number;
        pageCount: number;
        startPage: number;
    };
    products: IProduct[];
}

class FavoriteApi extends Api {
    constructor() {
        super('/favorite');
    }

    getFavorite(page: number = 1): Promise<favoriteReturn | Pick<favoriteReturn, "products">> {
        return this.get('/full', {data: {page}});
    }

    addFavorite(productId: number): Promise<returnDefault> | null {
        if (!productId) {
            return null;
        }

        return this.post('', {data: {productId}});
    }

    removeFavorite(productId: number): Promise<returnDefault> | null {
        if (!productId) {
            return null;
        }

        return this.delete('', {data: {productId}});
    }
}

export default new FavoriteApi();
