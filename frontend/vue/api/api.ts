export enum METHOD {
    GET = 'GET',
    POST = 'POST',
    PUT = 'PUT',
    PATCH = 'PATCH',
    DELETE = 'DELETE'
}

type Options = {
    data?: any;
    headers?: Record<string, string>;
};

export interface returnDefault {
    status: boolean;
}

type APIMethod = (url: string, options?: Options) => Promise<any | returnDefault>

function queryStringify(data: { [key:string]: string | number }) {
    return `?${Object.entries(data).map((el) => el.join('=')).join('&')}`;
}

export default class Api {
    protected defaultPath: string

    constructor(defaultPath: string) {
        this.defaultPath = defaultPath;
    }

    protected get:APIMethod = (path, options = {}) => {
        const newUrl = options.data ? this.defaultPath + path + queryStringify(options.data) : this.defaultPath + path;
        return this.request(newUrl, METHOD.GET, { headers: options.headers });
    };

    protected post:APIMethod = (path, options = {}) => this.request(this.defaultPath + path, METHOD.POST, { ...options });

    protected put:APIMethod = (path, options = {}) => this.request(this.defaultPath + path, METHOD.PUT, { ...options });

    protected patch:APIMethod = (path, options = {}) => this.request(this.defaultPath + path, METHOD.PATCH, { ...options });

    protected delete:APIMethod = (path, options = {}) => this.request(this.defaultPath + path, METHOD.DELETE, { ...options });

    private async request(url: string, method: METHOD = METHOD.GET, options: Options) {
        const { headers = { 'Content-type': 'application/json' }, data } = options;

        if (document?.querySelector('meta[name="csrf-param"]')?.getAttribute("content")) {
            const requested = document?.querySelector('meta[name="csrf-param"]')?.getAttribute("content");
            headers['X-Requested-With'] = requested ? requested : '';
        }

        if (document?.querySelector('meta[name="csrf-token"]')?.getAttribute("content")) {
            const token = document?.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
            headers['X-CSRF-TOKEN'] = token ? token : '';
        }

        try {
            let response = await fetch(url, {
                method,
                headers,
                body: JSON.stringify(data)
            })

            if (!response.ok) {
                console.log(response)
                throw new Error(`${response.status}, ${response.statusText}`)
            }

            return await response.json();
        } catch (e: any) {
            console.log('Error: ' + e.message);
        }
    }
}
