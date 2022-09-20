import Friend from "@/api/Friend";
import Group from "@/api/Group";
import {debounce} from "@/lib/Util";

export default {
    namespaced: true,
    state: {
        friendList: [],
        groupList: [],
        requests: [],
        hintCount: 0,
    },
    getters: {},
    mutations: {
        setFriendList(state: any, v: any) {
            state.friendList = v;
        },
        setGroupList(state: any, v: any) {
            state.groupList = v;
        },
        setHintCount(state: any, v: any) {
            state.hintCount = v;
        },
    },
    actions: {
        /**
         * 刷新好友列表
         * @param commit
         * @param loading
         */
        refreshFriendList({commit}: any, loading = false) {
            debounce(() => {
                Friend.loading(loading).list().then((res: any) => commit('setFriendList', res.data.map((info: any) => {
                    return {...info.friend, ...info};
                })));
            }).apply(this);
        },
        /**
         * 刷新群聊列表
         * @param commit
         * @param loading
         */
        refreshGroupList({commit}: any, loading = false) {
            debounce(() => {
                Group.loading(loading).list().then((res: any) => commit('setGroupList', res.data.map((info: any) => {
                    return {...info.group, ...info, realname: info.group?.name,};
                })));
            }).apply(this);
        },
        refreshHintCount({commit}: any) {
            debounce(() => {
                Friend.loading(false).hintCount().then((res: any) => commit('setHintCount', res.data));
            }).apply(this);
        },
    },
}
