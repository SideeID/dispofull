<template x-if="showPreview">
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showPreview = false; selected = null;"></div>
        <div class="relative w-full max-w-5xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Preview Surat Tugas</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || 'Draft'"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="window.print()" 
                        class="px-4 py-2 rounded-lg text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2">
                        <i data-feather="printer" class="w-3.5 h-3.5"></i>
                        Cetak
                    </button>
                    <button @click="showPreview = false; selected = null;" class="text-gray-400 hover:text-rose-600">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto px-8 py-8 text-sm leading-relaxed bg-gray-50 dark:bg-gray-800/40 flex-1">
                <div id="printableArea" class="max-w-3xl mx-auto space-y-8">
                    <!-- Include shared styles -->
                    @include('templates.components.styles')
                    
                    <!-- Header Surat -->
                    @include('templates.components.header')

                    <!-- Title -->
                    <div class="text-center space-y-1">
                        <div class="letter-title" x-text="selected?.perihal || selected?.subject || 'Surat Tugas'"></div>
                        <div class="letter-number">
                            <span>Nomor :</span>
                            <span x-text="selected?.number || 'DRAFT'"></span>
                        </div>
                    </div>

                    <!-- Konten surat (HTML dari editor) -->
                    <div class="content prose prose-sm dark:prose-invert max-w-none" x-html="selected?.konten || '<p><em>Konten surat belum diisi.</em></p>'"></div>

                    <!-- Tanda tangan -->
                    <div class="signature">
                        <p>Jakarta, <span x-text="selected?.tanggal || selected?.date || '—'"></span></p>
                        <template x-if="selected?.signature">
                            <div class="mt-4">
                                <template x-if="selected.signature.signature_path">
                                    <img :src="`/storage/${selected.signature.signature_path}`" class="h-20 mx-auto mb-2" alt="Signature">
                                </template>
                                <template x-if="!selected.signature.signature_path && selected.signature.signature_data">
                                    <img :src="selected.signature.signature_data" class="h-20 mx-auto mb-2" alt="Signature">
                                </template>
                            </div>
                        </template>
                        <template x-if="!selected?.signature">
                            <br><br><br>
                        </template>
                        <p x-text="selected?.signature?.signer_name || selected?.signer?.nama || '[Nama Penandatangan]'"></p>
                        <p x-text="selected?.signature?.signer_title || selected?.signer?.jabatan || '[Jabatan Penandatangan]'"></p>
                    </div>

                    <!-- Metadata Surat -->
                    <div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700">
                        <h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-3">Informasi Surat</h4>
                        <div class="grid gap-3 text-xs">
                            <div class="flex items-start gap-3">
                                <span class="w-32 text-gray-500 dark:text-gray-400">Prioritas:</span>
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                    :class="{
                                        'bg-emerald-500/20 text-emerald-700': selected?.priority=='low',
                                        'bg-slate-500/20 text-slate-700': selected?.priority=='normal',
                                        'bg-amber-500/20 text-amber-700': selected?.priority=='high',
                                        'bg-rose-500/20 text-rose-700': selected?.priority=='urgent'
                                    }"
                                    x-text="selected?.priority || 'normal'"></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-32 text-gray-500 dark:text-gray-400">Status:</span>
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                    :class="{
                                        'bg-slate-500/20 text-slate-700': selected?.status=='draft',
                                        'bg-amber-500/20 text-amber-700': selected?.status=='pending',
                                        'bg-emerald-500/20 text-emerald-700': selected?.status=='processed',
                                        'bg-rose-500/20 text-rose-700': selected?.status=='rejected',
                                        'bg-gray-500/20 text-gray-700': selected?.status=='closed',
                                        'bg-slate-500/20 text-slate-700': selected?.status=='archived'
                                    }"
                                    x-text="selected?.status || 'draft'"></span>
                            </div>
                            <div class="flex items-start gap-3" x-show="selected?.destination">
                                <span class="w-32 text-gray-500 dark:text-gray-400">Tujuan/Tim:</span>
                                <span class="text-gray-700 dark:text-gray-200" x-text="selected?.destination"></span>
                            </div>
                            <div class="flex items-start gap-3" x-show="selected?.notes?.ringkasan">
                                <span class="w-32 text-gray-500 dark:text-gray-400">Ringkasan:</span>
                                <span class="text-gray-700 dark:text-gray-200" x-text="selected?.notes?.ringkasan"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Penerima Internal/External -->
                    <div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700" x-show="(selected?.tujuanInternal && selected.tujuanInternal.length) || (selected?.tujuanExternal && selected.tujuanExternal.length)">
                        <h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">Penerima</h4>
                        <ul class="grid md:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                            <template x-for="u in selected?.tujuanInternal" :key="'i'+u">
                                <li class="px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" x-text="u"></li>
                            </template>
                            <template x-for="e in selected?.tujuanExternal" :key="'e'+e">
                                <li class="px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" x-text="e"></li>
                            </template>
                        </ul>
                    </div>

                    <!-- Lampiran & Peserta -->
                    <div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700 flex items-start gap-8">
                        <div class="flex-1">
                            <h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">
                                Lampiran (<span x-text="Array.isArray(selected?.attachments) ? selected.attachments.length : (selected?.attachments || 0)"></span>)
                            </h4>
                            <template x-if="Array.isArray(selected?.attachments) && selected.attachments.length">
                                <ul class="space-y-1 text-xs">
                                    <template x-for="a in selected.attachments" :key="a.id || a.filename">
                                        <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                            <i data-feather='paperclip' class='w-3.5 h-3.5 text-violet-500'></i>
                                            <span x-text="a.filename || a.nama"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">
                                Peserta (<span x-text="Array.isArray(selected?.participants) ? selected.participants.length : (selected?.participants || 0)"></span>)
                            </h4>
                            <template x-if="Array.isArray(selected?.participants) && selected.participants.length">
                                <ul class="space-y-1 text-xs">
                                    <template x-for="p in selected.participants" :key="p.id || p.nama">
                                        <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                            <i data-feather='user' class='w-3.5 h-3.5 text-indigo-500'></i>
                                            <span x-text="p.nama + (p.jabatan ? ' (' + p.jabatan + ')' : '')"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </div>

                    <!-- Footer -->
                    @include('templates.components.footer')
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="window.print()" 
                    class="px-4 py-2 rounded-lg text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i>
                    Cetak
                </button>
                <button @click="showPreview = false; selected = null;" 
                    class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 font-medium">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
