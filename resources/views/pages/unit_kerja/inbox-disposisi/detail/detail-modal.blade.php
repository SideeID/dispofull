<template x-if="showDetail">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6 max-h-[90vh] overflow-y-auto"
            x-data="dispositionDetail(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4 sticky top-0 bg-white dark:bg-gray-800 pb-4 border-b border-gray-200 dark:border-gray-700 z-10">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Disposisi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="detail.letter_number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="loadingDetail">
                <div class="flex items-center justify-center py-12">
                    <span class="animate-spin h-8 w-8 rounded-full border-4 border-indigo-600 border-t-transparent"></span>
                </div>
            </template>

            <template x-if="!loadingDetail && detail.id">
                <div class="space-y-6">
                    <!-- Surat Info -->
                    <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-5 space-y-3">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i data-feather="file-text" class="w-4 h-4"></i>
                            Informasi Surat
                        </h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Nomor Surat</span>
                                <p class="font-medium text-gray-800 dark:text-gray-100" x-text="detail.letter_number"></p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Dari</span>
                                <p class="font-medium text-gray-800 dark:text-gray-100" x-text="detail.from"></p>
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Perihal</span>
                                <p class="font-medium text-gray-800 dark:text-gray-100" x-text="detail.subject"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Disposisi Info -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-5 space-y-3">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i data-feather="send" class="w-4 h-4"></i>
                            Disposisi
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Dari</span>
                                <p class="font-medium text-gray-800 dark:text-gray-100" x-text="detail.from_user + (detail.from_user_position ? ' · ' + detail.from_user_position : '')"></p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Instruksi</span>
                                <p class="text-gray-800 dark:text-gray-100 whitespace-pre-line" x-text="detail.instruction"></p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Prioritas</span>
                                    <p class="font-medium" :class="{
                                        'text-emerald-600 dark:text-emerald-400': detail.priority === 'low',
                                        'text-gray-600 dark:text-gray-300': detail.priority === 'normal',
                                        'text-amber-600 dark:text-amber-400': detail.priority === 'high',
                                        'text-rose-600 dark:text-rose-400': detail.priority === 'urgent'
                                    }" x-text="detail.priority === 'urgent' ? 'Urgent' : detail.priority === 'high' ? 'Tinggi' : detail.priority === 'low' ? 'Rendah' : 'Normal'"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Status</span>
                                    <p class="font-medium" :class="{
                                        'text-amber-600 dark:text-amber-400': detail.status === 'pending',
                                        'text-blue-600 dark:text-blue-400': detail.status === 'in_progress',
                                        'text-emerald-600 dark:text-emerald-400': detail.status === 'completed'
                                    }" x-text="detail.status === 'pending' ? 'Menunggu' : detail.status === 'in_progress' ? 'Dikerjakan' : 'Selesai'"></p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Batas Waktu</span>
                                    <p class="font-medium" :class="detail.is_overdue ? 'text-rose-600 dark:text-rose-400' : 'text-gray-800 dark:text-gray-100'" x-text="detail.due_date || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response (jika ada) -->
                    <div x-show="detail.response" class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-5 space-y-3">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i data-feather="message-square" class="w-4 h-4"></i>
                            Laporan / Response
                        </h4>
                        <p class="text-gray-800 dark:text-gray-100 whitespace-pre-line" x-text="detail.response"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="detail.completed_at">
                            Diselesaikan pada <span x-text="detail.completed_at"></span>
                        </p>
                    </div>

                    <!-- Attachments -->
                    <div x-show="detail.attachments && detail.attachments.length" class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <i data-feather="paperclip" class="w-4 h-4"></i>
                            Lampiran Surat (<span x-text="detail.attachments?.length || 0"></span>)
                        </h4>
                        <ul class="space-y-2">
                            <template x-for="att in detail.attachments" :key="att.id">
                                <li class="flex items-center gap-3 text-sm p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600/50">
                                    <i data-feather="file" class="w-4 h-4 text-gray-400"></i>
                                    <a :href="att.url" target="_blank" class="flex-1 text-indigo-600 dark:text-indigo-400 hover:underline" x-text="att.name"></a>
                                    <span class="text-xs text-gray-500" x-text="(att.size / 1024).toFixed(1) + ' KB'"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="markAsRead()" 
                            x-show="!detail.read_at" :disabled="submitting"
                            class="px-4 py-2 rounded-lg text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50">
                            <span x-show="!submitting">Tandai Dibaca</span>
                            <span x-show="submitting">Loading...</span>
                        </button>
                        <button type="button" @click="openResponse()" 
                            x-show="detail.status !== 'completed'"
                            class="px-4 py-2 rounded-lg text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2">
                            <i data-feather="edit" class="w-4 h-4"></i>
                            <span x-text="detail.response ? 'Update Response' : 'Berikan Response'"></span>
                        </button>
                        <button type="button" @click="closeAll()"
                            class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                            Tutup
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    function dispositionDetail(sel) {
        return {
            selected: sel,
            detail: {},
            loadingDetail: false,
            submitting: false,
            async init() {
                await this.fetchDetail();
                // Auto mark as read when opened
                if (this.detail.id && !this.detail.read_at) {
                    setTimeout(() => this.markAsRead(), 1000);
                }
            },
            async fetchDetail() {
                if (!this.selected?.id) return;
                this.loadingDetail = true;
                try {
                    const res = await fetch(`/unit-kerja/api/dispositions/${this.selected.id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const json = await res.json();
                        this.detail = json.data;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loadingDetail = false;
                    this.$nextTick(() => { if (window.feather) feather.replace(); });
                }
            },
            async markAsRead() {
                if (!this.detail.id || this.detail.read_at) return;
                this.submitting = true;
                try {
                    const res = await fetch(`/unit-kerja/api/dispositions/${this.detail.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });
                    if (res.ok) {
                        await this.fetchDetail();
                        this.$dispatch('refresh-dispositions');
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.submitting = false;
                }
            },
            openResponse() {
                this.$dispatch('open-modal', { modal: 'showResponse', row: this.detail });
            }
        }
    }
</script>
