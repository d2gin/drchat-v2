import User from "@/api/User";
import Passport from "@/lib/Passport";
import {debounce} from "@/lib/Util";

export default {
    namespaced: true,
    state: {
        info: null,
        token: null,
    },
    getters: {},
    mutations: {
        setInfo(state: any, info: any) {
            state.info = info;
        },
        setToken(state: any, token: string) {
            state.token = token;
        },
    },
    actions: {
        refreshInfo({commit}: any) {
            debounce(() => {
                User.info().then((res: any) => {
                    commit('setInfo', res.data);
                });
            }).apply(this);
        },
        refreshToken({commit}: any) {
            commit('setToken', Passport.getToken());
        }
    },
}
