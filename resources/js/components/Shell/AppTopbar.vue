<template>
    <header ref="topbarRoot" class="shell-topbar">
        <div class="shell-topbar__inner">
            <!-- LEFT: GREETING -->
            <div class="shell-topbar__left">
                <button
                    class="shell-topbar-action shell-btn d-lg-none"
                    type="button"
                    aria-label="Toggle navigation"
                    @click.stop="shell.togglePrimarySidebar()"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div class="shell-greeting">
                    <div class="shell-greeting__eyebrow">
                        <i class="bi bi-stars"></i>
                        <span>{{ greetingLabel }}</span>
                    </div>
                    <div class="shell-greeting__title">Halo, {{ firstName }}</div>
                </div>
            </div>

            <!-- RIGHT: CLOCK + ACTIONS + ACTIVE USER MODULE -->
            <div class="shell-topbar__right">
                <div class="shell-clock d-none d-md-flex">
                    <div class="shell-clock__time">
                        <i class="bi bi-clock"></i>
                        <span>{{ currentTimeText }}</span>
                    </div>
                    <div class="shell-clock__date">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ currentDateText }}</span>
                    </div>
                </div>

                <div class="shell-topbar-divider d-none d-md-block"></div>

                <div class="shell-action-group">
                    <!-- MESSAGES -->
                    <div class="position-relative">
                        <button
                            class="shell-topbar-icon shell-btn"
                            :class="{ 'is-active is-blue': shell.state.showMessages }"
                            type="button"
                            aria-label="Pesan"
                            @click.stop="toggleMessages"
                        >
                            <i class="bi bi-envelope"></i>
                            <span
                                v-if="messages.length && !shell.state.showMessages"
                                class="shell-dot shell-dot--blue"
                            ></span>
                        </button>

                        <transition name="panel-pop">
                            <div v-if="shell.state.showMessages" class="shell-topbar-popover">
                                <div class="shell-popover-head">
                                    <div>
                                        <div class="shell-popover-title">Pesan Terbaru</div>
                                    </div>
                                    <i class="bi bi-chat-square-text text-primary"></i>
                                </div>

                                <div class="shell-popover-body shell-scrollbar">
                                    <div v-for="message in messages" :key="message.id" class="shell-message-row">
                                        <div class="shell-message-avatar">{{ message.initial || 'M' }}</div>
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="shell-message-meta">
                                                <span class="shell-message-sender text-truncate">{{
                                                    message.sender
                                                }}</span>
                                                <span class="shell-message-time">{{ message.time }}</span>
                                            </div>
                                            <div class="shell-message-text text-truncate">{{ message.text }}</div>
                                        </div>
                                    </div>

                                    <div v-if="!messages.length" class="shell-empty-state">Tidak ada pesan baru.</div>
                                </div>

                                <button class="shell-popover-footer shell-btn" type="button">Buka Semua Pesan</button>
                            </div>
                        </transition>
                    </div>

                    <!-- NOTIFICATIONS -->
                    <div class="position-relative">
                        <button
                            class="shell-topbar-icon shell-btn"
                            :class="{ 'is-active is-red': shell.state.showNotifications }"
                            type="button"
                            aria-label="Notifikasi"
                            @click.stop="toggleNotifications"
                        >
                            <i class="bi bi-bell"></i>
                            <span v-if="unreadNotificationsCount && !shell.state.showNotifications" class="shell-badge">
                                {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
                            </span>
                        </button>

                        <transition name="panel-pop">
                            <div v-if="shell.state.showNotifications" class="shell-topbar-popover">
                                <div class="shell-popover-head">
                                    <div>
                                        <div class="shell-popover-title">Notifikasi Sistem</div>
                                    </div>
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>

                                <div class="shell-popover-body shell-scrollbar">
                                    <div v-for="item in notifications" :key="item.id" class="shell-notification-row">
                                        <div
                                            class="shell-notification-avatar"
                                            :class="`tone-${item.severity || item.type || 'info'}`"
                                        >
                                            <i :class="notificationIcon(item)"></i>
                                        </div>
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="shell-message-meta">
                                                <span class="shell-notification-title text-truncate">{{
                                                    item.title
                                                }}</span>
                                                <span class="shell-message-time">{{
                                                    formatRelativeTime(item.created_at || item.time)
                                                }}</span>
                                            </div>
                                            <div class="shell-message-text text-truncate">
                                                {{ item.message || item.desc || 'Tidak ada detail tambahan.' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="!notifications.length" class="shell-empty-state">
                                        Belum ada notifikasi baru.
                                    </div>
                                </div>

                                <button
                                    class="shell-popover-footer is-red shell-btn"
                                    type="button"
                                    @click="markAllAsRead"
                                >
                                    Bersihkan Riwayat
                                </button>
                            </div>
                        </transition>
                    </div>

                    <!-- HELP -->
                    <div class="position-relative">
                        <button
                            class="shell-topbar-icon shell-btn"
                            :class="{ 'is-active is-dark': shell.state.showHelp }"
                            type="button"
                            aria-label="Bantuan"
                            @click.stop="toggleHelp"
                        >
                            <i class="bi bi-question-circle"></i>
                        </button>

                        <transition name="panel-pop">
                            <div v-if="shell.state.showHelp" class="shell-topbar-popover shell-topbar-popover--sm">
                                <div class="shell-popover-head">
                                    <div class="shell-popover-title">Pusat Bantuan</div>
                                </div>

                                <div class="shell-help-list">
                                    <button
                                        v-for="item in resolvedHelpItems"
                                        :key="item.label"
                                        class="shell-help-row shell-btn"
                                        type="button"
                                    >
                                        <span class="shell-help-icon">
                                            <i :class="item.icon"></i>
                                        </span>
                                        <span class="shell-help-copy">
                                            <span class="shell-help-title text-truncate">{{ item.label }}</span>
                                            <span class="shell-help-desc text-truncate">{{ item.description }}</span>
                                        </span>
                                    </button>
                                </div>

                                <div class="shell-help-version">V.2.4.0 EVO SYSTEMS</div>
                            </div>
                        </transition>
                    </div>
                </div>

                <div class="shell-topbar-divider d-none d-lg-block"></div>

                <button class="shell-module-pill shell-btn" type="button" @click="goToActiveModule">
                    <span class="shell-module-pill__left">
                        <span class="shell-module-pill__icon">
                            <i :class="activeModuleIcon"></i>
                        </span>
                        <span class="shell-module-pill__code">{{ activeModuleDisplay }}</span>
                    </span>

                    <span class="shell-module-pill__divider"></span>

                    <span class="shell-module-pill__user-wrap">
                        <span class="shell-module-pill__user">{{ userName }}</span>
                        <span class="shell-module-pill__nik">{{ userNik }}</span>
                    </span>
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useShellState } from '../../composables/useShellState';

const shell = useShellState();
const topbarRoot = ref(null);

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
    brand: {
        type: Object,
        default: () => ({}),
    },
    layout: {
        type: Object,
        default: () => ({}),
    },
    navigation: {
        type: Object,
        default: () => ({
            home: null,
            modules: [],
        }),
    },
});

const notifications = computed(() => props.layout?.notifications || []);
const messages = computed(() => props.layout?.messages || []);
const helpItems = computed(() => props.layout?.help || []);
const shellMeta = computed(() => props.layout?.shell || {});
const unreadNotificationsCount = computed(() => props.layout?.unreadNotificationsCount || 0);
const userName = computed(() => props.user?.name || props.user?.username || 'User');
const userNik = computed(() => props.user?.nik || '-');
const firstName = computed(() => String(userName.value).split(' ')[0] || userName.value);
const homeUrl = computed(() => props.navigation?.home?.url || '/homepage');

const activeModuleCode = computed(() => {
    if (shellMeta.value?.activeModule === 'HOME') {
        return 'HOME';
    }

    return shellMeta.value?.activeModule || 'APP';
});

const activeModuleDisplay = computed(() => (activeModuleCode.value === 'HOME' ? 'DASHBOARD' : activeModuleCode.value));

const activeModuleIcon = computed(() => {
    const activeCode = activeModuleCode.value;

    if (activeCode === 'HOME') {
        return 'bi bi-house-door-fill';
    }

    const activeModule = (props.navigation?.modules || []).find((module) => module.code === activeCode);
    return activeModule?.icon || 'bi bi-grid-1x2-fill';
});

const activeModuleUrl = computed(() => shellMeta.value?.activeModuleUrl || homeUrl.value);

const resolvedHelpItems = computed(() => {
    if (helpItems.value.length) {
        return helpItems.value.map((item) => ({
            icon: item.icon || 'bi bi-life-preserver',
            label: item.label,
            description: item.description,
        }));
    }

    return [
        { icon: 'bi bi-book', label: 'Panduan Pengguna', description: 'Tutorial penggunaan modul' },
        { icon: 'bi bi-life-preserver', label: 'Tiket Support', description: 'Hubungi tim IT Development' },
        { icon: 'bi bi-chat-dots', label: 'Live Chat', description: 'Konsultasi admin HRD' },
    ];
});

const currentTime = ref(new Date());
let timer = null;

const greetingLabel = computed(() => {
    const hour = currentTime.value.getHours();
    if (hour < 11) return 'SELAMAT PAGI';
    if (hour < 15) return 'SELAMAT SIANG';
    if (hour < 19) return 'SELAMAT SORE';
    return 'SELAMAT MALAM';
});

const currentTimeText = computed(() =>
    currentTime.value.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    }),
);

const currentDateText = computed(() =>
    currentTime.value.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }),
);

function notificationIcon(item) {
    const tone = String(item?.severity || item?.type || '').toLowerCase();

    if (tone.includes('security')) return 'bi bi-shield-check';
    if (tone.includes('warning') || tone.includes('alert')) return 'bi bi-exclamation-triangle';
    if (tone.includes('success')) return 'bi bi-check-circle';
    return 'bi bi-bell';
}

function formatRelativeTime(value) {
    if (!value) {
        return '';
    }

    if (typeof value === 'string' && value.match(/^\d+[mh]$/i)) {
        return value;
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return String(value);
    }

    return parsed.toLocaleString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: 'short',
    });
}

function toggleMessages() {
    shell.togglePopover('showMessages');
}

function toggleNotifications() {
    shell.togglePopover('showNotifications');
}

function toggleHelp() {
    shell.togglePopover('showHelp');
}

async function markAllAsRead() {
    shell.resetInteractionState();

    try {
        await axios.post('/homepage/notifications/read-all');
        router.reload({
            preserveScroll: true,
            preserveState: true,
        });
    } catch (error) {
        console.error('Failed to mark notifications as read:', error);
    }
}

function goToActiveModule() {
    router.visit(activeModuleUrl.value || homeUrl.value);
}

function handleOutsideClick(event) {
    if (!topbarRoot.value) {
        return;
    }

    if (!topbarRoot.value.contains(event.target)) {
        shell.resetInteractionState();
    }
}

onMounted(() => {
    timer = window.setInterval(() => {
        currentTime.value = new Date();
    }, 1000);

    document.addEventListener('mousedown', handleOutsideClick);
});

onBeforeUnmount(() => {
    if (timer) {
        window.clearInterval(timer);
    }

    document.removeEventListener('mousedown', handleOutsideClick);
});
</script>

<style scoped>
.bi {
    display: inline-table;
}

.shell-topbar,
.shell-topbar * {
    box-sizing: border-box;
}

.shell-topbar {
    position: sticky;
    top: 0;
    z-index: 30;
    height: 4rem;
    background: rgba(255, 255, 255, 0.52);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.62);
    color: #0f172a;
    font-size: 13px;
    transition: all 500ms cubic-bezier(0.2, 0.8, 0.2, 1);
}

.shell-topbar__inner {
    height: 100%;
    padding: 0 2.5rem 0 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
}

.shell-btn {
    border: 0;
    outline: 0;
    background: transparent;
    font: inherit;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.shell-topbar__left,
.shell-topbar__right,
.shell-action-group {
    display: flex;
    align-items: center;
}

.shell-topbar__left {
    min-width: 0;
    gap: 1rem;
}

.shell-topbar__right {
    margin-left: auto;
    gap: 1.5rem;
    flex-shrink: 0;
}

.shell-action-group {
    gap: 0.25rem;
}

.shell-topbar-action {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.9rem;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 260ms ease;
}

.shell-topbar-action:hover {
    background: #f1f5f9;
    color: #4f46e5;
}

.shell-greeting {
    min-width: 0;
    display: flex;
    flex-direction: column;
    text-align: left;
}

.shell-greeting__eyebrow {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.32rem;
    color: #94a3b8;
    font-size: 0.625rem;
    line-height: 1;
    font-weight: 950;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.shell-greeting__eyebrow i {
    color: #f59e0b;
    font-size: 0.88rem;
    letter-spacing: 0;
}

.shell-greeting__title {
    color: #0f172a;
    font-size: 1rem;
    line-height: 1;
    font-weight: 950;
    letter-spacing: -0.025em;
}

.shell-clock {
    flex-direction: column;
    align-items: flex-end;
}

.shell-clock__time {
    display: flex;
    align-items: center;
    gap: 0.42rem;
    color: #0f172a;
    font-size: 0.875rem;
    line-height: 1;
    font-weight: 950;
}

.shell-clock__time i {
    color: #4f46e5;
    font-size: 0.82rem;
}

.shell-clock__date {
    display: flex;
    align-items: center;
    gap: 0.34rem;
    margin-top: 0.38rem;
    color: #94a3b8;
    font-size: 0.68rem;
    line-height: 1;
    font-weight: 800;
}

.shell-clock__date i {
    font-size: 0.68rem;
}

.shell-topbar-divider {
    width: 1px;
    height: 2.5rem;
    background: rgba(226, 232, 240, 0.78);
}

.shell-topbar-icon {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.8rem;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.16rem;
    transition:
        background 240ms ease,
        color 240ms ease,
        box-shadow 240ms ease,
        transform 240ms ease;
}

.shell-topbar-icon:hover {
    background: #f1f5f9;
    color: #4f46e5;
}

.shell-topbar-icon.is-active {
    color: #ffffff;
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
}

.shell-topbar-icon.is-blue {
    background: #4f46e5;
}

.shell-topbar-icon.is-red {
    background: #ef4444;
}

.shell-topbar-icon.is-dark {
    background: #0f172a;
}

.shell-dot {
    position: absolute;
    top: 0.72rem;
    right: 0.72rem;
    width: 0.42rem;
    height: 0.42rem;
    border-radius: 999px;
    border: 2px solid #ffffff;
    animation: pulse-dot 1.25s ease-in-out infinite;
}

.shell-dot--blue {
    background: #4f46e5;
}

.shell-badge {
    position: absolute;
    top: 0.35rem;
    right: 0.32rem;
    min-width: 1rem;
    height: 1rem;
    padding: 0 0.2rem;
    border-radius: 999px;
    border: 2px solid #ffffff;
    background: #ef4444;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.56rem;
    line-height: 1;
    font-weight: 950;
}

.shell-topbar-popover {
    position: absolute;
    top: calc(100% + 0.75rem);
    right: 0;
    z-index: 50;
    width: 20rem;
    border-radius: 1.25rem;
    border: 1px solid rgba(226, 232, 240, 0.95);
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(18px);
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.18);
    overflow: hidden;
}

.shell-topbar-popover--sm {
    width: 18rem;
}

.shell-popover-head {
    min-height: 3.25rem;
    padding: 0.95rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.shell-popover-title {
    color: #0f172a;
    font-size: 0.75rem;
    line-height: 1;
    font-weight: 950;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.shell-popover-body {
    max-height: 15rem;
    overflow-y: auto;
}

.shell-message-row,
.shell-notification-row {
    display: flex;
    gap: 0.75rem;
    padding: 0.75rem;
    border-bottom: 1px solid #f8fafc;
    transition: background 200ms ease;
}

.shell-message-row:hover,
.shell-notification-row:hover {
    background: #f8fafc;
}

.shell-message-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 999px;
    background: #dbeafe;
    color: #1d4ed8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.62rem;
    font-weight: 950;
}

.shell-message-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
    min-width: 0;
}

.shell-message-sender,
.shell-notification-title {
    color: #0f172a;
    font-size: 0.7rem;
    line-height: 1;
    font-weight: 850;
}

.shell-message-time {
    flex-shrink: 0;
    color: #94a3b8;
    font-size: 0.56rem;
    line-height: 1;
    font-weight: 700;
}

.shell-message-text {
    margin-top: 0.3rem;
    color: #64748b;
    font-size: 0.64rem;
    line-height: 1.25;
    font-weight: 550;
}

.shell-notification-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 0.75rem;
    background: #fee2e2;
    color: #dc2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.shell-notification-avatar.tone-security {
    background: #fef3c7;
    color: #d97706;
}

.shell-notification-avatar.tone-success {
    background: #dcfce7;
    color: #16a34a;
}

.shell-notification-avatar.tone-info {
    background: #dbeafe;
    color: #4f46e5;
}

.shell-empty-state {
    padding: 1rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 700;
}

.shell-popover-footer {
    width: 100%;
    min-height: 2.5rem;
    color: #94a3b8;
    background: #ffffff;
    font-size: 0.62rem;
    font-weight: 950;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    transition: all 200ms ease;
}

.shell-popover-footer:hover {
    background: #f8fafc;
    color: #4f46e5;
}

.shell-popover-footer.is-red:hover {
    color: #ef4444;
}

.shell-help-list {
    padding: 0.5rem;
}

.shell-help-row {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem;
    border-radius: 0.9rem;
    text-align: left;
    transition: background 220ms ease;
}

.shell-help-row:hover {
    background: #f8fafc;
}

.shell-help-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 0.75rem;
    background: #f1f5f9;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 220ms ease;
}

.shell-help-row:hover .shell-help-icon {
    background: #4f46e5;
    color: #ffffff;
}

.shell-help-copy {
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.shell-help-title {
    color: #0f172a;
    font-size: 0.68rem;
    line-height: 1;
    font-weight: 950;
}

.shell-help-desc {
    margin-top: 0.26rem;
    color: #94a3b8;
    font-size: 0.56rem;
    line-height: 1;
    font-weight: 600;
}

.shell-help-version {
    padding: 0.75rem;
    background: rgba(248, 250, 252, 0.7);
    color: #94a3b8;
    text-align: center;
    font-size: 0.56rem;
    line-height: 1;
    font-weight: 850;
    letter-spacing: 0.02em;
    font-style: italic;
}

.shell-module-pill {
    min-height: 3rem;
    padding: 0.5rem 0.95rem 0.5rem 0.75rem;
    border-radius: 1rem;
    border: 1px solid #dcfce7;
    background: rgba(240, 253, 244, 0.72);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 240ms ease;
}

.shell-module-pill:hover {
    background: rgba(220, 252, 231, 0.72);
}

.shell-module-pill__left {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding-right: 0.65rem;
    border-right: 1px solid rgba(187, 247, 208, 0.9);
}

.shell-module-pill__icon {
    width: 1.35rem;
    height: 1.35rem;
    border-radius: 0.45rem;
    background: #ffffff;
    color: #4f46e5;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
}

.shell-module-pill__code {
    color: #15803d;
    font-size: 0.68rem;
    line-height: 1;
    font-weight: 950;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.shell-module-pill__divider {
    display: none;
}

.shell-module-pill__user-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    line-height: 1;
}

.shell-module-pill__user {
    max-width: 8rem;
    color: #0f172a;
    font-size: 0.62rem;
    line-height: 1;
    font-weight: 950;
    text-transform: uppercase;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.shell-module-pill__nik {
    margin-top: 0.26rem;
    color: #64748b;
    font-size: 0.5rem;
    line-height: 1;
    font-weight: 800;
}

.panel-pop-enter-active,
.panel-pop-leave-active {
    transition:
        opacity 200ms ease,
        transform 200ms ease;
}

.panel-pop-enter-from,
.panel-pop-leave-to {
    opacity: 0;
    transform: translateY(-0.5rem) scale(0.98);
}

.shell-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.shell-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.shell-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 999px;
}

.shell-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@keyframes pulse-dot {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.45;
        transform: scale(1.2);
    }
}

.shell-topbar button:focus-visible {
    outline: 2px solid rgba(37, 99, 235, 0.28);
    outline-offset: 2px;
}

@media (max-width: 1199.98px) {
    .shell-topbar__inner {
        padding: 0 1.25rem 0 1rem;
    }

    .shell-topbar__right {
        gap: 0.75rem;
    }
}

@media (max-width: 767.98px) {
    .shell-topbar {
        height: auto;
        min-height: 3.5rem;
    }

    .shell-greeting {
        display: none;
    }

    .shell-greeting__eyebrow {
        gap: 0;
        margin: 0;
    }

    .shell-topbar__left {
        gap: 0.3rem;
    }

    .shell-topbar__inner {
        padding: 0.55rem 1rem;
        gap: 0.8rem;
    }

    .shell-greeting__title {
        font-size: 0.92rem;
    }

    .shell-topbar__right {
        gap: 0.55rem;
    }

    .shell-module-pill {
        padding: 0rem 0.35rem;
        min-height: 2rem;
    }

    .shell-module-pill__user-wrap {
        display: none;
    }

    .shell-module-pill__left {
        padding-right: 0;
        border-right: 0;
    }

    .shell-topbar-popover {
        position: fixed;
        top: 4.75rem;
        right: 1rem;
        width: min(20rem, calc(100vw - 2rem));
    }
}
</style>
