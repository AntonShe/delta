import Api, { returnDefault } from "./api";
import { IUser, IUserDetail, IUserBadge, IOrder } from "../type";

interface sendPinProps {
    email?: string;
    phone?: string;
    isUpdate: number;
}

class UserApi extends Api {
    constructor() {
        super('');
    }

    getUser(): Promise<IUser> {
        return this.get('/user/get-user-info');
    }

    //почти тоже самое что и getUser, вопрос нужен ли
    getUserBadge(): Promise<IUserBadge> {
        return this.get('/user/get-user-badge');
    }

    updateUser(data: Partial<IUserDetail & IUser>): Promise<returnDefault> | null {
        if (!data) {
            return null;
        }

        return this.patch('/customer', { data });
    }

    getUserOrders(data?: Record<string, number | string>): Promise<IOrder> {
        return this.get('/user/get-orders', { data });
    }

    sendPin(data: sendPinProps): Promise<any> | null {
        if (!data) {
            return null;
        }

        return this.post('/user/send-pin', { data });
    }

    verifyPin(data: any): Promise<any> | null {
        if (!data) {
            return null;
        }

        return this.post('/user/verify-pin', { data });
    }
}

export default new UserApi();
