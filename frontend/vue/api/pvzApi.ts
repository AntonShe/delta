import Api from "./api";
import { IPvz } from "../type";

class PvzApi extends Api {
    constructor() {
        super('');
    }

    getPvz(): Promise<IPvz[]> {
        return this.get('/admin/backend/pvz/points');
    }
}

export default new PvzApi();
