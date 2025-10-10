<template x-if="showHistory">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5"
            x-data="historyModal(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Riwayat Surat</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="max-h-[55vh] overflow-y-auto pr-1 text-sm">
                <div class="flex items-center justify-center py-6" x-show="loading">
                    <div class="animate-spin h-6 w-6 rounded-full border-2 border-amber-500 border-t-transparent"></div>
                </div>
                <ul class="space-y-4" x-show="!loading && logs.length">
                    <template x-for="l in logs" :key="l.time + l.action">
                        <li class="flex items-start gap-3">
                            <div class="w-10 text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5"
                                x-text="timePart(l.time)"></div>
                            <div class="flex-1">
                                <div class="text-[11px] font-semibold" :class="statusClass(l.status)" x-text="l.actor">
                                </div>
                                <div class="text-gray-600 dark:text-gray-300 leading-snug" x-text="l.action"></div>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="l.time"></div>
                            </div>
                        </li>
                    </template>
                </ul>
                <div class="text-center py-6 text-[11px] text-gray-400" x-show="!loading && !logs.length">Belum ada
                    histori.</div>
            </div>
            <div class="flex items-center justify-end pt-2">
                <button type="button" @click="closeAll()"
                    class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
            </div>
        </div>
    </div>
</template>

<script>
    function historyModal(sel) {
        return {
            selected: sel,
            logs: [],
            loading: false,
            init() {
                this.fetch();
            },
            async fetch() {
                if (!this.selected) return;
                this.loading = true;
                try {
                    const r = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/history`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (r.ok) {
                        const j = await r.json();
                        this.logs = j.data;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },
            timePart(t) {
                return (t || '').split(' ')[1] || t;
            },
            statusClass(s) {
                return {
                    success: 'text-emerald-600 dark:text-emerald-400',
                    warning: 'text-amber-600 dark:text-amber-400',
                    error: 'text-rose-600 dark:text-rose-400',
                    info: 'text-slate-500 dark:text-slate-400'
                } [s] || 'text-slate-500 dark:text-slate-400';
            }
        }
    }
</script>
