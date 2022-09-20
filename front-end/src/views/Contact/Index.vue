<template>
    <div class="contact-box">
        <div class="data-list">
            <div style="padding: 0 0 10px 12px">
                <Search @search="handleSearch" @plus="handleSearch" value=""/>
            </div>
            <div class="data-title">
                新的朋友
            </div>
            <ContactItem :item="newFriendItem" :for-recent="false" @pick="showRequestList = true"/>
            <div class="data-panel">
                <div class="data-title">
                    群聊
                </div>
                <ContactItem :item="createGroupItem" :for-recent="false" @pick="showCreateGroupForm = true"/>
                <ContactItem v-for="(item,index) in groupList" :key="index"
                             :item="item"
                             :for-recent="false"
                             @pick="pickGroup"/>
            </div>
            <div class="data-panel">
                <ContactItem v-for="(item, index) in friendList"
                             :key="index"
                             :item="item"
                             :for-recent="false"
                             :online="!!item.friend.is_online"
                             @pick="pickFriend"/>
            </div>
        </div>

        <RequestList v-model="showRequestList"/>
        <CreateGroup v-model="showCreateGroupForm"/>

        <UserInfo v-model="searchInfo"/>
    </div>
</template>
<script lang="ts">
    import useController from "@/lib/useController";
    import ContactController from "@/controller/ContactController";

    export default useController(new ContactController());
</script>
<style lang="less" scoped>
    .contact-box {
        position: relative;
        height: 100%;
        padding: 25px 0 0 0;

    }

    .flex-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
    }
</style>
