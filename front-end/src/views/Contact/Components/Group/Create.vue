<template>
    <a-modal
        title="验证信息"
        :visible="modelValue"
        :confirm-loading="confirmLoading"
        cancelText="取消"
        okText="创建"
        @ok="handleCreate"
        @cancel="handleCancel">
        <a-form ref="ruleForm"
                :model="form"
                autocomplete="off"
                :label-col="{span:6}"
                :wrapper-col="{span:12}">
            <a-form-item has-feedback label="群聊名称" name="group_name">
                <a-input v-model:value="form.group_name" placeholder="群聊名称" type="text"
                         autocomplete="off"/>
            </a-form-item>
            <a-form-item has-feedback label="选择成员">
                <!--<a-checkbox @change="handleMemberPick" class="friend-item" v-for="(friend, index) in friendList"
                            :key="index">
                    <div class="avatar">
                        <img :src="friend.friend.avatar"/>
                    </div>
                    <div class="realname">{{friend.realname}}</div>
                </a-checkbox>-->

                <a-checkbox-group v-model:value="form.members">
                    <a-checkbox class="friend-item" @change="handleMemberPick" v-for="(friend, index) in friendList"
                                :value="friend.friend_id"
                                :key="index">
                        <div class="avatar">
                            <img :src="friend.friend.avatar"/>
                        </div>
                        <div class="realname">{{friend.realname}}</div>
                    </a-checkbox>
                </a-checkbox-group>
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script lang="ts" setup>
    import {computed, reactive, ref, watch} from "vue";
    import {useStore} from "vuex";
    import Group from "@/api/Group";
    import {message} from "ant-design-vue";

    const props = defineProps({
        modelValue: Boolean,
    });
    const emits = defineEmits(['update:modelValue']);
    const store = useStore();
    let form = reactive({
        group_name: '',
        members: [],
    });
    let confirmLoading = ref(false);
    const friendList = computed(() => store.state.Contact.friendList);
    watch(() => props.modelValue, (v: Boolean) => {
        if (!v) {
            form.members = [];
            form.group_name = '';
        }
    });
    const handleCreate = () => {
        confirmLoading.value = true;
        Group.create(form.group_name, form.members).then((res: any) => {
            emits('update:modelValue', false);
            message.success(res.message);
            store.dispatch('Conversation/refreshConversationList');
            store.dispatch('Contact/refreshGroupList');
        }).finally(() => confirmLoading.value = false);
    };
    const handleCancel = () => {
        emits('update:modelValue', false);
    };
    const handleMemberPick = (e: Event) => {
    }
</script>

<style lang="less" scoped>
    .ant-checkbox-group {
        width: 100%;
    }

    .friend-item {
        padding: 15px 25px;
        width: 100%;
        margin-left: 0;
        display: flex;
        align-items: center;

        /deep/ .ant-checkbox + span {
            display: flex;
            align-items: center;
        }

        .avatar {
            width: 40px;
            height: 40px;
            display: inline-block;
            border-radius: 2px;
            overflow: hidden;

            img {
                width: 100%;
                height: 100%;
            }
        }

        .realname {
            display: inline-block;
            padding-left: 15px;
            font-size: 15px;
        }

        &:hover {
            background-color: #E8E7E7;
        }
    }
</style>
