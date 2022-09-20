export default {
    namespaced: true,
    state: {
        siteTitle: '',
        clientWidth: 0,
        clientHeight: 0,
    },
    getters: {},
    mutations: {
        setClientWidth(state: any, v: number | string) {
            state.clientWidth = v;
        },
        setClientHeight(state: any, v: number | string) {
            state.clientHeight = v;
        }
    },
    actions: {},
}