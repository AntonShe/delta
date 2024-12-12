export interface IProduct {
    id: number;
    labirintId: number;
    active: number;
    voteCount: number;
    weight: number;
    publishingHouseId: number;
    publishingYear: number;
    quantity: number;
    quantityCart: number;
    rating: number;
    oldPrice: number;
    isNew: number;
    isPopular: number;
    pagesNumber: number;
    nds: number;
    price: number;
    priceInOrder: number;
    age_category_id: string
    annotation: string;
    authors: string;
    bindingMaterial: string;
    color: string;
    cover: string;
    isbn: string;
    pageMaterial: string;
    pdf: string;
    publishingHouse: string;
    shortAnnotation: string;
    size: string;
    title: string;
    volumesCount: string;
    isCart: boolean;
    isFavourite: boolean;
    genres: IGenre[];
    ages: IAge[];
    languages: ILanguage[];
    levels: ILevel[];
}

export interface IGenre {
    id: number;
    isCourse: number;
    level: number;
    onMain: number;
    sort: number;
    name: string;
    cover: string;
    description: string;
    parentId: string;
    popular: string;
}

export interface IAge {
    id: number;
    intName: number;
    name: string;
}

export interface ILanguage {
    id: number;
    name: string;
}

export interface ILevel {
    id: number;
    sort: number;
    name: string;
}
