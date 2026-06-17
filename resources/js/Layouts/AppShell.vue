<template>
    <div class="app-shell">
        <AppSidebar :user="user" :brand="brand" :navigation="navigation" />

        <div class="shell-main">
            <AppTopbar :user="user" :brand="brand" :layout="layout" :navigation="navigation" />

            <main class="shell-content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppSidebar from '../components/Shell/AppSidebar.vue';
import AppTopbar from '../components/Shell/AppTopbar.vue';
import { useShellState } from '../composables/useShellState';

const shell = useShellState();
const page = usePage();

const user = computed(() => page.props.auth?.user || {});
const layout = computed(() => page.props.layout || {});
const brand = computed(() => layout.value.brand || {});
const navigation = computed(() => layout.value.navigation || { home: null, modules: [] });

watch(
    () => layout.value.shell,
    (nextShell) => {
        shell.syncNavigation({
            activeModule: nextShell?.activeModule || null,
            activeSubMenu: nextShell?.activeSubMenu || null,
        });
    },
    {
        immediate: true,
        deep: true,
    },
);

watch(
    () => page.url,
    () => {
        shell.closeMobileSidebar();
        shell.resetInteractionState();
    },
);
</script>
