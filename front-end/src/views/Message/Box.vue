<template>
    <div class="message-box" v-if="conversationId">
        <div class="message-head">
            <div class="contact">
                <i v-if="isMobile()" class="iconfont icon-zuojiantou goback" @click="$router.go(-1)"/>
                <!--<CloseOutlined style="float: right" @click="$router.push('/message')" v-else/>-->
                <a-button type="text" size="small" style="float: left" @click="$router.push('/message')" v-else>返回</a-button>
                {{conversation.object_info?.title}}
                <i v-if="conversation.object_type == 2"
                   class="iconfont icon-gengduo menu-more"
                   @click="showGroupMembers = true"/>
            </div>
        </div>

        <div class="message-content" ref="messageContentBox" @scroll="scrollit">
            <a-spin :spinning="app.loading"/>
            <div class="load-more" v-if="app.has_more" @click="loadMoreMessage">查看更多消息</div>
            <div class="load-new" v-if="app.has_new" @click="loadNewMessage">
                <div class="iconfont icon-shang turn-up"/>
                <div class="text">106条新消息</div>
                <div class="iconfont icon-chaguanbi close"/>
            </div>
            <template v-for="(msg, index) in messageRecords" :key="index">
                <Message :ref="(el) => refs[msg.id] = el"
                         :data="msg"
                         :type="msg.direction"
                         @onRedDot="handleRedDot"/>
            </template>
        </div>
        <SendBox ref="sendBox"
                 :video="conversation.object_type == 1"
                 @send="handleSend"
                 @vchat="handleVideoChat"/>
        <Members v-if="conversation.object_type == 2"
                 v-model="showGroupMembers"
                 :group-id="conversation.object_id"
                 :group-name="conversation.object_info.title"/>
    </div>
</template>

<script lang="ts">
    import useController from "@/lib/useController";
    import MessageBoxController from "@/controller/MessageBoxController";

    export default useController(new MessageBoxController());
</script>
<style lang="less" scoped>
    @message-head-height: 55px;
    .message-box {
        /*padding: 10px 0;*/
        background-color: #f5f5f5;
        /*height: 80%;*/
        height: 100%;
        border-top: 1px solid #ececec;

        &:after {
            content: "";
            clear: both;
            display: table;
        }


        .message-head {
            border-bottom: 1px solid #E7E7E7;
            height: @message-head-height;

            .contact {
                padding: 15px 30px;
                font-size: 18px;
                color: #000000;
                text-align: center;
                overflow: hidden;
                text-overflow: ellipsis;
                word-break: keep-all;
                white-space: nowrap;

                .goback {
                    float: left;
                }

                .menu-more {
                    float: right;
                    padding-right: 3px;
                    cursor: pointer;
                }
            }
        }

        .message-content {
            position: relative;
            width: 100%;
            height: calc(65% - @message-head-height);
            display: inline-block;
            overflow-x: hidden;
            overflow-y: auto;
            float: left;

            /*&::-webkit-scrollbar {*/
            /*    width: 0;*/
            /*}*/

            /*&:hover {*/

            &::-webkit-scrollbar {
                /*滚动条整体样式*/
                width: 6px; /*高宽分别对应横竖滚动条的尺寸*/
                height: 1px;
                position: fixed;
            }

            &::-webkit-scrollbar-thumb {
                /*滚动条里面小方块*/
                border-radius: 10px;
                background: #BDBDBB;
            }

            /*}*/

            .ant-spin-spinning {
                position: relative;
                width: 100%;
                z-index: 99;
                /*display: flex;*/
                /*align-items:center;*/
                /*justify-content: center;*/
                padding-top: 10px;

                .ant-spin-dot-spin {
                }
            }

            .load-more {
                color: #2C90FF;
                width: 100%;
                text-align: center;
                font-size: 12px;
                padding: 5px;
                cursor: pointer;
            }

            .load-new {
                background-color: #FFFFFF;
                color: #3EBB3C;
                position: fixed;
                /*width: 165px;*/
                height: 32px;
                border: 1px solid #E4E4E4;
                border-radius: 3px;
                font-size: 13px;
                z-index: 10;
                /*line-height: 32px;*/
                top: @message-head-height + 1px;
                right: 25px;
                padding: 4px 0 4px 10px;
                cursor: pointer;

                .turn-up {
                    display: inline-block;
                    float: left;
                    padding-top: 1px;
                    padding-right: 5px;
                    font-size: 12px;
                }

                .text {
                    display: inline-block;
                    float: left;
                }

                .close {
                    display: inline-block;
                    padding: 0 10px;
                    border-left: 1px solid #DFE1E0;
                    color: #6E6E6E;
                    float: right;
                    font-size: 13px;
                    margin-left: 10px;
                }
            }
        }

    }

    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .message-box {
            .message-head {
                .contact {
                    padding: 15px;
                }
            }

            .message-content {
                height: calc(75% - @message-head-height);
            }
        }
    }
</style>
