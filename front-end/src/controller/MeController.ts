import Controller from "@/interface/Controller";
import {isMobile} from "@/lib/Util";
import {message, Modal} from "ant-design-vue";
import {computed, onMounted, unref, watch} from "vue";
import {Router, useRouter} from "vue-router";
import {Store, useStore} from "vuex";
import Passport from "@/lib/Passport";
import {ComputedRef} from "@vue/reactivity";

export default class MeController implements Controller {
    protected router!: Router;
    protected userInfo!: ComputedRef;
    protected store!: Store<any>;

    setup(props: any, context: any) {
        this.router = useRouter();
        this.store = useStore();
        this.userInfo = computed(() => this.store.state.User.info);
        let is_mobile = computed(() => isMobile());
        watch(is_mobile, (v: any) => {
            if (!v) this.router.push('/message');
        });
        onMounted(() => {
            if (!unref(is_mobile)) {
                this.router.push('/message');
            }
        });
        return {
            is_mobile,
            userInfo: this.userInfo,
            logout: this.logout.bind(this),
        };
    }

    protected logout() {
        Modal.confirm({
            title: '确认退出？',
            okText: '退出',
            cancelText: '取消',
            onOk: () => {
                Passport.logout();
                message.success('已退出');
                this.router.push('/login');
            },
        });
    }

}