import { ICompany } from "./company";

export interface IUser {
    birthday: string;
    email: string;
    firstName: string;
    lastName: string;
    secondName: string;
    phone: string;
    isLegal: number;
    canSwitchToLegal: boolean;
    isPasswordExist: boolean;
    isPayerSameGetter: boolean;
    sex: boolean;
    company: ICompany[];
}

export interface IUserDetail {
    bitrix_id: string;
    date_create: string;
    date_update: string;
    email: string;
    firstName: string;
    id: number;
    isActive: number;
    isEmployee: boolean;
    lastName: string;
    password: string;
    phone: string;
    role?: any;
    secondName: string;
    sessionKey: string;
    userType: number;
    profile: ICompany[];
}

export interface IUserBadge {
    badge: [] | {
        email: string;
        fio: string;
        phone: string;
    };
    isGuest: boolean;
}
