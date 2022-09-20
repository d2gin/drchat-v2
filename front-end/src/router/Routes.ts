import HomeView from "@/views/Index/Main.vue";

export default [
    {
        path: '/',
        name: 'home',
        redirect: '/message',
        component: HomeView,
        children: [
            {
                path: '/message',
                name: 'message',
                components: {
                    'second-nav': () => import('@/views/Message/Index.vue'),
                    'third-nav': () => import('@/views/Message/Box.vue'),
                },
                meta: {
                    title: '聊天',
                },
            },
            {
                path: '/contact',
                name: 'contact',
                components: {
                    'second-nav': () => import('@/views/Contact/Index.vue'),
                    'third-nav': () => import('@/views/Contact/InfoBox.vue'),
                },
                meta: {
                    title: '联系人',
                },
            },
            {
                path: '/me',
                name: 'me',
                components: {
                    'second-nav': () => import('@/views/Me/Index.vue'),
                    'third-nav': () => import('@/views/Me/EmptySpace.vue'),// 空组件 占位
                },
                meta: {
                    title: '我的',
                },
            }
        ],
    },
    {
        path: '/login',
        name: 'login',
        component: () => import("@/views/Passport/Login.vue"),
        meta: {
            access: true,
            title: '登录',
        },
    },
    {
        path: '/mobile-message',
        name: 'mobile-message',
        component: () => import('@/views/Message/Box.vue'),
        meta: {
            title: '聊天',
        },
    },
    {
        path: '/video-chat',
        name: 'video-chat',
        component: () => import('@/views/Message/VideoChat.vue'),
        meta: {
            title: '视频',
        },
    },
    {
        path: '/mobile-friend-info',
        name: 'mobile-friend-info',
        component: () => import('@/views/Contact/InfoBox.vue'),
        meta: {
            title: '好友资料',
        },
    },
];
