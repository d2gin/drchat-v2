<template>
    <div class="search-box">
        <div class="input-search">
            <input v-model="keywords" placeholder="搜索" @keyup.enter="search"/>
            <i class="iconfont icon-sousuo isearch"/>
            <i :class="['iconfont', 'icon-chahao', 'icancel', keywords.length > 0 ? '' : 'hide']" @click="handleClear"/>
        </div>
        <div class="plus" @click="plus">
            <i class="iconfont icon-jia1"/>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import {onMounted, ref} from "vue";
    // 内置
    const props = defineProps({
        value: {
            type: String,
            default: '',
        }
    });
    const emits = defineEmits(['plus', 'search']);
    // 属性
    let keywords = ref('');
    onMounted(() => {
        keywords.value = props.value;
    });
    // 方法
    const plus = () => {
        emits('plus', keywords);
    };
    const handleClear = () => {
        keywords.value = '';
    };
    const search = () => {
        emits('search', keywords);
    };
</script>

<style lang="less" scoped>
    .search-box {
        position: relative;
    }

    .search-box:after {
        content: "";
        display: table;
        clear: both;
    }

    .input-search {
        position: relative;
        width: 190px;
        float: left;
    }

    .input-search .isearch, .input-search .icancel {
        position: absolute;
        color: #585858;
        top: 5px;
        font-size: 10px;
    }

    .input-search .isearch {
        left: 6px;
    }

    .input-search .icancel {
        right: 6px;
        cursor: pointer;
    }

    .input-search .icancel.hide {
        display: none;
    }

    .input-search input {
        border-radius: 4px;
        background-color: #DBD9D8;
        border: none;
        padding: 4px 22px;
        height: 25px;
        width: 100%;
    }

    .input-search input:focus {
        border: none;
        outline: 0;
        background-color: #FFFFFF;
    }

    .input-search input:focus ~ .icancel {
        color: #CFCFCF;
    }

    .search-box .plus {
        display: inline-block;
        padding: 3px 6px;
        margin-left: 10px;
        background-color: #DBD9D8;
        border-radius: 4px;
        height: 25px;
        float: left;
        line-height: 1em;
        color: #585858;
        font-size: 16px;
        cursor: pointer;
    }

    .search-box .plus .iconfont {
        font-size: 9px;
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .input-search {
            width: calc(100% - 25px);
            padding-right: 10px;

            .icancel {
                right: 16px;
            }

            /*input {
                background-color: #FFFFFF;
                border: 1px solid #DBD9D8;

                &:focus {
                    border: 1px solid #DBD9D8;
                }
            }*/
        }

        .search-box {
            .plus {
                margin-left: 0;
                /*background-color: #FFFFFF;*/
                /*border: 1px solid #DBD9D8;*/
            }
        }
    }
</style>
