<template>
    <div :class="classes" @click="pick" @dblclick="unpick" onselectstart="return false">
        <div class="data-part-one">
            <div class="item-avatar">
                <img :src="item.avatar"/>
            </div>
            <div class="unread-num" v-if="item.unread">{{item.unread}}</div>
        </div>
        <div class="data-part-two" :class="{contact: !forRecent}">
            <div class="item-title">
                <span class="dot"/>
                <span>{{item.realname}}</span>
            </div>
            <div class="item-breif" v-if="forRecent" v-html="emojiNameToImage(item.breif)"/>
        </div>
        <div class="data-part-three" v-if="forRecent">
            <slot name="part-three">
                <div class="item-time">{{item.datetime}}</div>
                <div class="item-trumpet"/>
            </slot>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import {emojiNameToImage} from '@/lib/Util';
    import {computed} from 'vue';

    const emits = defineEmits(['pick']);
    const props = defineProps({
        item: <any>Object,
        forRecent: {type: Boolean, default: true},
        actived: Boolean,
        online: Boolean,
    });
    // 属性
    let classes = computed(() => {
        return {
            'data-item': true,
            'action': props.actived,
            'online': props.online,
        };
    });
    // 方法
    const pick = (v: any) => {
        emits('pick', props.item);
    };
    const unpick = () => {
        //this.$emit('unpick', this.item);
    }
</script>

<style lang="less" scoped>
    @item-height: 40px;
    @part-one-width: 40px;
    @part-three-width: 90px;
    @part-two-width: calc(100% - @part-one-width - @part-three-width);
    .data-item {
        width: 100%;
        padding: 12px 6px 12px 12px;
        cursor: default;
        height: @item-height + 13px *2;

        &:after {
            content: "";
            display: table;
            clear: both;
        }

        &:hover {
            background-color: #DEDDDB;
        }

        &.action {
            background-color: #C9C8C6;
        }

        &.online .data-part-two .dot {
            position: relative;
            display: inline-block;
            width: 6px;
            height: 6px;
            background-color: #3EBB3C;
            border-radius: 6px;
            margin-right: 5px;
        }

        .data-part-one {
            position: relative;
            width: @part-one-width;
            height: 100%;
            float: left;

            .item-avatar {
                position: relative;
                height: 100%;
                width: 100%;
                overflow: hidden;
                border-radius: 2px;
                background-color: #FFFFFF;

                img {
                    width: 100%;
                    /*height: 100%;*/
                    min-height: 100%;
                }
            }

            .unread-num {
                display: inline-block;
                position: absolute;
                min-width: 18px;
                height: 18px;
                line-height: 18px;
                padding: 0 4px;
                border-radius: 18px;
                background-color: #FA5151;
                font-size: 10px;
                -webkit-text-size-adjust: none;
                color: #ffffff;
                top: -9px;
                right: -9px;
                text-align: center;
            }
        }

        .data-part-two {
            position: relative;
            width: @part-two-width;
            height: 100%;
            font-size: 14px;
            float: left;
            color: #000000;
            padding: 0 10px;
            overflow: hidden;
            word-break: keep-all;

            .item-title {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                display: flex;
                align-items: center;
            }

            .item-breif {
                color: #A8A899;
                font-size: 13px;
                /*padding-top: 2px;*/
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            &.contact {
                width: calc(100% - @part-one-width - 10px);
            }

            &.contact .item-title {
                position: relative;
                height: 100%;
                line-height: 40px;
            }
        }

        .data-part-three {
            position: relative;
            width: @part-three-width;
            height: 100%;
            float: left;

            .item-time {
                color: #A8A899;
                font-size: 12px;
                width: 100%;
                text-align: right;
            }
        }
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .data-item {
            /*border-bottom: 1px solid #f0f0f0;*/

            &:last-of-type {
                border-bottom: none;
            }
        }
    }
</style>
