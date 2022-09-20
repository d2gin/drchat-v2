<template>
    <router-view/>
</template>

<script lang="ts">
    import AppController from "@/controller/AppController";
    import useController from "@/lib/useController";

    export default useController(new AppController);
</script>

<style lang="less">
    html, body {
        height: 100%;
        width: 100%;
        padding: 0;
        margin: 0;
        overflow: hidden;
        /*overflow-x: auto;*/
    }

    * {
        box-sizing: border-box;
    }

    #app {
        width: 100%;
        height: 100%;
    }
</style>
