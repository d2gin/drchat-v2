import axios, {Axios, AxiosRequestConfig, AxiosRequestHeaders, AxiosResponse, CanceledError} from "axios";
import Config from "@/lib/Config";
import {message} from 'ant-design-vue';
import {STATUS_NEED_LOGIN} from "@/lib/StatusCode";
import router from "@/router";
import UserModel from "@/lib/Passport";
import Passport from "@/lib/Passport";
import store from "@/store";

export class Http {
    public instance!: Axios;
    protected abortController!: AbortController;
    protected config: any = {
        loading: false,
        requesting: '',
    };

    constructor() {
        this.instance = axios.create({
            baseURL: Config.api,
            timeout: 60000,
            headers: {},
        });
        // 拦截器
        this.instance.interceptors.request.use((config: AxiosRequestConfig) => {
            config.headers!.token = UserModel.getToken();
            return config;
        });
        this.instance.interceptors.response.use((response: AxiosResponse) => {
            if (response.data.code == STATUS_NEED_LOGIN) {
                Passport.logout();
                router.push('/login');
                return Promise.reject(response.data);
            } else if (response.data.code < 0) {
                message.error(response.data.message);
                return Promise.reject(response.data);
            }
            return response.data;
        });
    }

    loading(v: any = true) {
        this.config.loading = v;
        return this;
    }

    get(url: string, params: object | URLSearchParams | null = null) {
        return this.request({url, params});
    }

    post(url: string, data?: object) {
        return this.request({
            url,
            method: "POST",
            data,
        });
    }

    request(option: AxiosRequestConfig | string) {
        if (typeof option == "string") {
            option = {url: option,};
        }
        this.abortController = new AbortController();
        option.signal = this.abortController.signal;
        let loading = () => {
        };
        if (typeof this.config.loading == "string") {
            loading = message.loading(this.config.loading, 0);
        } else if (typeof this.config.loading == "boolean" && this.config.loading) {
            loading = message.loading('请稍候...', 0);
        }
        this.config.requesting = option.url;
        return this.instance.request(option).finally(loading).finally(() => this.reset());
    }

    abort(url: string | null = null) {
        if (url === null || url === this.config.requesting) {
            this.abortController?.abort();
        }
        return this;
    }

    private reset() {
        this.config = {
            loading: false,
            requesting: '',
        };
    }
}

export default (new Http());

function e(arg0: typeof CanceledError, e: any) {
    throw new Error("Function not implemented.");
}

