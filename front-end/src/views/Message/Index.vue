<template>
    <div class="sc-box">
        <div class="data-list" @scroll="handleListScroll">
            <div style="margin: 0 0 10px 12px" v-if="!is_mobile">
                <Search/>
            </div>
            <template v-for="(item, index) in conversationList" :key="index">
                <ContactItem :item="item"
                             :online="!!item.object_info.is_online"
                             :actived="$route.query.conversation_id == item.id"
                             :selected="selected(item)"
                             @pick="pick"/>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
    import MessageController from "@/controller/MessageController";
    import useController from "@/lib/useController";

    export default useController(new MessageController());
</script>

<style lang="less" scoped>
    .sc-box {
        position: relative;
        height: 100%;
        padding: 25px 0 0 0;
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .sc-box {
            padding-top: 15px;
        }
    }
</style>
