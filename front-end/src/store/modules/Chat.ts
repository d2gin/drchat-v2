import {Socketio} from "@/lib/Socket/Socketio";
import Chat from "@/api/Chat";

export default {
    namespaced: true,
    state: {
        expressions: [],
        expressionMap: {},
    },
    getters: {},
    mutations: {
        setExpressions(state: any, list: any) {
            state.expressions = list;
            list.forEach((v: any) => state.expressionMap[v.name] = v.path);
        }
    },
    actions: {
        refreshExpressions({commit}: any, loading = false) {
            Chat.loading(loading).expressions().then((res: any) => {
                commit('setExpressions', res.data.list);
            });
        }
    },
}
