<template>
    <div class="video-box">

        <div class="friend-box">
            <div class="avatar">
                <img :src="friend?.friend.avatar"/>
            </div>
            <div class="info">
                <div class="friend-name">test</div>
                <template v-if="$route.query.accept == 1">
                    <div class="connect-message" v-if="isPeerConnection">正在接入连线...</div>
                    <div class="connect-message" v-else-if="facehim.booted">正在通话...</div>
                    <div class="connect-message" v-else>等待视频连接...</div>
                </template>
                <template v-else>
                    <div class="connect-message" v-if="!inviteAsk.result">等待对方接受邀请...</div>
                    <div class="connect-message" v-else>{{inviteAsk.resultMap[inviteAsk.result]}}</div>
                </template>
            </div>
        </div>
        <video class="faceme" :class="facemeClasses" ref="facemeRef"
               @mousedown="handleMouse"
               @touchmove="handleTouch"
               @touchstart="handleTouch"
               @touchend="handleTouch"
               @dblclick.exact="handleFullscreen('faceme')"
               muted autoplay/>
        <video class="facehim" :class="facehimClasses" ref="facehimRef"
               @mousedown="handleMouse"
               @touchmove="handleTouch"
               @touchstart="handleTouch"
               @touchend="handleTouch"
               @dblclick="handleFullscreen('facehim')"
               autoplay/>
        <div class="chat-tool">
            <div :class="{item:true, active: faceme.camera_off}">
                <i class="iconfont icon-qiehuanyuyin" @click.stop="handleToggleCamera"/>
            </div>
            <div class="item" @click.stop="handleToggleSwitchCamera">
                <i class="iconfont icon-switch-camera"/>
            </div>
            <div class="item hand-up" @click.stop="$router.go(-1)">
                <i class="iconfont icon-guaduan1"/>
            </div>
            <div :class="{item: true, active: faceme.muted}" @click.stop="handleToggleMuted">
                <i class="iconfont icon-jingyin"/>
            </div>
            <div :class="{item: true, active: faceme.sound_off}" @click.stop="handleToggleSound">
                <i class="iconfont icon-sound-off"/>
            </div>
        </div>
        <Cameras v-model="cameraPick" :cameras="devices.camera.all" @pickCamera="handlePickCamera"/>
    </div>
</template>

<script lang="ts">
    import useController from "@/lib/useController";
    import VideoChatController from "@/controller/VideoChatController";

    export default useController(new VideoChatController());
</script>


<style lang="less" scoped>
    .video-box {
        position: relative;
        overflow: hidden;
        width: 720px;
        height: 100%;
        margin: 0 auto;
        background-color: #555555;

        .btn-group {
            position: absolute;
            bottom: 0;
            z-index: 10;
        }

        .facehim, .faceme {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            right: 0;
            transition: all .4s;

            &.trans-none {
                transition: unset;
            }

            &.front {
                -moz-transform: scale(-1, 1);
                -ms-transform: scale(-1, 1);
                -o-transform: scale(-1, 1);
                -webkit-transform: scale(-1, 1);
                transform: scale(-1, 1);
            }

            &.float-screen {
                width: 90px;
                height: 130px;
                top: 10px;
                right: 10px;
                z-index: 2;
                border: 1px solid #e0e0e0;
            }

            &.full-screen {
                width: 100%;
                height: 100%;
                top: 0 !important;
                right: 0 !important;
            }
        }

        .faceme {
        }

        .chat-tool {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 15px 0;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;


            .item {
                position: relative;
                display: inline-block;
                color: #ffffff;
                background-color: #2C2C2C;
                border-radius: 100%;
                width: 50px;
                height: 50px;
                line-height: 50px;
                text-align: center;
                cursor: pointer;
                margin-right: 30px;

                &:last-of-type {
                    margin-right: 0;
                }

                &:hover {
                    background-color: #404143;
                }

                &.hand-up {
                    background-color: #F35549;

                    &:hover {
                        background-color: #FB5F68;
                    }
                }

                &.active {
                    background-color: #ffffff;
                    color: #000000;
                }
            }

            .iconfont {
                display: inline-block;
                font-size: 25px;
            }
        }

        .friend-box {
            position: absolute;
            top: 0;
            left: 0;
            padding: 15px;
            z-index: 10;

            .avatar {
                float: left;
                width: 60px;
                height: 60px;
                overflow: hidden;

                img {
                    width: 100%;
                    height: 100%;
                }
            }

            .info {
                float: left;
                width: calc(100% - 60px);
                padding: 0 0 0 15px;
                color: #ffffff;

                .friend-name {
                    font-size: 18px;
                    font-weight: bold;
                }
            }
        }
    }

    .hide {
        display: none;
    }

    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .video-box {
            /*max-width: 100%;*/
            width: 100%;

            .facehim, .faceme {
                &.float-screen {
                }
            }

            .chat-tool {
                .item {
                    margin-right: 22px;
                }
            }
        }
    }
</style>
