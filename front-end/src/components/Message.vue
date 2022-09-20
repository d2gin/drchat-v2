<template>
    <div :class="type" ref="messageItem">
        <div class="avatar">
            <img :src="data.avatar"/>
        </div>
        <div class="message-ep" v-if="data.datetime">{{data.datetime}}</div>
        <div class="message">
            <span v-html="emojiNameToImage(data.content)"/>
            <div v-if="data.red_dot" class="red-dot" @click.stop="handleRedDot(data)"/>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import {emojiNameToImage} from '@/lib/Util';
    import {ref} from 'vue';

    const props = defineProps({
        data: Object,
        type: String,
    });
    const emits = defineEmits(['onRedDot']);
    const handleRedDot = (data: object) => {
        emits('onRedDot', data);
    };
    const messageItem = ref();
    defineExpose({messageItem});
</script>

<style lang="less" scoped>
    @avatar-height: 35px;
    @avatar-width: 35px;
    .forward, .reverse {
        position: relative;
        margin-top: 15px;
        /*padding-top: 8px;*/
        display: inline-block;
        width: 100%;

        &:last-of-type {
            margin-bottom: 15px;
        }


        .avatar {
            width: @avatar-height;
            height: @avatar-width;
            border-radius: 2px;
            background-color: gray;
            padding: 0;
            margin: 0;
            overflow: hidden;

            img {
                width: 100%;
                /*height: 100%;*/
                min-height: 100%;
            }
        }

        .message-ep {
            display: block;
            padding-bottom: 2px;
            margin-top: -5px;
            font-size: 12px;
            color: #B2B2B2;
            /*position: absolute;*/
            /*top: 0;*/
        }

        .message {
            position: relative;
            display: inline-block;
            max-width: 60%;
            min-width: 30px;
            min-height: 35px;
            word-wrap: break-word;
            border-radius: 4px;
            color: #232323;
            background-color: #ffffff;
            padding: 8px;
            cursor: default;
            font-size: 15px;

            &:after {
                content: "";
                position: absolute;
                border-width: 5px;
                border-style: solid;
                top: calc(calc(30px - 10px) / 2);
            }
        }
    }

    .reverse {
        float: right;
        padding-right: 24px;

        .message-ep {
            padding-right: calc(@avatar-width + 10px);
            text-align: right;
            /*float: right;*/
            /*right: @avatar-width;*/
        }

        .message {
            float: right;
            margin-right: 8px;
            background-color: #98FF65;

            &:after {
                left: 100%;
                border-color: transparent transparent transparent #98FF65;
            }

            &:hover {
                background-color: #98E165;
            }

            &:hover:after {
                border-color: transparent transparent transparent #98E165;
            }

            .red-dot {
                @size: 20px;
                position: absolute;
                display: inline-flex;
                justify-content: center;
                align-content: center;
                height: @size;
                width: @size;
                background-color: red;
                border-radius: 1000px;
                left: -(@size + 8px);
                text-align: center;
                color: #FFFFFF;
                font-size: 12px;

                &:after {
                    content: "!";
                }
            }
        }

        .avatar {
            float: right;
        }
    }

    .forward {
        float: left;
        padding-left: 30px;

        .message-ep {
            padding-left: calc(@avatar-width + 10px);
        }

        .message {
            float: left;
            margin-left: 8px;
            border: 1px solid #EDEDED;

            &:after {
                right: 100%;
                border-color: transparent #ffffff transparent transparent;
            }

            &:hover {
                background-color: #F5F5F5;
            }

            &:hover:after {
                border-color: transparent #F5F5F5 transparent transparent;
            }
        }

        .avatar {
            float: left;
        }

    }
</style>
