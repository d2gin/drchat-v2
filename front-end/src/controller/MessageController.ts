import Search from "@/components/Search.vue";
import ContactItem from "@/components/ContactItem.vue";
import Controller from "@/interface/Controller";
import {isMobile} from "@/lib/Util";
import {Router, useRouter} from "vue-router";
import {computed, onMounted, ref, watch} from "vue";
import {Store, useStore} from "vuex";

export default class MessageController implements Controller {
    protected components = {Search, ContactItem};
    protected router!: Router;
    protected store!: Store<any>;

    setup() {
        this.router = useRouter();
        this.store = useStore();
        const is_mobile = computed(() => isMobile());
        onMounted(() => {
            this.store.dispatch('Conversation/refreshConversationList', !this.store.state.Conversation.conversationList.length);
        });
        watch(is_mobile, (v1, v0) => this.router.push('/message'));
        return {
            pick: this.pick.bind(this),
            conversationList: computed(() => this.store.state.Conversation.conversationList),
            is_mobile,
            selected: this.selected.bind(this),
            handleListScroll: this.handleListScroll.bind(this),
        };
    }

    public pick(v: any) {
        if (isMobile()) {
            this.router.push(`/mobile-message?conversation_id=${v.id}`);
            return;
        }
        return this.router.push(`/message?conversation_id=${v.id}`);
    }

    public handleListScroll(e: any) {
        if (e.target.scrollHeight - e.target.offsetHeight == e.target.scrollTop) {
            this.store.dispatch('Conversation/paginateConversationList', this.store.state.Conversation.page + 1);
        }
    }

    public selected(item: any) {
    }
}
