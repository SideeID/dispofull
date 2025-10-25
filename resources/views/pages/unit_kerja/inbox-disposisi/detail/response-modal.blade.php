<template x-if="showResponse">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6"
            x-data="responseModal(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        <span x-show="!form.response">Berikan Response</span>
                        <span x-show="form.response">Update Response</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="disposition?.letter_number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 text-sm">
                <p class="font-medium text-gray-800 dark:text-gray-100 mb-2">Instruksi:</p>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line" x-text="disposition?.instruction"></p>
            </div>

            <form @submit.prevent="submit()" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                        Laporan / Response <span class="text-rose-500">*</span>
                    </label>
                    <textarea x-model="form.response" rows="6" required
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100"
                        placeholder="Jelaskan tindak lanjut yang telah dilakukan atau sedang dikerjakan..."></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Minimal 5 karakter. Jelaskan dengan detail progres atau hasil dari disposisi ini.
                    </p>
                </div>

                <p class="text-sm text-rose-500" x-text="error" x-show="error"></p>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span x-show="!disposition?.response">Status akan berubah menjadi "Sedang Dikerjakan"</span>
                        <span x-show="disposition?.response">Response akan diperbarui</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="closeAll()"
                            class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="submitting || form.response.length < 5"
                            class="px-5 py-2 rounded-lg text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2 disabled:opacity-50">
                            <span x-show="!submitting">Simpan Response</span>
                            <span x-show="submitting" class="flex items-center gap-2">
                                <span class="animate-spin h-4 w-4 rounded-full border-2 border-white border-t-transparent"></span>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Complete Action -->
            <div x-show="disposition?.response && disposition?.status !== 'completed'" 
                class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center gap-2">
                        <i data-feather="check-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                        Tandai Selesai
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                        Jika disposisi ini sudah selesai dikerjakan, klik tombol di bawah untuk menandai sebagai selesai.
                    </p>
                    <button type="button" @click="complete()" :disabled="submitting"
                        class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2 disabled:opacity-50">
                        <i data-feather="check" class="w-4 h-4"></i>
                        <span x-show="!submitting">Tandai Disposisi Selesai</span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    function responseModal(sel) {
        return {
            disposition: sel,
            form: {
                response: ''
            },
            error: '',
            submitting: false,
            init() {
                if (this.disposition?.response) {
                    this.form.response = this.disposition.response;
                }
                this.$nextTick(() => { if (window.feather) feather.replace(); });
            },
            async submit() {
                this.error = '';
                if (this.form.response.length < 5) {
                    this.error = 'Response minimal 5 karakter';
                    return;
                }
                this.submitting = true;
                try {
                    const res = await fetch(`/unit-kerja/api/dispositions/${this.disposition.id}/response`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    const json = await res.json();
                    if (res.ok) {
                        this.$dispatch('refresh-dispositions');
                        // Update parent modal if opened
                        if (this.disposition.id) {
                            this.disposition.response = json.data.response;
                            this.disposition.status = json.data.status;
                        }
                        // Show success notification
                        alert('Response berhasil disimpan!');
                        this.closeAll();
                    } else {
                        this.error = json.message || 'Gagal menyimpan response';
                    }
                } catch (e) {
                    console.error(e);
                    this.error = 'Terjadi kesalahan';
                } finally {
                    this.submitting = false;
                }
            },
            async complete() {
                if (!confirm('Tandai disposisi ini sebagai selesai?')) return;
                this.submitting = true;
                try {
                    const res = await fetch(`/unit-kerja/api/dispositions/${this.disposition.id}/complete`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    });
                    const json = await res.json();
                    if (res.ok) {
                        alert('Disposisi berhasil diselesaikan!');
                        this.$dispatch('refresh-dispositions');
                        this.closeAll();
                    } else {
                        this.error = json.message || 'Gagal menyelesaikan disposisi';
                    }
                } catch (e) {
                    console.error(e);
                    this.error = 'Terjadi kesalahan';
                } finally {
                    this.submitting = false;
                }
            },
            closeAll() {
                this.$dispatch('close-modals');
                window.location.reload(); // Refresh to get updated data
            }
        }
    }
</script>
