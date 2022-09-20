import Controller from "@/interface/Controller";
import NavItem from "@/views/Index/Components/NavItem.vue";

import {isMobile, playMessageAudio} from '@/lib/Util';
import {computed, watch, ref, reactive, onMounted, Ref, unref} from 'vue';
import {Store, useStore} from "vuex";
import {Socketio, useSocketio} from "@/lib/Socket/Socketio";
import {UnwrapNestedRefs} from "@vue/reactivity";
import {message, Modal} from "ant-design-vue";
import Passport from "@/lib/Passport";
import {Router, useRouter} from "vue-router";

export default class MainController implements Controller {
    protected components = {
        NavItem,
    };
    protected socketio!: Socketio;
    protected store!: Store<any>;
    protected router!: Router;
    protected userinfo = computed(() => this.store.state.User.info);
    protected menu!: UnwrapNestedRefs<any>;

    setup(props: any, context: any): any {
        let showSettingMenu = ref(false);
        let is_mobile = computed(() => isMobile());
        this.socketio = useSocketio();
        this.store = useStore();
        this.router = useRouter();
        this.menu = reactive({
            message: {
                path: '/message',
                icon: 'iconfont icon-xiaoxi',
                title: '消息列表',
                unread: computed(() => this.store.state.Conversation.hintCount),
                event: () => {
                },
            },
            contact: {
                path: '/contact',
                icon: 'iconfont icon-tongxunlu',
                title: '通讯录',
                unread: computed(() => this.store.state.Contact.hintCount),
                event: () => {
                }
            },
            me: {
                path: '/me',
                icon: 'iconfont icon-wodexuanzhong',
                title: '我',
                hide: !unref(is_mobile),
                event: () => {
                },
            },
            setting: {
                icon: 'iconfont icon-caidan',
                event: () => {
                    showSettingMenu.value = !unref(showSettingMenu);
                },
                type: 'setting'
            },
        });

        watch(is_mobile, (v: any) => this.menu.me.hide = !v);
        watch(showSettingMenu, (v: any) => {
            let clickOutside = (e: any) => showSettingMenu.value = false;
            if (v) {
                document.addEventListener('click', clickOutside);
            } else {
                document.removeEventListener('click', clickOutside);
            }
        });

        // 钩子
        onMounted(() => {
            //this.store.dispatch('Conversation/refreshHintCount');
            this.store.dispatch('Contact/refreshHintCount');
        });
        return {
            is_mobile,
            showSettingMenu,
            menu: this.menu,
            userinfo: this.userinfo,
            logout: this.logout.bind(this),
        };
    }

    public logout() {
        Modal.confirm({
            title: "确认退出登录？",
            okText: "退出",
            cancelText: "取消",
            centered: true,
            onOk: () => {
                Passport.logout();
                message.success('已退出');
                this.router.push('/login');
            }
        });
    }
}
