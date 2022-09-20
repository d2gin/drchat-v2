import {createApp} from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import ant from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import Socketio from "@/lib/Socket/Socketio";
// 图标库
import './static/iconfont/iconfont.css';
// 主题
import '@/static/main.less';

let vue = createApp(App);
vue.provide('socketio', Socketio);
vue.use(store);
vue.use(router);
vue.use(ant);
vue.mount('#app');
