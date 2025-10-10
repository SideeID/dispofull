<template x-if="showAttachment">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5"
            x-data="attachmentModal(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Lampiran Surat</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="space-y-4 text-sm">
                <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300" x-show="!loading && attachments.length">
                    <template x-for="a in attachments" :key="a.id">
                        <li class="flex items-center justify-between gap-3 p-2 rounded bg-gray-50 dark:bg-gray-700/40">
                            <div class="flex items-center gap-2">
                                <i data-feather="file" class="w-4 h-4 text-amber-500"></i>
                                <span x-text="a.original_name || a.name"></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] text-gray-400"
                                    x-text="formatSize(a.file_size || a.size)"></span>
                                <a :href="a.file_url || '#'" target="_blank"
                                    class="text-amber-600 dark:text-amber-400 hover:underline text-[11px]">Unduh</a>
                            </div>
                        </li>
                    </template>
                </ul>
                <div class="text-center py-6 text-[11px] text-gray-400" x-show="!loading && !attachments.length">Tidak
                    ada lampiran.</div>
                <div class="flex items-center justify-center py-6" x-show="loading">
                    <div class="animate-spin h-5 w-5 rounded-full border-2 border-amber-500 border-t-transparent"></div>
                </div>
                <div class="pt-2 border-t border-dashed border-gray-200 dark:border-gray-700/60">
                    <form class="space-y-3" @submit.prevent="upload()">
                        <div>
                            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tambah
                                Lampiran</label>
                            <input x-ref="file" type="file"
                                class="block w-full text-[11px] text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-500/10 dark:file:text-amber-300" />
                            <p class="mt-1 text-[10px] text-gray-400">PDF / Gambar (max 5MB)</p>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" @click="closeAll()"
                                class="px-3 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Tutup</button>
                            <button type="submit" :disabled="uploading"
                                class="px-3 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
                                <span x-show="!uploading">Upload</span>
                                <span x-show="uploading" class="flex items-center gap-1"><span
                                        class="animate-spin h-4 w-4 rounded-full border-2 border-white border-t-transparent"></span>
                                    Proses</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    function attachmentModal(sel) {
        return {
            selected: sel,
            attachments: [],
            loading: false,
            uploading: false,
            init() {
                this.fetch();
            },
            async fetch() {
                if (!this.selected) return;
                this.loading = true;
                try {
                    const res = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/attachments`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (res.ok) {
                        this.attachments = await res.json();
                    } else {
                        this.attachments = []
                    }
                    this.$nextTick(() => {
                        if (window.feather) feather.replace();
                    });
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },
            formatSize(bytes) {
                if (!bytes) return '0B';
                const units = ['B', 'KB', 'MB', 'GB'];
                let i = 0;
                while (bytes > 1024 && i < units.length - 1) {
                    bytes /= 1024;
                    i++;
                }
                return `${bytes.toFixed(1)}${units[i]}`;
            },
            async upload() {
                if (!this.$refs.file.files.length) return;
                const file = this.$refs.file.files[0];
                const form = new FormData();
                form.append('file', file);
                this.uploading = true;
                try {
                    const res = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/attachments`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: form
                    });
                    if (res.ok) {
                        await this.fetch();
                        this.$refs.file.value = '';
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.uploading = false;
                }
            }
        }
    }
</script>
