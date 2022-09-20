import Controller from "@/interface/Controller";
import User from "@/lib/Passport";
import {message} from "ant-design-vue";
import Passport from '@/api/Passport';
import {reactive} from "vue";
import {Router, useRouter} from "vue-router";
import {Store, useStore} from "vuex";
import {UnwrapNestedRefs} from "@vue/reactivity";

export default class LoginController implements Controller {
    protected router!: Router;
    protected store!: Store<any>;
    protected userform!: UnwrapNestedRefs<{ username: string, password: string }>;

    setup(props: any, context: any): any {
        this.store = useStore();
        this.router = useRouter();
        this.userform = reactive({
            username: '',
            password: '',
        });
        return {
            userform: this.userform,
            focusBT: this.focusBT.bind(this),
            blurBT: this.blurBT.bind(this),
            login: this.login.bind(this),
        };
    }

    protected focusBT = (e: any) => {
        e.target.parentNode.parentNode.style['border-bottom-color'] = '#8EE6C3';
    };
    protected blurBT = (e: any) => {
        e.target.parentNode.parentNode.style['border-bottom-color'] = '#F1F1F1';
    };
    protected login = () => {
        Passport.loading(true).login(this.userform.username, this.userform.password).then((res: any) => {
            message.success(res.message);
            User.setToken(res.data.token);
            this.store.dispatch('User/refreshToken');
            this.router.push('/');
        });
    };
}
