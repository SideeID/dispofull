<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="buatSurat()"
		 x-init="init()"
		 @keydown.escape.window="closeAll()"
	>
		@php
			// Placeholder data – ganti dengan query/model sebenarnya nanti
			$jenisSuratOptions = ['Surat Tugas','Surat Edaran','Surat Undangan','Memo Internal'];
			$priorities = [
				'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
			];
		@endphp

		<!-- Header -->
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/40">
						<i data-feather="edit-3" class="w-6 h-6"></i>
					</span>
					Buat Surat
				</h1>
				<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formulir pembuatan surat keluar (Unit Kerja)</p>
			</div>
			<div class="flex items-center gap-2">
				<button @click="saveDraft()" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 text-xs font-medium px-4 py-2 flex items-center gap-2">
					<i data-feather="save" class="w-4 h-4"></i> Simpan Draft
				</button>
				<button @click="open('showPreview')" class="btn bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium px-4 py-2 flex items-center gap-2">
					<i data-feather="eye" class="w-4 h-4"></i> Pratinjau
				</button>
				<button @click="open('showSubmitConfirm')" class="btn bg-orange-600 hover:bg-orange-500 text-white text-xs font-medium px-4 py-2 flex items-center gap-2">
					<i data-feather="send" class="w-4 h-4"></i> Ajukan TTD
				</button>
				<button @click="resetForm()" class="btn bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-3 py-2 text-xs font-medium">
					Reset
				</button>
			</div>
		</div>

		<form @submit.prevent="open('showSubmitConfirm')" class="space-y-8">
			<!-- Informasi Umum -->
			<div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
				<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="info" class="w-4 h-4 text-orange-500"></i> Informasi Umum</h2>
					<span class="text-[11px] text-gray-400" x-show="changed" x-transition>Belum disimpan</span>
				</div>
				<div class="p-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3 text-sm">
					<div class="flex flex-col gap-1.5 md:col-span-1">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Jenis Surat</label>
						<select x-model="form.jenis" @change="markChanged()" class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
							<option value="">Pilih Jenis</option>
							@foreach($jenisSuratOptions as $j)
								<option value="{{ $j }}">{{ $j }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-2">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Perihal</label>
						<input type="text" x-model="form.perihal" @input="markChanged()" placeholder="Perihal / Subject" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Tanggal Surat</label>
						<input type="date" x-model="form.tanggal" @change="markChanged()" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Prioritas</label>
						<select x-model="form.prioritas" @change="markChanged()" class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
							<option value="normal">Normal</option>
							<option value="urgent">Urgent</option>
							<option value="high">High</option>
							<option value="low">Low</option>
						</select>
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300 flex items-center gap-1">Tingkat Kerahasiaan <span class="text-[10px] text-gray-400 font-normal">(Opsional)</span></label>
						<select x-model="form.klasifikasi" @change="markChanged()" class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
							<option value="biasa">Biasa</option>
							<option value="internal">Internal</option>
							<option value="rahasia">Rahasia</option>
						</select>
					</div>
					<div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-3">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Ringkasan Singkat</label>
						<textarea rows="2" x-model="form.ringkasan" @input="markChanged()" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" placeholder="Ringkasan isi pokok surat..."></textarea>
					</div>
				</div>
			</div>

			<!-- Penomoran & Penandatangan -->
			<div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
				<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="hash" class="w-4 h-4 text-orange-500"></i> Nomor & Penandatangan</h2>
					<div class="flex items-center gap-2">
						<button type="button" @click="open('showNumbering')" class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='sliders' class='w-3.5 h-3.5'></i> Atur Penomoran</button>
						<button type="button" @click="open('showSigner')" class="text-[11px] px-3 py-1.5 rounded bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-1"><i data-feather='user-check' class='w-3.5 h-3.5'></i> Penandatangan</button>
					</div>
				</div>
				<div class="p-6 grid gap-6 md:grid-cols-2 text-sm">
					<div class="space-y-2">
						<div class="text-xs font-medium text-gray-600 dark:text-gray-300">Nomor Surat (Preview)</div>
						<div class="px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 border border-dashed border-gray-300 dark:border-gray-600 font-mono text-xs text-orange-600 dark:text-amber-400" x-text="computedNumber()"></div>
						<p class="text-[10px] text-gray-400">Nomor final akan dikunci saat pengajuan tanda tangan.</p>
					</div>
					<div class="space-y-2">
						<div class="text-xs font-medium text-gray-600 dark:text-gray-300">Penandatangan</div>
						<template x-if="signer">
							<div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-between">
								<div>
									<div class="text-xs font-semibold text-gray-800 dark:text-gray-100" x-text="signer.nama"></div>
									<div class="text-[11px] text-gray-500 dark:text-gray-400" x-text="signer.jabatan"></div>
								</div>
								<button type="button" @click="open('showSigner')" class="text-[11px] text-orange-600 dark:text-amber-400 hover:underline">Ubah</button>
							</div>
						</template>
						<template x-if="!signer">
							<button type="button" @click="open('showSigner')" class="w-full p-3 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-[11px] text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 justify-center"><i data-feather='plus-circle' class='w-4 h-4'></i>Pilih Penandatangan</button>
						</template>
					</div>
				</div>
			</div>

			<!-- Penerima & Peserta -->
			<div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
				<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="users" class="w-4 h-4 text-orange-500"></i> Penerima & Peserta</h2>
					<div class="flex items-center gap-2">
						<button type="button" @click="open('showParticipants')" class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='user-plus' class='w-3.5 h-3.5'></i> Kelola Peserta</button>
					</div>
				</div>
				<div class="p-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3 text-sm">
					<div class="flex flex-col gap-1.5 md:col-span-1">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Unit Internal (Tujuan)</label>
						<div class="space-y-2">
							<template x-for="u in form.tujuanInternal" :key="u">
								<div class="px-3 py-1.5 rounded bg-gray-50 dark:bg-gray-700 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
									<span x-text="u"></span>
									<button type="button" @click="removeInternal(u)" class="text-rose-500 hover:text-rose-400"><i data-feather='x' class='w-3 h-3'></i></button>
								</div>
							</template>
							<div class="flex gap-2">
								<input type="text" x-model="internalTemp" @keydown.enter.prevent="addInternal()" placeholder="Tambah unit" class="flex-1 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
								<button type="button" @click="addInternal()" class="px-3 py-1.5 rounded bg-orange-600 hover:bg-orange-500 text-white text-[11px]">Tambah</button>
							</div>
						</div>
					</div>
					<div class="flex flex-col gap-1.5 md:col-span-1">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Alamat Eksternal</label>
						<div class="space-y-2">
							<template x-for="e in form.tujuanExternal" :key="e">
								<div class="px-3 py-1.5 rounded bg-gray-50 dark:bg-gray-700 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
									<span x-text="e"></span>
									<button type="button" @click="removeExternal(e)" class="text-rose-500 hover:text-rose-400"><i data-feather='x' class='w-3 h-3'></i></button>
								</div>
							</template>
							<div class="flex gap-2">
								<input type="text" x-model="externalTemp" @keydown.enter.prevent="addExternal()" placeholder="Tambah alamat" class="flex-1 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
								<button type="button" @click="addExternal()" class="px-3 py-1.5 rounded bg-orange-600 hover:bg-orange-500 text-white text-[11px]">Tambah</button>
							</div>
						</div>
					</div>
					<div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-1">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300 flex items-center gap-1">Statistik Peserta <span class="text-[10px] text-gray-400 font-normal">(otomatis)</span></label>
						<div class="grid grid-cols-3 gap-3">
							<div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 text-center">
								<div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Peserta</div>
								<div class="text-lg font-semibold text-orange-600 dark:text-amber-400" x-text="participants.length"></div>
							</div>
							<div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 text-center">
								<div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Lampiran</div>
								<div class="text-lg font-semibold text-amber-600 dark:text-amber-400" x-text="attachments.length"></div>
							</div>
							<div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 text-center">
								<div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Revisi</div>
								<div class="text-lg font-semibold text-amber-600 dark:text-amber-400" x-text="history.length"></div>
							</div>
						</div>
						<div class="mt-3 flex gap-2">
							<button type="button" @click="open('showAttachments')" class="flex-1 text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1 justify-center"><i data-feather='paperclip' class='w-3.5 h-3.5 text-orange-500'></i> Lampiran</button>
							<button type="button" @click="open('showHistory')" class="flex-1 text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1 justify-center"><i data-feather='clock' class='w-3.5 h-3.5 text-orange-500'></i> Revisi</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Konten Surat -->
			<div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
				<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="file-text" class="w-4 h-4 text-orange-500"></i> Konten Surat</h2>
					<div class="flex items-center gap-2">
						<button type="button" @click="open('showTemplates')" class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='layers' class='w-3.5 h-3.5 text-orange-500'></i> Template</button>
					</div>
				</div>
				<div class="p-6 space-y-4 text-sm">
					<div class="bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600 rounded-lg">
						<div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600 flex items-center gap-3 text-[11px] text-gray-500 dark:text-gray-400">
							<button type="button" class="hover:text-orange-500"><i data-feather='bold' class='w-3.5 h-3.5'></i></button>
							<button type="button" class="hover:text-orange-500"><i data-feather='italic' class='w-3.5 h-3.5'></i></button>
							<button type="button" class="hover:text-orange-500"><i data-feather='underline' class='w-3.5 h-3.5'></i></button>
							<span class="w-px h-4 bg-gray-300 dark:bg-gray-600"></span>
							<button type="button" class="hover:text-orange-500"><i data-feather='list' class='w-3.5 h-3.5'></i></button>
							<button type="button" class="hover:text-orange-500"><i data-feather='link' class='w-3.5 h-3.5'></i></button>
							<button type="button" class="hover:text-orange-500"><i data-feather='minus' class='w-3.5 h-3.5 rotate-90'></i></button>
							<span class="ml-auto text-[10px] text-gray-400" x-text="contentLength() + ' karakter'"></span>
						</div>
						<textarea rows="10" x-model="form.konten" @input="updateContent()" class="w-full bg-transparent px-4 py-3 focus:outline-none text-sm text-gray-700 dark:text-gray-100 resize-y" placeholder="Tulis isi surat di sini, atau gunakan template..."></textarea>
					</div>
					<div>
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Catatan Internal (Tidak muncul di surat)</label>
						<textarea rows="3" x-model="form.catatanInternal" @input="markChanged()" class="mt-1 w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-sm" placeholder="Catatan internal untuk unit kerja..."></textarea>
					</div>
				</div>
			</div>

			<!-- Aksi Bawah -->
			<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-2">
				<div class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-3">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
				</div>
				<div class="flex items-center gap-2">
					<button type="button" @click="saveDraft()" class="px-4 py-2 rounded-lg text-sm bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2"><i data-feather='save' class='w-4 h-4'></i>Simpan Draft</button>
					<button type="button" @click="open('showPreview')" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-2"><i data-feather='eye' class='w-4 h-4'></i>Pratinjau</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-sm bg-orange-600 hover:bg-orange-500 text-white flex items-center gap-2"><i data-feather='send' class='w-4 h-4'></i>Ajukan TTD</button>
				</div>
			</div>
		</form>

		@include('pages.unit_kerja.buat-surat.detail.preview-modal')
		@include('pages.unit_kerja.buat-surat.detail.participants-modal')
		@include('pages.unit_kerja.buat-surat.detail.attachment-modal')
		@include('pages.unit_kerja.buat-surat.detail.templates-modal')
		@include('pages.unit_kerja.buat-surat.detail.signer-modal')
		@include('pages.unit_kerja.buat-surat.detail.numbering-modal')
		@include('pages.unit_kerja.buat-surat.detail.history-modal')
		@include('pages.unit_kerja.buat-surat.detail.submit-confirm-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>

	<script>
		function buatSurat(){
			return {
				showPreview:false, showParticipants:false, showAttachments:false, showTemplates:false, showSigner:false, showNumbering:false, showHistory:false, showSubmitConfirm:false,
				changed:false,
				signer:null,
				internalTemp:'', externalTemp:'',
				form:{
					jenis:'', perihal:'', tanggal:'', prioritas:'normal', klasifikasi:'biasa', ringkasan:'',
					konten:'', catatanInternal:'', nomor:{prefix:'ST', seq:1, tahun:(new Date()).getFullYear(), unit:'UK'},
					tujuanInternal:['Bagian Keuangan'], tujuanExternal:[],
				},
				participants:[
					{nama:'Andi Saputra', nip:'1987654321', jabatan:'Staf', status:'aktif'},
					{nama:'Siti Rahma', nip:'1991122233', jabatan:'Koordinator', status:'aktif'},
				],
				attachments:[
					{nama:'draf-rab.pdf', size:'120KB'},
				],
				templates:[
					{id:1, nama:'Template Surat Tugas Umum', kategori:'Surat Tugas', isi:'<p>Menugaskan kepada ...</p>'},
					{id:2, nama:'Template Rapat Koordinasi', kategori:'Undangan', isi:'<p>Sehubungan dengan ...</p>'},
				],
				history:[
					{time:'2025-10-05 09:10', note:'Draft awal dibuat'},
				],
				signers:[
					{id:1, nama:'Dr. Budi Santosa', jabatan:'Kepala Unit', nip:'19650101 199003 1 001'},
					{id:2, nama:'Ir. Lina Wati', jabatan:'Sekretaris Unit', nip:'19770202 200501 2 002'},
				],
				open(modal){ this[modal]=true },
				closeAll(){ this.showPreview=false; this.showParticipants=false; this.showAttachments=false; this.showTemplates=false; this.showSigner=false; this.showNumbering=false; this.showHistory=false; this.showSubmitConfirm=false; },
				markChanged(){ this.changed=true; },
				resetForm(){ if(confirm('Reset semua isian?')){ this.changed=false; this.form.jenis=''; this.form.perihal=''; this.form.tanggal=''; this.form.prioritas='normal'; this.form.klasifikasi='biasa'; this.form.ringkasan=''; this.form.konten=''; this.form.catatanInternal=''; this.form.tujuanInternal=['Bagian Keuangan']; this.form.tujuanExternal=[]; this.participants=[{nama:'Andi Saputra', nip:'1987654321', jabatan:'Staf', status:'aktif'},{nama:'Siti Rahma', nip:'1991122233', jabatan:'Koordinator', status:'aktif'}]; this.attachments=[{nama:'draf-rab.pdf', size:'120KB'}]; this.history=[{time: new Date().toISOString().slice(0,16).replace('T',' '), note:'Reset draft'}]; this.signer=null; } },
				addInternal(){ if(this.internalTemp.trim()!==''){ this.form.tujuanInternal.push(this.internalTemp.trim()); this.internalTemp=''; this.markChanged(); } },
				removeInternal(u){ this.form.tujuanInternal = this.form.tujuanInternal.filter(x=>x!==u); this.markChanged(); },
				addExternal(){ if(this.externalTemp.trim()!==''){ this.form.tujuanExternal.push(this.externalTemp.trim()); this.externalTemp=''; this.markChanged(); } },
				removeExternal(e){ this.form.tujuanExternal = this.form.tujuanExternal.filter(x=>x!==e); this.markChanged(); },
				selectTemplate(t){ if(confirm('Gunakan template ini? Konten sekarang akan ditimpa.')){ this.form.konten = t.isi.replace(/<[^>]+>/g,''); this.markChanged(); this.history.unshift({time: new Date().toISOString().slice(0,16).replace('T',' '), note:'Menggunakan template '+t.nama}); this.closeAll(); } },
				setSigner(s){ this.signer = s; this.closeAll(); },
				computedNumber(){ const p = this.form.nomor; return `${p.prefix}/${String(p.seq).padStart(3,'0')}/${p.unit}/${p.tahun}`; },
				incrementSeq(){ this.form.nomor.seq++; this.markChanged(); },
				decrementSeq(){ if(this.form.nomor.seq>1){ this.form.nomor.seq--; this.markChanged(); } },
				updateContent(){ this.markChanged(); },
				contentLength(){ return (this.form.konten||'').length; },
				saveDraft(){ this.history.unshift({time:new Date().toISOString().slice(0,16).replace('T',' '), note:'Simpan draft'}); this.changed=false; alert('Draft disimpan (dummy)'); },
				submit(){ this.history.unshift({time:new Date().toISOString().slice(0,16).replace('T',' '), note:'Diajukan untuk tanda tangan'}); this.changed=false; this.closeAll(); alert('Pengajuan tanda tangan (dummy)'); },
				init(){ /* placeholder future init */ },
			}
		}
	</script>
</x-app-layout>
