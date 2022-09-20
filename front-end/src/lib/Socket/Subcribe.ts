import store from "@/store";
import $Socketio, {Socketio} from "@/lib/Socket/Socketio";
import {watch} from "vue";

class Subcribe {
    protected socketio: Socketio = $Socketio;
    protected store: any = store;

    constructor() {
    }

    normalChatChannel() {
        watch(() => this.store.state.User.info, (v: any) => {
            if (!v) return;
            // 订阅私聊频道
            this.socketio.io.emit('subcribe', {
                channel: `normal_chat#${this.store.state.User.info.id}`,
                data: {...this.store.state.User.info, token: this.store.state.User.token},
            });
        }, {immediate: true});
    }

    groupChatChannel() {
        if (this.store.state.User.info) {
            // 订阅群聊频道
            watch(() => this.store.state.Contact.groupList, (list: any[]) => {
                list.forEach((group) => {
                    this.socketio.io.emit('subcribe', {
                        channel: `group_chat#${group.group_id}`,
                        data: {group, user_token: this.store.state.User.token},
                    });
                });
            }, {immediate: true});
        }
    }
}

export default new Subcribe();
