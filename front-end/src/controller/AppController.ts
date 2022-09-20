import {Store, useStore} from "vuex";
import {computed, onMounted, Ref, ref, watch} from "vue";
import {Socketio, useSocketio} from "@/lib/Socket/Socketio";
import Controller from "@/interface/Controller";
import Subcribe from "@/lib/Socket/Subcribe";
import {playMessageAudio} from "@/lib/Util";
import Friend from "@/api/Friend";
import {Modal} from "ant-design-vue";
import {RouteLocationNormalizedLoaded, Router, useRoute, useRouter} from "vue-router";

export default class AppController implements Controller {
    protected store!: Store<any>;
    protected socketio!: Socketio;
    protected userinfo!: Ref;
    protected router!: Router;
    protected route!: RouteLocationNormalizedLoaded;

    setup() {
        this.router = useRouter();
        this.route = useRoute();
        this.store = useStore();
        this.socketio = useSocketio();
        this.userinfo = computed(() => this.store.state.User.info);

        this.listenSocketEvents();
        this.connectSocket();
        onMounted(this.mounted.bind(this));
        return {};
    }

    /**
     * 页面挂载
     */
    public mounted() {
        console.log('App mounted');
        watch(this.userinfo, v => {
            this.store.dispatch('User/refreshToken');
            this.store.dispatch('Contact/refreshGroupList');
            this.store.dispatch('Chat/refreshExpressions');
        }, {immediate: true});
        this.setClientWidthAndHight();
        window.onresize = this.setClientWidthAndHight.bind(this);
    }

    /**
     * 连接socket
     */
    public connectSocket() {
        watch(this.userinfo, v => {
            if (v !== null) {
                this.socketio.connect({
                    query: {token: this.store.state.User.token,}
                });
            } else {
                this.socketio.disconnect();
            }
        });
    }

    /**
     * 监听socket事件
     */
    public listenSocketEvents() {
        // 连接成功
        this.socketio.onDefault('connect', () => {
            // 订阅私聊频道
            Subcribe.normalChatChannel();
            // 订阅群聊频道
            Subcribe.groupChatChannel();
        });
        // 新消息
        this.socketio.onDefault('chat_new_message', (data: any) => {
            this.store.dispatch('Conversation/refreshConversationList');
            // this.store.dispatch('Conversation/refreshHintCount');
            if (data.sender != this.store.state.User.info.id) {
                playMessageAudio();
            }
        });
        // 好友请求
        this.socketio.onDefault('friend_new_request', (data: any) => {
            this.store.dispatch('Contact/refreshHintCount');
            playMessageAudio();
        });
        // 好友通过请求
        this.socketio.onDefault('friend_request_pass', (data: any) => {
            this.store.dispatch('Contact/refreshFriendList');
            this.store.dispatch('Contact/refreshHintCount');
            if (data.sender != this.store.state.User.info.id) {
                playMessageAudio();
            }
        });
        // 刷新会话列表
        this.socketio.onDefault('refresh_conversation', (data: any) => {
            this.store.dispatch('Contact/refreshFriendList');
            this.store.dispatch('Conversation/refreshConversationList');
        });
        // 视频通话
        this.socketio.onDefault('video-event', (data: any) => {
            switch (data.event) {
                case 'call-up':
                    if (this.route.name == 'video-chat') return;
                    let sender = data.data.sender;
                    let friendInfo = this.store.state.Contact.friendList.find((info: any) => info.friend_id == sender);
                    if (!friendInfo) {
                        friendInfo = Friend.info(sender).then((res: any) => Promise.resolve(res.data));
                    } else {
                        friendInfo = Promise.resolve(friendInfo);
                    }
                    friendInfo.then((info: any) => {
                        Modal.confirm({
                            title: `${info.realname} 邀请您 ${(data.data.type == 'video' ? '视频' : '语音')}聊天`,
                            okText: '接听',
                            cancelText: '拒绝',
                            icon: 'phone',
                            onCancel: () => {
                                this.socketio.emit('video-event-publish', {
                                    friend_id: sender,
                                    event: 'call-invite-feedback',
                                    data: {
                                        sender: this.store.state.User.info.id,
                                        result: 'reject'
                                    },
                                });
                            },
                            onOk: () => {
                                this.socketio.emit('video-event-publish', {
                                    friend_id: sender,
                                    event: 'call-invite-feedback',
                                    data: {
                                        sender: this.store.state.User.info.id,
                                        result: 'accept'
                                    },
                                });
                                this.router.push({
                                    name: 'video-chat',
                                    query: {
                                        friend_id: sender,
                                        accept: 1,
                                    }
                                });
                            },
                        });
                    });
                    break;
            }
        });
    }

    /**
     * 记录客户端可视区域长宽
     */
    public setClientWidthAndHight() {
        this.store.commit('Client/setClientWidth', document.documentElement.clientWidth);
        this.store.commit('Client/setClientHeight', document.documentElement.clientHeight);
    }
}
