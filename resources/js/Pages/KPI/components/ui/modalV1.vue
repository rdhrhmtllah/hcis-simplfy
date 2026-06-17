<template>
    <transition name="modal-fade">
        <div
            v-if="show"
            class="modal fade show d-block"
            tabindex="-1"
            style="
                background-color: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(3px);
            "
        >
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="close"
                        >
                            {{ isReadOnly ? "Tutup" : "Batal" }}
                        </button>

                        <button
                            v-if="!isReadOnly"
                            type="button"
                            class="btn btn-primary"
                            @click="submit"
                            :disabled="loading"
                        >
                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm"
                            ></span>
                            <i v-else class="bi bi-check-circle me-2"></i>
                            {{ submitText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: "ModalV1",
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: "Modal Title",
        },
        submitText: {
            type: String,
            default: "Submit Form",
        },
        loading: {
            type: Boolean,
            default: false,
        },
        isReadOnly: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["close", "submit"],
    methods: {
        close() {
            this.$emit("close");
        },
        submit() {
            this.$emit("submit");
        },
    },
};
</script>

<style scoped>
/* Variabel Warna */
*,
*::before,
*::after {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #a5b4fc;
    --secondary: #64748b;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --light: #f8fafc;
    --dark: #1e293b;
    --surface: #ffffff;
    --surface-soft: #f1f5f9;
    --border: #e2e8f0;
    --text: #334155;
    --text-muted: #64748b;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modal-backdrop.show {
    backdrop-filter: blur(3px);
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header {
    background-color: var(--surface-soft);
    border-bottom: 1px solid var(--border);
    padding: 1.5rem;
}
.modal-title {
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.modal-title .icon-wrapper {
    background-color: var(--primary-light);
    color: var(--primary-dark);
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    background-color: var(--surface-soft);
    border-top: 1px solid var(--border);
    padding: 1rem 2rem;
}

/* Transisi untuk modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
.modal-fade-enter-active .modal-dialog,
.modal-fade-leave-active .modal-dialog {
    transition: transform 0.3s ease;
}
.modal-fade-enter-from .modal-dialog,
.modal-fade-leave-to .modal-dialog {
    transform: scale(0.95) translateY(-20px);
}

@media (max-width: 768px) {
    .modal-container {
        margin: 0.5rem;
        max-height: 83vh;
    }
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 1rem;
    }
    .modal-footer .btn-modern {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .modal-container.assign-modal {
        width: 98%;
        max-height: 90vh;
    }
}
</style>
