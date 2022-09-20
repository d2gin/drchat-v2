<template>
    <div class="user-box" v-if="is_mobile">
        <div class="user-head">
            <div class="user-avatar">
                <img :src="userInfo?.avatar"/>
            </div>
            <div class="user-info">
                <div class="nickname">{{userInfo?.nickname}}</div>
                <div class="username">
                    用户名：{{userInfo?.username}}
                    <div class="detail">
                        <i class="iconfont icon-zuojiantou"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-item ext">
            支付<i class="iconfont icon-zuojiantou"/>
        </div>
        <div class="user-item">
            收藏<i class="iconfont icon-zuojiantou"/>
        </div>
        <div class="user-item">
            朋友圈<i class="iconfont icon-zuojiantou"/>
        </div>
        <div class="user-item">
            表情<i class="iconfont icon-zuojiantou"/>
        </div>
        <div class="user-item">
            设置<i class="iconfont icon-zuojiantou"/>
        </div>
        <div class="user-item ext" @click="logout">
            退出<i class="iconfont icon-zuojiantou"/>
        </div>
    </div>
</template>

<script lang="ts">
    import MeController from "@/controller/MeController";
    import useController from "@/lib/useController";

    export default useController(new MeController());
</script>

<style lang="less" scoped>
    @user-avatar-width: 60px;
    .user-box {
        .user-head {
            color: #000000;
            background-color: #ffffff;
            padding: 20px 15px 15px 20px;

            &:after {
                content: "";
                display: table;
                clear: both;
            }

            .user-avatar {
                width: @user-avatar-width;
                height: @user-avatar-width;
                float: left;
                overflow: hidden;
                border-radius: 5px;

                img {
                    position: relative;
                    width: 100%;
                    height: 100%;
                }
            }

            .user-info {
                float: left;
                width: calc(100% - @user-avatar-width);
                padding: 5px 0 5px 15px;
                line-height: 28px;

                .nickname {
                    position: relative;
                    width: 100%;
                    font-size: 18px;
                    font-weight: bolder;
                }

                .username {
                    position: relative;
                    width: 100%;
                    color: #aaaaaa;
                    font-size: 14px;

                    .detail {
                        float: right;

                        .icon-zuojiantou {
                            display: inline-block;
                            transform: scaleX(-1);
                            font-size: 12px;
                        }
                    }
                }
            }
        }

        .user-item {
            width: 100%;
            padding: 10px 15px;
            background: #ffffff;
            color: #111111;
            /*font-weight: bold;*/

            &.ext {
                margin: 6px 0;
            }

            .icon-zuojiantou {
                font-weight: normal;
                display: inline-block;
                transform: scaleX(-1);
                float: right;
                font-size: 13px;
                color: #aaaaaa;
            }
        }

        .center {
            text-align: center;
        }

        .logout {
            display: inline-block;
            padding: 10px 20px;
            background-color: orangered;
            color: #ffffff;
        }
    }
</style>