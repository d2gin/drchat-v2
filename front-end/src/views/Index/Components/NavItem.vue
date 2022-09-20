<template>
    <div :class="['channel',active ? 'action' : '']" v-if="!item.hide" @click.stop="item.event">
        <template v-if="item.path">
            <router-link :to="item.path">
                <i :class="item.icon"></i>
            </router-link>
        </template>
        <template v-else>
            <i :class="item.icon"></i>
        </template>
        <div class="unread-num" v-if="item.unread">{{item.unread}}</div>
    </div>
</template>

<script lang="ts">
    import {Options, Vue} from "vue-class-component";

    @Options({
        props: {
            item: Object,
        },
        watch: {
            $route() {
                this.current(this.$route.path);
            }
        },
    })
    export default class NavItem extends Vue {
        item!: any;
        active = false;

        mounted() {
            this.$nextTick(function () {
                this.current(this.$route.path)
            });
        }

        current(path: string) {
            this.active = !!(this.item.path && this.item.path == path);
        }
    }
</script>

<style lang="less" scoped>
    .channel {
        position: relative;
        margin-top: 25px;
        margin-bottom: 25px;
        text-align: center;
        cursor: pointer;

        .unread-num {
            display: inline-block;
            position: absolute;
            min-width: 18px;
            height: 18px;
            line-height: 16px;
            padding: 0 4px;
            border-radius: 18px;
            background-color: #FA5151;
            font-size: 10px;
            -webkit-text-size-adjust: none;
            color: #ffffff;
            top: -5px;
            right: calc(50% - 18px);
            text-align: center;
        }

        img {
            width: 20px;
            height: 20px;
        }

        a {
            display: inline-block;
            width: 100%;
            height: 100%;
            text-decoration: none;
        }

        .iconfont {
            width: 100%;
            height: 100%;
            font-size: 23px;
            color: #828282;
        }

        &:hover .iconfont {
            color: #e0e0e0;
        }

        &.action .iconfont, &.action:hover .iconfont {
            color: #07C160;
        }
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .channel {
            position: relative;
            margin: 0;
            margin-left: 25px;
            margin-right: 25px;
            text-align: center;
            cursor: pointer;
            /*line-height: 60px;*/
            float: left;
        }
    }
</style>
