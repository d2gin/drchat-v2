import Controller from "@/interface/Controller";
import ContactItem from "@/components/ContactItem.vue";
import Search from "@/components/Search.vue";
import RequestList from "@/views/Contact/Components/Request/List.vue";
import UserInfo from "@/views/Contact/Components/Request/Info.vue";
import CreateGroup from "@/views/Contact/Components/Group/Create.vue";
import {computed, onMounted, reactive, Ref, ref, unref, watch} from "vue";
import Friend from "@/api/Friend";
import {Store, useStore} from "vuex";
import {Router, useRouter} from "vue-router";
import {isMobile} from "@/lib/Util";
import ChatConversation from "@/api/ChatConversation";

export default class ContactController implements Controller {
    protected components = {
        ContactItem,
        Search,
        RequestList,
        UserInfo,
        CreateGroup,
    };

    protected store!: Store<any>;
    protected router!: Router;
    protected searchInfo: Ref = ref();
    protected friendList: Ref = computed(() => this.store.state.Contact.friendList);
    protected groupList: Ref = computed(() => this.store.state.Contact.groupList);

    setup(props: any, context: any): any {
        this.store = useStore();
        this.router = useRouter();
        watch(isMobile, () => this.router.push('/contact'));
        onMounted(this.mounted.bind(this));

        // 向模板提供数据
        return {
            friendList: this.friendList,
            groupList: this.groupList,
            newFriendItem: reactive({
                avatar: require('../assets/newfriend.png'),
                realname: '新的朋友',
                unread: computed(() => this.store.state.Contact.hintCount),
            }),
            createGroupItem: reactive({
                avatar: require('../assets/new_group.png'),
                realname: '创建群聊',
            }),

            pickFriend: this.pickFriend.bind(this),
            pickGroup: this.pickGroup.bind(this),
            handleSearch: this.handleSearch.bind(this),
            showRequestList: ref(false),
            showCreateGroupForm: ref(false),
            searchInfo: this.searchInfo,
        };
    }

    protected mounted() {
        this.store.dispatch('Contact/refreshHintCount');
        if (!unref(this.friendList).length) {
            Friend.loading().list().then((res: any) => {
                // 写入状态
                this.store.commit('Contact/setFriendList', res.data.map((v: any) => {
                    return {...v, ...v.friend};
                }));
            });
        }
        if (!unref(this.groupList).length) {// 刷新群聊列表
            this.store.dispatch('Contact/refreshGroupList');
        }
    }

    /**
     * 用户搜索 按用户名精准搜索
     * @param keywords
     */
    protected handleSearch(keywords: string) {
        Friend.loading().preciseSearch(unref(keywords)).then((res: any) => this.searchInfo.value = res.data);
    }

    /**
     * 展示好友资料
     * @param item
     */
    protected pickFriend(item: any) {
        this.router.push({name: isMobile() ? 'mobile-friend-info' : 'contact', query: {friend_id: item.friend_id}});
    }

    /**
     * 点击群聊直接跳转聊天页
     * @param item
     */
    protected pickGroup(item: any) {
        ChatConversation.loading(true).createConversation(item.group_id, true).then((res: any) => {
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
