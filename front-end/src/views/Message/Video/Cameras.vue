<template>
    <a-drawer
        v-model:visible="modelValue"
        @close="handleClose"
        placement="bottom">
        <a-list bordered item-layout="horizontal">
            <a-list-item v-for="(item, index) in cameras">
                <a-button size="small" type="primary" @click="handlePickCamera(index, item)">切换</a-button>
                {{ item.label }}
            </a-list-item>
        </a-list>
    </a-drawer>
</template>

<script lang="ts" setup>
    import {onMounted, watch} from "vue";

    const props = defineProps({
        modelValue: Boolean,
        cameras: Array,
    });
    const emits = defineEmits(['update:modelValue', 'pickCamera']);
    const handleClose = () => {
        emits('update:modelValue', false);
    };

    const handlePickCamera = (index: number, camera: any) => {
        emits('pickCamera', index, camera);
    };
</script>

<style scoped>

</style>
