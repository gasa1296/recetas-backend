import axios from "axios";

const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

interface ApiProps {
    endpoint: string;
    method?: string;
    _data?: any;
    tokenUse?: boolean;
    params?: any;
    urlParams?: number | string;
    baseUrlNew?: string;
}

export const Api = (props: ApiProps) => {
    const { endpoint, method, _data, tokenUse, params, urlParams } = {
        method: "GET",
        _data: {},
        tokenUse: false,
        params: {},
        urlParams: "",
        ...props,
    };

    const instance = axios.create({
        baseURL: `${baseUrl}/api`,
        method: method,
        headers: {
            "Content-Type": "application/json",
        },
    });

    // Add a request interceptor
    instance.interceptors.request.use(
        async (config) => {
            const token: string | null = await localStorage.getItem(
                "sessionToken"
            );

            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        },
        function (error) {
            return Promise.reject(error);
        }
    );

    return instance.request({
        data: Object.keys(_data).length !== 0 ? _data : undefined,
        params: Object.keys(params).length !== 0 ? params : undefined,
        url: urlParams ? `${endpoint}/${urlParams}` : endpoint,
    });
};
