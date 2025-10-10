<template x-if="showDisposition">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5"
            x-data="dispositionModal(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Disposisi Surat</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="grid md:grid-cols-2 gap-6 text-sm">
                <div class="space-y-4">
                    <form class="space-y-4" @submit.prevent="submit()">
                        <div>
                            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kepada
                                (User)</label>
                            <select x-model="form.to_user_id"
                                class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
                                <option value="">-- pilih --</option>
                                <template x-for="u in recipients" :key="u.id">
                                    <option :value="u.id"
                                        x-text="u.name + (u.position ? ' · '+u.position : '')"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Instruksi</label>
                            <textarea x-model="form.instruction" rows="3"
                                class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100"
                                placeholder="Tindak lanjuti ..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
                                <select x-model="form.priority"
                                    class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Batas
                                    Waktu</label>
                                <input type="date" x-model="form.due_date"
                                    class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
                            </div>
                        </div>
                        <p class="text-[11px] text-rose-500" x-text="error" x-show="error"></p>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="closeAll()"
                                class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
                            <button type="submit" :disabled="submitting"
                                class="px-4 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
                                <span x-show="!submitting">Simpan Disposisi</span>
                                <span x-show="submitting" class="flex items-center gap-1"><span
                                        class="animate-spin h-4 w-4 rounded-full border-2 border-white border-t-transparent"></span>
                                    Proses</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="space-y-5">
                    <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
                        <div
                            class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2 flex items-center justify-between">
                            Riwayat Disposisi <span class="text-[10px] font-normal"
                                x-show="dispLoading">Memuat...</span></div>
                        <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300"
                            x-show="!dispLoading && dispositions.length">
                            <template x-for="d in dispositions" :key="d.id">
                                <li>
                                    <span class="font-semibold" x-text="d.created_at"></span>
                                    · Ke <span x-text="d.to_user?.name || '-' "></span>
                                    · <span x-text="d.instruction"></span>
                                </li>
                            </template>
                        </ul>
                        <div class="text-[11px] text-gray-400" x-show="!dispLoading && !dispositions.length">Belum ada
                            disposisi.</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
                        <div
                            class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">
                            Status Pengerjaan</div>
                        <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                            <template x-for="d in dispositions" :key="'st-' + d.id">
                                <li>
                                    <span x-text="(d.to_user?.name || 'User')"></span>
                                    · <span
                                        :class="{
                                            'text-amber-600 dark:text-amber-400': d.status==='pending',
                                            'text-blue-600 dark:text-blue-400': d.status==='in_progress',
                                            'text-emerald-600 dark:text-emerald-400': d.status==='completed',
                                            'text-rose-600 dark:text-rose-400': d.status==='returned'
                                        }"
                                        x-text="statusLabel(d.status)"></span>
                                </li>
                            </template>
                            <li x-show="!dispositions.length" class="text-gray-400">Belum ada progres.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end pt-2">
                <button type="button" @click="closeAll()"
                    class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
            </div>
        </div>
    </div>
</template>

<script>
    function dispositionModal(sel) {
        return {
            selected: sel,
            recipients: [],
            dispositions: [],
            dispLoading: false,
            submitting: false,
            error: '',
            form: {
                to_user_id: '',
                instruction: '',
                priority: 'normal',
                due_date: ''
            },
            init() {
                this.fetchRecipients();
                this.fetchDispositions();
            },
            async fetchRecipients() {
                try {
                    const r = await fetch('/rektor/api/incoming-letters/recipients/dispositions', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (r.ok) {
                        const j = await r.json();
                        this.recipients = j.data;
                    }
                } catch (e) {
                    console.error(e);
                }
            },
            async fetchDispositions() {
                if (!this.selected) return;
                this.dispLoading = true;
                try {
                    const r = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/dispositions`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (r.ok) {
                        this.dispositions = await r.json();
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.dispLoading = false;
                    this.$nextTick(() => {
                        if (window.feather) feather.replace();
                    });
                }
            },
            statusLabel(s) {
                return {
                    pending: 'Menunggu',
                    in_progress: 'Sedang Diproses',
                    completed: 'Selesai',
                    returned: 'Dikembalikan'
                } [s] || s;
            },
            async submit() {
                this.error = '';
                if (!this.form.to_user_id || !this.form.instruction) {
                    this.error = 'Kepada & instruksi wajib.';
                    return;
                }
                this.submitting = true;
                try {
                    const res = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/dispositions`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    if (res.ok) {
                        this.form = {
                            to_user_id: '',
                            instruction: '',
                            priority: 'normal',
                            due_date: ''
                        };
                        await this.fetchDispositions();
                    } else {
                        const j = await res.json();
                        this.error = j.message || 'Gagal menyimpan';
                    }
                } catch (e) {
                    console.error(e);
                    this.error = 'Terjadi kesalahan';
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
