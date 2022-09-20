<template>
    <a-drawer
        title="新的好友"
        :width="width"
        :visible="modelValue"
        :body-style="bodyStyle"
        :drawer-style="{backgroundColor: '#f5f5f5'}"
        :header-style="{backgroundColor: '#f5f5f5'}"
        @close="handleClose">
        <template v-for="(item, index) in list" :key="index">
            <common-item :item="item" @pick="showNewFriend">
                <template v-slot:part-three>
                    <div class="flex-container">
                        <a-popconfirm v-if="item.status <= Friend.STATUS_UNTIPS_REQUEST" title="确定通过好友请求吗？"
                                      ok-text="是"
                                      cancel-text="否"
                                      @confirm="handlePass(item)">
                            <div class="cm-btn">
                                接受
                            </div>
                        </a-popconfirm>
                        <a-popconfirm v-if="item.status <= Friend.STATUS_UNTIPS_REQUEST" title="确定拒绝好友请求吗？"
                                      ok-text="是"
                                      cancel-text="否"
                                      @confirm="handlePass(item, 2)">
                            <div class="cm-btn" style="background-color: #ff4500;margin-left: 5px;">
                                拒绝
                            </div>
                        </a-popconfirm>
                        <div class="cm-text" v-else-if="item.status == Friend.STATUS_PASS_REQUEST">
                            已添加
                        </div>
                        <div class="cm-text" v-else-if="item.status == Friend.STATUS_REFUSE_REQUEST">
                            已拒绝
                        </div>
                    </div>
                </template>
            </common-item>
        </template>
    </a-drawer>
    <RequestInfo v-model="friendInfo" :previewOnly="true"/>
</template>

<script lang="ts" setup>
    import CommonItem from "@/views/Contact/Components/Request/CommonItem.vue";
    import RequestInfo from "@/views/Contact/Components/Request/Info.vue";
    import {isMobile} from "@/lib/Util";
    import {onMounted, reactive, ref, watch} from "vue";
    import Friend from "@/api/Friend";
    import {message} from "ant-design-vue";

    const props = defineProps({
        modelValue: Boolean,
    });
    const emits = defineEmits(['close', 'update:modelValue', 'pass',]);
    // 属性
    let width = ref(isMobile() ? '90%' : '60%');
    let bodyStyle = reactive({
        padding: isMobile() ? 0 : '',
    });
    let friendInfo = ref(null);
    let list = ref([]);
    watch(() => props.modelValue, (v: any) => {
        if (v) {
            Friend.loading().requestList().then((res: any) => {
                list.value = res.data;
            });
        }
    });
    // 钩子
    onMounted(() => {
    });
    // 方法
    const handleClose = () => {
        emits('update:modelValue', false);
    };
    /**
     * 通过请求
     * @param requestItem
     */
    const handlePass = (requestItem: any) => {
        Friend.loading().passRequest(requestItem.id).then((res: any) => {
            emits('pass');
            return Friend.requestList().then((reqList: any) => {
                list.value = reqList.data;
                return Promise.resolve(res);
            });
        }).then((res: any) => message.success(res.message));
    };
    /**
     * 展示用户资料
     * @param item
     */
    const showNewFriend = (item: any) => {
        friendInfo.value = reactive(item.sender_info);
    };
</script>

<style lang="less" scoped>
    .cm-text {
        line-height: 50px;
        color: #A8A899;
        font-size: 12px;
    }

    .cm-btn {
        display: inline-block;
        background-color: #1AAD19;
        padding: 5px 10px;
        color: #ffffff;
        cursor: pointer;
        font-size: 12px;

        &:hover {
            background-color: #129611;
        }
    }
</style>
