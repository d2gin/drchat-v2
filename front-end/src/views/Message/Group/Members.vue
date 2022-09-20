<template>
    <a-drawer
            title="群成员"
            :width="width"
            :visible="modelValue"
            :body-style="{padding: 0}"
            :drawer-style="{backgroundColor: '#f5f5f5'}"
            :header-style="{backgroundColor: '#f5f5f5'}"
            placement="right"
            @close="handleClose">
        <a-card class="card" :body-style="{padding: 0}">
            <div class="member" v-for="(member, index) in members" :key="index">
                <div class="avatar">
                    <img :src="member.user_info.avatar"/>
                </div>
                <div class="nickname">{{member.user_info.nickname}}</div>
            </div>
        </a-card>
        <a-card class="card" :body-style="{padding: 0}">
            <div class="card-item">
                群聊名称
                <div class="item-content">
                    {{groupName}}
                </div>
            </div>
            <div class="card-item">群二维码</div>
            <div class="card-item">群公告</div>
            <div class="card-item">备注</div>
        </a-card>
        <a-card class="card" :body-style="{padding: 0}">
            <div class="card-item exit" @click="handleExit">删除并退出</div>
        </a-card>
    </a-drawer>
</template>

<script lang="ts" setup>
    import {onMounted, reactive, ref, watch} from "vue";
    import Group from "@/api/Group";
    import {isMobile} from "@/lib/Util";
    import {message, Modal} from "ant-design-vue";
    import {useRouter} from "vue-router";
    import {useStore} from "vuex";

    const props = defineProps({
        modelValue: Boolean,
        groupId: {type: Number, required: true},
        groupName: {type: String, required: true},
    });
    const emits = defineEmits(['update:modelValue']);
    const router = useRouter();
    const store = useStore();

    // 属性
    let width = ref(isMobile() ? '80%' : '420px');
    onMounted(() => {
    });

    let members = ref([]);

    watch(() => props.modelValue, (v: Boolean) => {
        if (v) {
            Group.loading(true).members(props.groupId).then((res: any) => members.value = res.data);
        } else {
        }
    });
    const handleClose = () => {
        emits('update:modelValue', false);
    };

    const handleExit = () => {
        Modal.confirm({
            title: '退出群聊',
            content: '确定退出当前群聊？',
            okText: "确定",
            cancelText: "再想想",
            onOk: () => {
                return Group.exit(props.groupId).then((res: any) => {
                    message.success(res.message);
                    emits('update:modelValue', false);
                    store.dispatch('Conversation/refreshConversationList');
                    store.dispatch('Contact/refreshGroupList');
                    //store.dispatch('Conversation/refreshHintCount');
                    router.push('/message');
                });
            },
        });
    };
</script>

<style lang="less" scoped>
    .card {
        margin-bottom: 10px;

        .card-item {
            padding: 10px;
            cursor: pointer;

            .item-content {
                font-size: 13px;
                color: #909090;
                float: right;
            }
        }

        .exit {
            text-align: center;
            color: #ff4000;
        }
    }

    .member {
        display: inline-block;
        //align-items: center;
        width: 40px;
        margin-left: 15px;
        padding-top: 20px;
        cursor: pointer;

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 2px;
            overflow: hidden;

            img {
                width: 100%;
                height: 100%;
            }
        }

        .nickname {
            font-size: 12px;
            text-align: center;
            line-height: 25px;
            color: #a0a0a0;
            padding-bottom: 10px;
        }
    }
</style>
