import Controller from "@/interface/Controller";
import {CloseOutlined} from "@ant-design/icons-vue";
import Userinfo from "@/components/Userinfo.vue";
import Friend from "@/api/Friend";
import {RouteLocationNormalized, Router, useRoute, useRouter} from "vue-router";
import {Store, useStore} from "vuex";
import {computed, onMounted, reactive, Ref, ref, unref, watch} from "vue";
import {message, Modal} from "ant-design-vue";
import ChatConversation from "@/api/ChatConversation";
import {debounce, isMobile, throttle} from "@/lib/Util";

export default class InfoBoxController implements Controller {
    protected route!: RouteLocationNormalized;
    protected router!: Router;
    protected store!: Store<any>;
    protected info!: Ref;
    protected components = {
        CloseOutlined,
        Userinfo,
    };

    setup(props: any, context: any): any {
        this.route = useRoute();
        this.router = useRouter();
        this.store = useStore();
        this.info = ref();
        let friend_id: any = computed(() => this.route.query.friend_id);
        onMounted(() => {
        });
        watch(friend_id, (v: number) => {
            if (v) {
                Friend.loading().info(v).then(
                    (res: any) => this.info.value = reactive({...res.data, ...res.data.friend,})
                );
            } else {
                this.info.value = null;
            }
        }, {immediate: true});

        return {
            info: this.info,
            isMobile: computed(() => isMobile()),
            closeBox: this.closeBox.bind(this),
            handleDelete: throttle(this.handleDelete.bind(this)),
            handleMarkname: this.handleMarkname.bind(this),
            handleContact: throttle(this.handleContact.bind(this)),
        };
    }

    protected handleDelete = () => {
        Modal.confirm({
            title: '删除好友',
            content: "确定删除？",
            okText: "确定",
            cancelText: "再想想",
            onOk: () => {
                return Friend.delete(unref(this.info)?.friend_id).then((res: any) => {
                    this.store.dispatch('Contact/refreshFriendList');
                    message.success(res.message);
                    this.router.push('/contact');
                });
            },
        });
    }

    protected handleMarkname() {
    }

    protected closeBox() {
        this.router.push('/contact');
    }

    protected handleContact(info: any) {
        ChatConversation.loading(true).createConversation(info.friend_id).then((res: any) => {
            this.store.dispatch('Conversation/refreshConversationList');
            this.router.push({
                name: isMobile() ? 'mobile-message' : 'message',
                query: {
                    conversation_id: res.data.conversation_id,
                },
            });
        });
    }
}
