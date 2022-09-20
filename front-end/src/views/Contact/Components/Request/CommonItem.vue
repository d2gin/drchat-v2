<template>
    <div class="common-item">
        <div class="data-part-one" @click="pick">
            <div class="item-avatar">
                <img :src="item.sender_info.avatar"/>
            </div>
        </div>
        <div class="data-part-two" @click="pick">
            <div class="item-title">{{item.sender_info.nickname}}</div>
            <div class="item-content">{{item.message}}</div>
        </div>
        <div class="data-part-three">
            <slot name="part-three"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "common-item",
        props: {
            item: Object,
            selected: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {};
        },
        mounted() {
            console.log('aaaa');
        },
        methods: {
            pick() {
                this.$emit('pick', this.item);
            }
        }

    }
</script>

<style lang="less" scoped>
    @item-height: 50px;
    @part-one-width: 50px;
    @part-three-width: 120px;
    @part-two-width: calc(100% - @part-one-width - @part-three-width);
    .common-item {
        width: 100%;
        padding: 12px 6px 12px 12px;
        cursor: default;
        height: @item-height + 13px *2;
        border-bottom: 1px solid #E7E7E7;

        .data-part-one {
            position: relative;
            width: @part-one-width;
            height: @item-height;
            float: left;

            .item-avatar {
                position: relative;
                height: 100%;
                width: 100%;
                overflow: hidden;
                border-radius: 2px;

                img {
                    width: 100%;
                    /*height: 100%;*/
                    min-height: 100%;
                }
            }
        }

        .data-part-two {
            position: relative;
            width: @part-two-width;
            height: 100%;
            font-size: 14px;
            float: left;
            color: #000000;
            padding: 5px 10px;
            overflow: hidden;
            word-break: keep-all;

            .item-title {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            .item-content {
                color: #A8A899;
                font-size: 13px;
                /*padding-top: 2px;*/
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
        }

        .data-part-three {
            position: relative;
            width: @part-three-width;
            height: 100%;
            float: left;

            .item-component {
                color: #A8A899;
                font-size: 12px;
                width: 100%;
                text-align: right;
            }
        }
    }
</style>
