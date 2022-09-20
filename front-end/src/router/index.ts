import {createRouter, createWebHistory, NavigationGuardNext, RouteLocationNormalized} from 'vue-router'
import routes from "./Routes";
import Passport from "@/lib/Passport";
import store from "@/store";
import User from "@/api/User";
import NProgress from 'nprogress' // 进度条
import 'nprogress/nprogress.css' // 引入样式

NProgress.configure({
    easing: 'ease',
    speed: 500,
    showSpinner: false
})
const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes
});
// 守卫
router.beforeEach(async (to: RouteLocationNormalized, from: RouteLocationNormalized) => {
    NProgress.start();
    if (!Passport.isLogin() && !to.meta.access && to.name != 'login') {
        return '/login';
    }
    NProgress.inc(0.1);
    // 用户资料
    // @ts-ignore
    if (!store.state.User.info && to.name != 'login') {
        store.commit('User/setInfo', (await User.loading(false).info()).data);
    }
    NProgress.inc(0.5);
    if (to.meta.title) {
        // @ts-ignore
        document.title = to.meta.title;
    } else document.title = '';
    return true;
});

router.afterEach(() => {
    NProgress.done();
})

export default router;
