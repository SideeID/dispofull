<template x-if="openFlags.showTemplates">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Template Surat</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih template untuk mengisi konten surat.</p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
            </div>
            
            <!-- Content (with fixed max-height) -->
            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 text-sm min-h-0" x-data="{ search:'', kategori:'' }">
                <div class="flex flex-col sm:flex-row gap-3 text-xs">
                    <div class="relative flex-1">
                        <i data-feather='search' class='w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                        <input x-model="search" type="text" placeholder="Cari template" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
                    </div>
                    <select x-model="kategori" class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500">
                        <option value="">Semua Kategori</option>
                        <option value="Umum">Umum</option>
                        <option value="Internal">Internal</option>
                        <option value="Kerjasama">Kerjasama</option>
                        <option value="Personalia">Personalia</option>
                    </select>
                </div>
                <div class="grid md:grid-cols-2 gap-4" x-data="{ 
                    templates: [
                        {
                            id: 'surat-tugas',
                            nama: 'Surat Tugas',
                            kategori: 'Umum',
                            isi: 'Wakil Rektor I Universitas Bakrie dengan ini menugaskan kepada nama-nama di bawah ini:\n\n[Isi daftar nama personil yang ditugaskan]\n\nKegiatan tersebut tidak mengganggu tugas pokok dan kegiatan lainnya yang ditetapkan oleh Universitas Bakrie.\n\nDemikian Surat Tugas ini diberikan untuk dilaksanakan sebaik-baiknya.'
                        },
                        {
                            id: 'surat-edaran',
                            nama: 'Surat Edaran',
                            kategori: 'Umum',
                            isi: 'Dengan ini diberitahukan kepada seluruh Unit Kerja di Universitas Bakrie, bahwa:\n\n1. [Isi poin pertama]\n2. [Isi poin kedua]\n\nDemikian Edaran ini disampaikan untuk dapat dipahami dan dilaksanakan dengan sebaik-baiknya.'
                        },
                        {
                            id: 'internal-memo',
                            nama: 'Internal Memo',
                            kategori: 'Internal',
                            isi: 'Dear Bapak/Ibu,\n\n[Isi memo internal]\n\nDemikian memo ini kami sampaikan. Atas perhatiannya kami ucapkan terima kasih.\n\nSalam,'
                        },
                        {
                            id: 'perjanjian-kerjasama',
                            nama: 'Perjanjian Kerjasama (PKS)',
                            kategori: 'Kerjasama',
                            isi: 'Dengan hormat,\n\nKami dari pihak Bakrie selaku calon mitra kerja [Nama Perusahaan/Instansi] dengan ini mengajukan permohonan untuk melakukan penandatanganan Perjanjian Kerja Sama (PKS) dalam menjalankan kegiatan:\n\n[Jelaskan detail kegiatan kerjasama]\n\nDemikian permohonan kerjasama ini kami sampaikan. Atas perhatian Bapak, kami ucapkan terima kasih.\n\nHormat kami,'
                        },
                        {
                            id: 'surat-izin-atasan',
                            nama: 'Surat Izin Atasan',
                            kategori: 'Personalia',
                            isi: 'Saya yang bertanda tangan di bawah ini memberikan izin kepada:\n\nNama: [Nama]\nNIP: [NIP]\nJabatan: [Jabatan]\n\nUntuk mengikuti Program: [Tujuan Izin]\n\nDemikian surat izin ini dibuat untuk dapat digunakan sebagaimana mestinya.'
                        },
                        {
                            id: 'surat-keterangan',
                            nama: 'Surat Keterangan',
                            kategori: 'Umum',
                            isi: 'Rektor Universitas Bakrie menyatakan dengan ini:\n\nNama: [Nama]\nTempat/Tanggal Lahir: [Tempat, Tanggal]\nAlamat: [Alamat Lengkap]\n\nAdalah benar [keterangan yang diberikan, misal: staf/mahasiswa/dosen di Universitas Bakrie].\n\nDemikian Surat Keterangan ini dibuat untuk dapat digunakan sebagaimana mestinya.'
                        }
                    ],
                    filtered(){ 
                        return this.templates.filter(t=> 
                            (!search || t.nama.toLowerCase().includes(search.toLowerCase())) && 
                            (!kategori || t.kategori===kategori)
                        ); 
                    } 
                }">
                    <template x-for="t in filtered()" :key="t.id">
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/40 flex flex-col gap-3">
                            <div>
                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200" x-text="t.nama"></div>
                                <div class="text-[10px] text-gray-400" x-text="t.kategori"></div>
                            </div>
                            <div class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-3" x-text="t.isi.replace(/<[^>]+>/g,'').slice(0,120)+'...' "></div>
                            <div class="flex items-center justify-end">
                                <button type="button" @click="selectTemplate(t)" class="px-3 py-1.5 rounded bg-violet-600 hover:bg-violet-500 text-white text-[11px] flex items-center gap-1"><i data-feather='download' class='w-3.5 h-3.5'></i> Pakai</button>
                            </div>
                        </div>
                    </template>
                    <div class="text-[11px] text-gray-400" x-show="filtered().length===0">Tidak ada template.</div>
                </div>
            </div>
            
            <!-- Footer (with absolute positioning fallback) -->
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-3 bg-white dark:bg-gray-800 rounded-b-2xl w-full z-10">
                <div class="text-[11px] text-gray-500 dark:text-gray-400">Pilih template akan menimpa konten saat ini.</div>
                <button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
            </div>
        </div>
    </div>
</template>
