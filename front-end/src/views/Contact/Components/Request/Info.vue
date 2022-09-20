<template>
    <a-drawer
        title="用户资料"
        :width="width"
        :visible="modelValue ? true : false"
        placement="left"
        :body-style="{ paddingBottom: '80px', }"
        :drawer-style="{backgroundColor: '#f5f5f5'}"
        :header-style="{backgroundColor: '#f5f5f5'}"
        @close="onCloseFriend">
        <a-modal
            title="验证信息"
            :visible="addConfirm.visible"
            :confirm-loading="addConfirm.confirmLoading"
            cancelText="取消"
            okText="添加"
            @ok="handleConfirmAdd"
            @cancel="handleCancelConfirm">

            <a-form ref="ruleForm"
                    :model="addConfirm.form"
                    autocomplete="off"
                    :label-col="{span:6}"
                    :wrapper-col="{span:12}">
                <a-form-item has-feedback label="备注名" name="remark_name">
                    <a-input v-model:value="addConfirm.form.remark_name" placeholder="备注名" type="text"
                             autocomplete="off"/>
                </a-form-item>
                <a-form-item has-feedback label="验证信息" name="message">
                    <a-mentions rows="3" placeholder="验证信息" v-model:value="addConfirm.form.message"/>
                </a-form-item>
            </a-form>
        </a-modal>
        <UserInfo v-if="modelValue" :info="modelValue" :can-contact="false">
            <template v-slot:info-foot>
                <div class="send-btn" v-if="!previewOnly" @click="handleShowConfirm">添加好友</div>
            </template>
        </UserInfo>
    </a-drawer>
</template>

<script lang="ts" setup>
    import {isMobile} from "@/lib/Util";
    import UserInfo from "@/components/Userinfo.vue";
    import {reactive, watch} from "vue";
    import Friend from "@/api/Friend";
    import {message} from "ant-design-vue";
    import {useStore} from "vuex";

    const props = defineProps({
        modelValue: Object,
        previewOnly: Boolean,
    });
    const emits = defineEmits(['update:modelValue']);
    const store = useStore();

    let width = isMobile() ? '90%' : '550';

    let addConfirm = reactive({
        confirmLoading: false,
        visible: false,
        form: {
            remark_name: '',
            message: '',
        },
    });
    // 方法
    const onCloseFriend = () => {
        emits('update:modelValue', null);
    };
    const handleConfirmAdd = () => {
        let user_id = props.modelValue?.id;
        if (!user_id) {
            message.error('缺失id');
            return false;
        }
        addConfirm.confirmLoading = true;
        Friend.addRequest(user_id, addConfirm.form.remark_name, addConfirm.form.message).then((res: any) => {
            message.success(res.message);
            store.dispatch('Contact/refreshFriendList');
            addConfirm.visible = false;
            emits('update:modelValue', null);
        }).finally(() => addConfirm.confirmLoading = false);
    };
    const handleCancelConfirm = () => {
        addConfirm.visible = false;
    };
    const handleShowConfirm = () => {
        addConfirm.visible = true;
    };
</script>

<style lang="less" scoped>
</style>
