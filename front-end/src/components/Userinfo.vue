<template>
    <div class="info-box">
        <div class="info-head">
            <div class="info-main">
                <div class="info-title">{{info.nickname}}</div>
                <div class="xingbie">
                    <i class="iconfont"
                       :class="{
                        nanxing: info.sex == 1,
                        nvxing: info.sex == 2,
                        'icon-nanxing': info.sex == 1,
                        'icon-nvxing': info.sex == 2,
                    }"/>
                </div>
                <div class="info-breif" v-if="'signature' in info">{{info.signature}}</div>
            </div>
            <div class="info-avatar">
                <img :src="info.avatar"/>
            </div>
        </div>
        <div class="info-body">
            <table>
                <tr v-if="'username' in info">
                    <td class="info-item-field">用户名</td>
                    <td class="info-item-value">{{info.username}}</td>
                </tr>
                <tr v-if="'remark_name' in info">
                    <td class="info-item-field">备注</td>
                    <td class="info-item-value">
                        <div class="ready-to-mark" @click="showMarkInput" v-if="!marknameInputVisible">
                            <template v-if="!info.remark_name">点击添加备注</template>
                            <template v-else>{{info.remark_name}}</template>
                        </div>

                        <template v-if="marknameInputVisible">
                            <div class="to-mark">
                                <input class="markname-input" ref="marknameInput" @keyup.enter="$event.target.blur"
                                       @blur="handleTomark"
                                       :value="info.remark_name"/>
                            </div>
                        </template>
                    </td>
                </tr>
                <tr v-if="'city' in info">
                    <td class="info-item-field">地区</td>
                    <td class="info-item-value">{{info.city}}</td>
                </tr>
                <tr v-if="'signature' in info">
                    <td class="info-item-field">签名</td>
                    <td class="info-item-value">{{info.signature}}</td>
                </tr>
                <tr v-if="'source' in info">
                    <td class="info-item-field">来源</td>
                    <td class="info-item-value">{{info.source}}</td>
                </tr>
            </table>
        </div>
        <div class="info-body" v-if="'marktext' in info">
            <table>
                <tr>
                    <td class="info-item-field">{{info.nickname}}</td>
                    <td class="info-item-value">{{info.marktext}}</td>
                </tr>
            </table>
        </div>
        <div class="info-foot">
            <div class="send-btn" @click="handleContact" v-if="canContact">发消息</div>
            <slot name="info-foot"></slot>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import {nextTick, ref} from "vue";

    const props = defineProps({
        info: Object,
        canContact: {
            type: Boolean,
            default: true,
        },
    });
    const emits = defineEmits(['contact', 'tomark']);
    let marknameInputVisible = ref(false);
    const marknameInput = ref();

    const handleContact = () => {
        emits('contact', props.info);
    };
    const showMarkInput = () => {
        marknameInputVisible.value = true;
        nextTick(() => {
            marknameInput.value.focus();
        });
    }
    const handleTomark = (e: any) => {
        marknameInputVisible.value = false;
        var markname = e.target.value;
        /*if (markname) */
        //props.info.markname = markname;
        emits('tomark', props.info?.fid, markname);
    };
</script>

<style lang="less" scoped>
    .info-box {
        position: relative;
        width: 400px;
        height: 100%;
        padding: 50px 10px;
        margin: 0 auto;

        .info-head {
            position: relative;
            width: 100%;
            padding: 50px 0;
            border-bottom: 1px solid #E7E7E7;

            &:after {
                content: "";
                display: table;
                clear: both;
            }

            .info-main {
                position: relative;
                display: inline-block;
                width: calc(100% - 60px);
                float: left;
                padding-right: 10px;

                .info-title {
                    font-size: 20px;
                    max-width: calc(100% - 25px);
                    color: #000000;
                    padding-left: 0;
                    overflow: hidden;
                    float: left;
                    display: inline-block;
                    white-space: nowrap;
                    /*width: 100%;*/
                    text-overflow: ellipsis;
                }

                .xingbie {
                    float: left;
                    height: 25px;

                    .nanxing, .nvxing {
                        padding-left: 5px;
                        font-size: 17px;
                        line-height: 30px;
                    }

                    .nanxing {
                        color: #46B6EF;
                    }

                    .nvxing {
                        color: #F37E7D;
                    }
                }

                .info-breif {
                    width: 100%;
                    color: #999999;
                    font-size: 14px;
                    word-break: break-all;
                    padding-top: 10px;
                    padding-left: 1px;
                    float: left;
                }
            }


            .info-avatar {
                overflow: hidden;
                border-radius: 2px;
                width: 60px;
                height: 60px;
                float: left;
                cursor: pointer;

                img {
                    position: relative;
                    width: 100%;
                    height: 100%;
                }
            }
        }

        .info-body {
            border-bottom: 1px solid #E7E7E7;
            padding: 20px 0;
            font-size: 14px;
            line-height: 30px;

            &:last-of-type {
                border-bottom: none;
            }

            .ready-to-mark {
                cursor: pointer;

                &:hover {
                    background-color: #E7ECF0;
                    border: 1px solid #C0C4C7;
                    border-radius: 3px;
                    padding: 0;
                    line-height: normal;
                }
            }

            .to-mark {
                &.hide {
                    display: none;
                }

                .markname-input {
                    background-color: #F2F2F2;
                    border: none;
                    border-bottom: 1px solid #000000;
                    line-height: normal;

                    &:focus {
                        outline: 0;
                    }
                }
            }

            td.info-item-field {
                margin-bottom: 18px;
                text-align: justify;
                text-justify: distribute-all-lines;
                text-align-last: justify;
                width: 50px;
                color: #999999;

                &:last-of-type {
                    margin-bottom: 0;
                }
            }

            td.info-item-value {
                padding-left: 40px;
            }
        }

        .info-foot {
            padding-top: 35px;

            /deep/ .send-btn {
                background-color: #1AAD19;
                cursor: pointer;
                width: 140px;
                color: #ffffff;
                line-height: 35px;
                text-align: center;
                font-size: 13px;
                margin: 0 auto;

                &:hover {
                    background-color: #129611;
                }
            }
        }
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .info-box {
            width: 100%;
            padding-top: 0;
            padding-bottom: 0;

            .info-head {
                padding-top: 25px;
                padding-bottom: 25px;
            }
        }
    }
</style>
