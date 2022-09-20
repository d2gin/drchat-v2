<template>
    <div class="send-box" ref="sendBoxRef">
        <div class="send-tool">
            <div class="emoji-box" v-if="showEmoji">
                <div class="emoji-body">
                    <template v-for="(item, index) in expressions" :key="index">
                        <div class="emoji-item" @click="addEmoji(item)">
                            <img :src="item.path" :title="item.name"/>
                        </div>
                    </template>
                </div>
                <div class="emoji-foot">
                    <div class="emoji-cate act" @click="emojiType">
                        <img src="@/assets/emoji.png"/>
                    </div>
                    <div class="emoji-cate" @click="emojiType">
                        <img src="@/assets/like.png"/>
                    </div>
                </div>
            </div>
            <div class="tool-item" v-clickoutside="handleHideEmoji" @click="handleEmoji">
                <i class="iconfont icon-xiaolian"/>
            </div>
            <div class="tool-item">
                <i class="iconfont icon-folder"/>
            </div>

            <div class="tool-item">
                <i class="iconfont icon-jiandao"/>
            </div>

            <div class="tool-item">
                <i class="iconfont icon-liaotianjilu"/>
            </div>
            <div class="the-right">
                <div class="tool-item">
                    <i class="iconfont icon-tongxinyuan"/>
                </div>

                <div class="tool-item" v-if="video" @click="handleShipin">
                    <i class="iconfont icon-shipindianhua1"/>
                </div>
            </div>
        </div>
        <div class="message-textarea"
             contenteditable="true"
             spellcheck="false"
             ref="messageTextareaRef"
             @focus="focusToChangeBG"
             @blur="blurToChangeBG"
             @keydown.ctrl.enter="sendMsg"
             @click="handleClick"
             @keydown.exact="handleClick"
             @keyup.exact="handleClick"/>
        <div class="send-foot">
            <button class="send-btn" type="button" @click="sendMsg">发送(S)</button>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import {mapState, useStore} from "vuex";
    import {computed, getCurrentInstance, onMounted, reactive, ref} from "vue";
    // 内置
    const props = defineProps({
        video: {type: Boolean, default: true},
    });
    const emits = defineEmits(['vchat', 'send']);
    const store = useStore();
    const sendBoxRef: any = ref(null);
    const messageTextareaRef: any = ref(null);
    // 钩子
    onMounted(() => {
    });
    // 属性
    let lastSelectionRange = ref(null);
    let showEmoji = ref(false);
    let sendBox = reactive({
        bgContext: '',
    });

    // vuex
    const userinfo = computed(() => store.state.Chat.expressions);
    const expressions = computed(() => store.state.Chat.expressions);
    //
    let selection: any = computed(() => window.getSelection());
    // 指令
    const vClickoutside = {
        mounted: (el: any, binding: any, vnode: any, prevVnode: any) => {
            // 给当前元素绑定个私有变量，方便在unbind中可以解除事件监听
            el.__vueClickOutside__ = function (e: any) {
                // 这里判断点击的元素是否是本身，是本身，则返回
                for (var i = 0; i <= 3; i++) {
                    var d = e.path[i];
                    if (!d) {
                        break;
                    } else if (-1 !== d.className.search(/(emoji-box|emoji-body|emoji-item)/is)) {
                        return false;
                    }
                }
                if (el.contains(e.target)) {
                    return false;
                } else if (binding.value) {// 判断指令中是否绑定了函数
                    binding.value(e);
                }
            };
            document.addEventListener('click', el.__vueClickOutside__);
        },
        unmounted(el: any, binding: any, vnode: any, prevVnode: any) {
            // 解除事件监听
            document.removeEventListener('click', el.__vueClickOutside__);
            delete el.__vueClickOutside__;
        }
    };
    // 方法
    /**
     * 写入表情
     * @param item
     */
    const addEmoji = (item: any) => {
        focus();
        if (lastSelectionRange.value) {
            selection.value.removeAllRanges();
            selection.value.addRange(lastSelectionRange.value);
        }
        document.execCommand('insertHTML', false, `<img emoji-name="${item.code}" class="editor-emoji" src="${item.path}"/>`);
        saveLastRange();
    };

    const emojiType = (e: any) => {
        console.log(e);
    };

    const handleClick = () => {
        saveLastRange();
    }

    const handleEmoji = () => {
        showEmoji.value = !showEmoji.value;
        if (showEmoji.value && !expressions.value.length) {
            store.dispatch('Chat/refreshExpressions', true);
        }
    };
    const handleHideEmoji = () => {
        showEmoji.value = false;
    };
    const handleShipin = () => {
        emits('vchat');
    };
    const saveLastRange = () => {
        lastSelectionRange.value = selection.value.getRangeAt(0);
        // console.log(this.lastSelectionRange)
    };
    const focusToChangeBG = () => {
        // 保存现场
        sendBox.bgContext = sendBoxRef.value.style.backgroundColor;
        sendBoxRef.value.style.backgroundColor = '#FFFFFF';
    };
    const blurToChangeBG = () => {
        sendBoxRef.value.style.backgroundColor = sendBox.bgContext;
    };
    const getFormatValue = () => {
        let text = messageTextareaRef.value.innerHTML;
        return emojiImageToName(text);
    };
    const emojiImageToName = (str: string) => {
        return str.replace(/<img emoji-name=["|']([^"]*?)["|'] [^>]*>/gi, "[!$1]");
    };

    const sendMsg = () => {
        var text = getFormatValue();
        if (!text) {
            console.log('内容为空');
        }
        emits('send', text);
    };
    const clear = () => {
        if (messageTextareaRef.value) {
            messageTextareaRef.value.innerHTML = "";
        }
    };
    const focus = () => {
        if (messageTextareaRef.value) messageTextareaRef.value.focus();
    };

    defineExpose({ //
        clear,
        focus,
    });
</script>

<style lang="less" scoped>
    @send-tool-height: 35px;
    @send-foot-height: 40px;
    @emoji-foot-height: 40px;
    @emoji-box-width: 475px;
    @emoji-box-height: 290px;
    .send-box {
        position: relative;
        width: 100%;
        height: 35%;
        padding: 0;
        float: left;
        border-top: 1px solid #ececec;

        .send-tool {
            height: @send-tool-height;
            padding: 0 25px;
            /*border: 1px solid red;*/

            .tool-item {
                display: inline-block;
                float: left;
                margin-right: 15px;
                width: 25px;
                height: @send-tool-height;
                line-height: @send-tool-height;
                overflow: hidden;
                cursor: pointer;

                .iconfont {
                    color: #878787;
                    font-size: 20px;

                    &:hover {
                        color: #222222;
                    }

                    &.icon-tongxinyuan {
                        font-size: 18px;
                    }

                    &.icon-jiandao {
                        font-size: 18px;
                    }

                    &.icon-shipindianhua1 {
                        font-size: 21px;
                    }
                }

                &:last-of-type {
                    margin-right: 0;
                }
            }

            .the-right {
                float: right;
            }


            .emoji-box {
                position: absolute;
                width: @emoji-box-width;
                height: @emoji-box-height;
                border: 1px solid #D1D1D1;
                border-radius: 2px;
                box-shadow: 0 0 5px #D3D3D3;
                bottom: 100%;
                /*right: 80%;*/
                left: 0;
                background-color: #FFFFFF;

                .emoji-box-modal {
                    position: fixed;
                    /*width: 100%;*/
                    /*height: 100%;*/
                    z-index: 999;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;

                }


                .emoji-body {
                    position: relative;
                    width: 100%;
                    max-height: calc(100% - @emoji-foot-height);
                    overflow-x: hidden;
                    /*margin: 0 0 50px 0;*/
                    padding: 8px 5px;

                    &::-webkit-scrollbar {
                        /*滚动条整体样式*/
                        width: 6px; /*高宽分别对应横竖滚动条的尺寸*/
                        height: 1px;
                        position: fixed;
                    }

                    &::-webkit-scrollbar-thumb {
                        /*滚动条里面小方块*/
                        border-radius: 10px;
                        /*box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);*/
                        background: #D6D6D8;
                    }

                    .emoji-item {
                        position: relative;
                        display: inline-block;
                        width: 35px;
                        height: 35px;
                        margin-left: 2px;
                        margin-top: 2px;
                        padding: 2px;
                        cursor: pointer;

                        &:last-of-type {
                            padding-left: 0;
                        }

                        &:hover {
                            background-color: #E7E7E7;
                        }

                        img {
                            position: relative;
                            width: 100%;
                            height: 100%;
                        }
                    }
                }

                .emoji-foot {
                    position: fixed;
                    display: inline-block;
                    width: @emoji-box-width - 2;
                    height: @emoji-foot-height;
                    background-color: #F5F5F5;

                    .emoji-cate {
                        display: flex;
                        width: 60px;
                        height: @emoji-foot-height;
                        float: left;
                        cursor: pointer;
                        padding: 5px 3px;
                        justify-content: center;
                        align-items: center;

                        &.act {
                            background-color: #FFFFFF;
                        }

                        img {
                            position: relative;
                            width: 23px;
                            height: 23px;
                        }
                    }
                }
            }
        }

        .message-textarea {
            position: relative;
            box-sizing: border-box;
            width: 100%;
            height: calc(100% - @send-tool-height - @send-foot-height);
            border: none;
            /*border: 1px solid red;*/
            resize: none;
            float: left;
            line-height: 1.3em;
            font-size: 14px;
            padding-top: 5px;
            color: #202020;
            background-color: #F5F5F5;

            &:focus {
                outline: 0;
                background-color: #FFFFFF;
            }

            img {
                border: 1px solid red;
            }
        }

        .send-foot {
            position: relative;
            height: @send-foot-height;
            width: 100%;
            float: left;

            .send-btn {
                float: right;
                background-color: #f5f5f5;
                border: 1px solid #E1E1E1;
                color: #606060;
                padding: 6px 10px;
                display: inline-block;
                cursor: pointer;
                margin-right: 10px;
                margin-bottom: 10px;

                &:hover {
                    background-color: #129611;
                    color: #ffffff;
                }
            }
        }
    }

    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .send-box {
            height: 25%;

            .send-tool {
                .emoji-box {
                    width: 100%;
                    height: @emoji-box-height * 0.8;
                }
            }
        }
    }
</style>
