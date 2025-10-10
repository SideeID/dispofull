<template x-if="showSign">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5"
            x-data="signModal(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tanda Tangan Digital</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="space-y-4 text-sm">
                <div
                    class="bg-gray-50 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Area tanda tangan (placeholder).
                        (Implementasi pad / upload di tahap lanjut)</p>
                    <div class="h-32 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 flex items-center justify-center text-[11px] text-gray-400"
                        x-text="padPlaceholder"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Metode</label>
                        <select x-model="form.signature_type"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="digital">Digital</option>
                            <option value="electronic">Electronic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Jenis
                            File</label>
                        <select
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="pdf">PDF</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan
                        (opsional)</label>
                    <textarea rows="3" x-model="form.notes"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100"
                        placeholder="Catatan untuk log..."></textarea>
                </div>
                <div class="border rounded-lg p-3 bg-gray-50 dark:bg-gray-700/40">
                    <div
                        class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">
                        Riwayat Tanda Tangan</div>
                    <ul class="space-y-2 text-[11px] text-gray-600 dark:text-gray-300"
                        x-show="!loading && signatures.length">
                        <template x-for="s in signatures" :key="s.id">
                            <li class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium" x-text="s.user"></span> · <span x-text="s.position || '-'"
                                        class="text-gray-500"></span>
                                    <div class="text-[10px]" x-text="s.signed_at"></div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full"
                                    :class="s.status === 'signed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' :
                                        'bg-amber-500/10 text-amber-600 dark:text-amber-400'"
                                    x-text="s.status"></span>
                            </li>
                        </template>
                    </ul>
                    <div class="text-center text-[11px] py-4 text-gray-400" x-show="!loading && !signatures.length">
                        Belum ada tanda tangan.</div>
                    <div class="flex items-center justify-center py-4" x-show="loading">
                        <div class="animate-spin h-5 w-5 rounded-full border-2 border-amber-500 border-t-transparent">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeAll()"
                        class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Tutup</button>
                    <button type="button" @click="sign()" :disabled="submitting"
                        class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">
                        <span x-show="!submitting">Tandatangani</span>
                        <span x-show="submitting" class="flex items-center gap-1"><span
                                class="animate-spin h-4 w-4 rounded-full border-2 border-white border-t-transparent"></span>
                            Proses</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    function signModal(sel) {
        return {
            selected: sel,
            signatures: [],
            loading: false,
            submitting: false,
            padPlaceholder: 'Signature Pad',
            form: {
                signature_type: 'digital',
                notes: '',
                signature_data: null
            },
            init() {
                this.fetch();
            },
            async fetch() {
                if (!this.selected) return;
                this.loading = true;
                try {
                    const r = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/signatures`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (r.ok) {
                        const j = await r.json();
                        this.signatures = j.data;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.feather) feather.replace();
                    });
                }
            },
            async sign() {
                this.submitting = true;
                try {
                    const res = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/signatures`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    if (res.ok) {
                        await this.fetch();
                        this.form.notes = '';
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
