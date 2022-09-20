import Controller from "@/interface/Controller";
import Message from "@/components/Message.vue";
import SendBox from "@/components/SendBox.vue";
import Members from "@/views/Message/Group/Members.vue";
import {CloseOutlined} from "@ant-design/icons-vue";
import {isMobile, throttle} from "@/lib/Util";
import {Socketio, useSocketio} from "@/lib/Socket/Socketio";
import {Store, useStore} from "vuex";
import {RouteLocationNormalized, Router, useRoute, useRouter} from "vue-router";
import {nextTick, onMounted, reactive, ref, Ref, unref, watch} from "vue";
import {UnwrapNestedRefs} from "@vue/reactivity";
import Chat from "@/api/Chat";
import ChatConversation from "@/api/ChatConversation";
import {message, Modal} from "ant-design-vue";

export default class MessageBoxController implements Controller {
    protected socketio!: Socketio;
    protected store!: Store<any>;
    protected router!: Router;
    protected route!: RouteLocationNormalized;
    protected messageRecords!: Ref<any[]>;
    protected conversation!: Ref;
    protected conversationId: Ref<number> = ref(0);
    protected lastScrollHeight!: Ref<number>;
    protected sendBox: Ref = ref();
    protected messageContentBox: Ref = ref();
    protected app!: UnwrapNestedRefs<any>;

    protected components = {
        CloseOutlined,
        SendBox,
        Message,
        Members,
    };

    setup(props: any, context: any): any {
        this.socketio = useSocketio();
        this.store = useStore();
        this.router = useRouter();
        this.route = useRoute();
        this.messageRecords = ref([]);
        this.conversation = ref(<any>{});
        this.app = reactive({
            page: 1,
            has_more: true,
            has_new: false,
            loading: false,
            initializing: false,
            sending_message: false,
        });
        this.lastScrollHeight = ref(0);

        // 观察器
        watch(() => this.route.query.conversation_id, (v: any) => this.initData());
        // 钩子
        onMounted(this.mounted.bind(this));
        return {
            app: this.app,
            refs: ref([]),
            messageRecords: this.messageRecords,
            conversationId: this.conversationId,
            conversation: this.conversation,
            isMobile,
            sendBox: this.sendBox,
            messageContentBox: this.messageContentBox,
            showGroupMembers: ref(false),

            handleSend: throttle(this.handleSend.bind(this)),
            handleVideoChat: throttle(this.handleVideoChat.bind(this)),
            handleRedDot: throttle(this.handleRedDot.bind(this)),
            scrollit: this.scrollit.bind(this),
            loadNewMessage: throttle(this.loadNewMessage.bind(this)),
            loadMoreMessage: this.loadMoreMessage.bind(this),
        };
    }

    /**
     * 初始化数据
     */
    initData() {
        this.conversationId.value = <any>this.route.query.conversation_id;
        this.app.page = 1;
        this.app.has_more = true;
        this.messageRecords.value = [];
        this.conversation.value = {};
        if (unref(this.conversationId)) {
            this.app.initializing = true;
            // 加载聊天记录
            this.loadMessage(this.app.page).then((res: any) => {
                this.messageRecords.value = res?.data?.list;
                nextTick(() => {
                    this.scrollBottom();
                    this.updateScrollHeight();
                    this.app.initializing = false;
                });
            });
        }
    }

    /**
     * 页面挂载
     */
    protected mounted() {
        this.conversationId.value = <any>this.route.query.conversation_id;
        if (this.conversationId.value) {
            this.initData();
        }
        // 监听
        this.socketio.off('chat_new_message').on('chat_new_message', (data: any) => {
            if (
                !this.conversationId.value
                || (this.conversation.value.object_id != data.receiver && data.scene == ChatConversation.CONVERSATION_GROUP)
                || data.sender == this.store.state.User.info.id
            ) {
                return;
            }
            this.messageRecords.value.push({
                avatar: data.sender_info?.avatar,
                content: data.content,
                direction: 'forward',
            });
            if (!this.isBrowsing()) {
                nextTick(() => this.scrollBottom());
            }
            const isGroup = this.conversation.value.object_type == ChatConversation.CONVERSATION_GROUP;
            ChatConversation.clearUnread(isGroup ? this.conversation.value.object_id : data.sender, isGroup);
        });
    }

    protected handleVideoChat() {
        if (this.conversation.value.object_type == ChatConversation.CONVERSATION_NORMAL) {
            this.router.push({name: 'video-chat', query: {friend_id: this.conversation.value.object_id}});
        }
    }

    /**
     * 发送消息
     * @param content
     */
    protected sendMessage(content: string) {
        if (this.app.sending_message) {
            message.warn('上一条消息正在发送');
            return Promise.resolve();
        }
        this.app.sending_message = true;
        if (this.sendBox.value) {
            unref(this.sendBox).clear();
        }
        let isGroup = unref(this.conversation).object_type == ChatConversation.CONVERSATION_GROUP;
        return Chat.loading(true).send(unref(this.conversation).object_id, content, isGroup).then((res: any) => {
            this.store.dispatch('Conversation/refreshConversationList');
            message.success(res.message);
        }).finally(() => this.app.sending_message = false);
    }

    /**
     * 发送点击事件
     * @param content
     */
    protected handleSend(content: string) {
        if (this.app.initializing) {
            message.warn('等待数据加载完成');
            return false;
        } else if (!content) {
            message.error('内容不能为空');
            return false;
        }
        let payload = reactive({
            content,
            avatar: this.store.state.User.info.avatar,
            red_dot: false,
            direction: 'reverse',
            sender: this.store.state.User.info.id,
            receiver: this.conversation.value.object_id,
            id: (new Date()).getTime(),
        });
        this.messageRecords.value.push(payload);
        this.sendMessage(content).catch(() => payload.red_dot = true);
        nextTick(() => this.scrollBottom());
    }

    /**
     * 消息框红色感叹号点击事件
     * @param data
     */
    protected handleRedDot(data: any) {
        Modal.confirm({
            content: '重新发送消息?',
            okText: '确认',
            cancelText: '取消',
            onOk: throttle(() => {
                this.sendMessage(data.content).then(() => {
                    this.messageRecords.value = unref(this.messageRecords).map((v: any) => {
                        let red_dot = v.red_dot;
                        if (v.id === data.id) red_dot = false;
                        return {...v, red_dot,};
                    });
                });
            }),
        });
    }

    /**
     * 加载消息记录
     * @param page
     * @param limit
     */
    protected loadMessage(page: number, limit = 30) {
        if (this.app.loading) {
            return Promise.resolve();
        }
        this.app.loading = true;
        return Chat.loading().records(unref(this.conversationId), page, limit).then((res: any) => {
            this.conversation.value = res.data.conversation;
            if (!res.data.list.length) {
                this.app.has_more = false;
            }
            res.data.list = res.data.list.map((v: any) => {
                return {
                    ...v,
                    avatar: v.sender_info.avatar,
                    realname: v.sender_info.realname,
                    content: v.record.content,
                };
            });
            this.store.dispatch('Conversation/refreshConversationList');
            //this.store.dispatch('Conversation/refreshHintCount');
            return Promise.resolve(res);
        }).finally(() => this.app.loading = false);
    }

    protected loadNewMessage() {
    }

    /**
     * 加载更多
     */
    protected loadMoreMessage() {
        this.loadMessage(++this.app.page).then((res: any) => {
            this.messageRecords.value = [...res.data.list, ...unref(this.messageRecords)];
            // 保持当前浏览的位置
            nextTick(() => {
                let oldScHeight = this.lastScrollHeight.value;
                this.updateScrollHeight();
                let newScHeight = this.lastScrollHeight.value;
                let diffHeight = newScHeight - oldScHeight;
                if (diffHeight > 30) this.scrollTo(diffHeight - 30);
            });
        }, () => this.app.page--).catch(() => this.app.page--);
    }

    /**
     * 滚动条事件
     * @param e
     */
    protected scrollit(e: any) {
        if (e.target.scrollTop == 0 && this.app.has_more) {
            this.loadMoreMessage();
        }
    }

    /**
     * 是否在浏览前面的内容
     */
    protected isBrowsing() {
        if (!this.messageContentBox.value) return false;
        const scrollTop = this.messageContentBox.value.scrollTop;
        const scrollHeight = this.messageContentBox.value.scrollHeight;
        const offsetHeight = this.messageContentBox.value.offsetHeight;
        const ableScroll = scrollHeight - offsetHeight;
        return scrollTop < (ableScroll - (offsetHeight / 2));
        // return scrollTop < scrollHeight - offsetHeight;
    }

    protected scrollBottom() {
        if (!this.messageContentBox.value) return false;
        this.scrollTo(this.messageContentBox.value.scrollHeight);
    }

    /**
     * 滚动
     * @param height
     */
    protected scrollTo(height: number) {
        if (!this.messageContentBox.value) return false;
        this.messageContentBox.value.scrollTop = height;
    }

    /**
     * 记录当前的滚动条高度
     */
    protected updateScrollHeight() {
        if (!this.messageContentBox.value) return false;
        this.lastScrollHeight.value = this.messageContentBox.value.scrollHeight;
    }
}
