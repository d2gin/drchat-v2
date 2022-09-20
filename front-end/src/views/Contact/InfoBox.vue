<template>
    <div v-if="info">
        <div class="close" v-if="!isMobile" @click="closeBox">
            <CloseOutlined/>
        </div>
        <Userinfo :info="info" @contact="handleContact" @tomark="handleMarkname">
            <template v-slot:info-foot>
                <div class="send-btn del" style="background-color: #ff4500; margin-top: 5px;"
                     @click="handleDelete">
                    删除Ta
                </div>
            </template>
        </Userinfo>
    </div>
</template>

<script lang="ts">
    import useController from "@/lib/useController";
    import InfoBoxController from "@/controller/InfoBoxController";

    export default useController(new InfoBoxController());
</script>
<style lang="less" scoped>
    .close {
        position: absolute;
        left: 20px;
        top: 10px;
        cursor: pointer;
    }
</style>
