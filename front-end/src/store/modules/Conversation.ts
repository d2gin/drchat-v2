import ChatConversation from "@/api/ChatConversation";
import {debounce} from "@/lib/Util";

export default {
    namespaced: true,
    state: {
        conversationList: [],
        hintCount: 0,
        page: 1,
    },
    getters: {},
    mutations: {
        setConversationList(state: any, v: any) {
            state.conversationList = v;
        },
        setHintCount(state: any, v: any) {
            state.hintCount = v;
        },
        setPage(state: any, page: any) {
            state.page = page;
        }
    },
    actions: {
        /**
         * 刷新会话列表 会话列表属于全局状态
         * @param commit
         * @param loading
         */
        refreshConversationList({commit}: any, loading = false) {
            debounce(() => {
                ChatConversation.loading(loading).list().then((res: any) => {
                    let hintCount = 0;
                    let list = res.data.map((v: any) => {
                        hintCount += v.unread;
                        return {
                            ...v,
                            avatar: v.object_info?.avatar,
                            realname: v.object_info?.title || '',
                            breif: v.record?.brief || '',
                        };
                    });
                    commit('setPage', 1);
                    commit('setHintCount', hintCount);
                    commit('setConversationList', list);
                });
            }).apply(this);
        },
        paginateConversationList({commit, state}: any, page = 1) {
            debounce(() => {
                ChatConversation.loading(true).list(page).then((res: any) => {
                    let hintCount = 0;
                    let list = res.data.map((v: any) => {
                        hintCount += v.unread;
                        return {
                            ...v,
                            avatar: v.object_info?.avatar,
                            realname: v.object_info?.title || '',
                            breif: v.record?.brief || '',
                        };
                    });
                    commit('setPage', page);
                    commit('setHintCount', hintCount);
                    commit('setConversationList', [...state.conversationList, ...list]);
                });
            }).apply(this);
        },
        /**
         * 获取未读信息数
         * @param commit
         */
        refreshHintCount({commit}: any) {
            debounce(() => {
                ChatConversation.loading(false).hintCount().then((res: any) => commit('setHintCount', res.data));
            }).apply(this);
        }
    },
}
