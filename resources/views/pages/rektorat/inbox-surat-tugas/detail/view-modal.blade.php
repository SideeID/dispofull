<template x-if="showView">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Surat Tugas (Inbox)</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="grid md:grid-cols-2 gap-6 text-sm">
				<div class="space-y-3">
					<div>
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Perihal</div>
						<div class="text-gray-700 dark:text-gray-200 font-medium" x-text="selected?.subject"></div>
					</div>
					<div class="grid grid-cols-2 gap-4">
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Asal</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.origin"></div>
						</div>
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Tanggal Surat</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.date"></div>
						</div>
					</div>
					<div class="grid grid-cols-2 gap-4">
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Periode</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.start + ' s/d ' + selected?.end"></div>
						</div>
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Status</div>
							<div><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="{
								'bg-amber-500/10 text-amber-600 dark:text-amber-400': selected?.status=='pending_ack',
								'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400': selected?.status=='acknowledged',
								'bg-rose-500/10 text-rose-600 dark:text-rose-400': selected?.status=='need_signature',
								'bg-blue-500/10 text-blue-600 dark:text-blue-400': selected?.status=='signed',
								'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': selected?.status=='published',
							}" x-text="selected?.status"></span></div>
						</div>
					</div>
					<div>
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Prioritas</div>
						<div><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="{
							'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': selected?.priority=='low',
							'bg-slate-500/10 text-slate-600 dark:text-slate-300': selected?.priority=='normal',
							'bg-amber-500/10 text-amber-600 dark:text-amber-400': selected?.priority=='high',
							'bg-rose-500/10 text-rose-600 dark:text-rose-400': selected?.priority=='urgent'
						}" x-text="selected?.priority"></span></div>
					</div>
					<div>
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Ringkasan</div>
						<p class="text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">Ringkasan singkat surat tugas (placeholder) - tarik dari catatan / content surat.</p>
					</div>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>Nama A · Ketua Tim</li>
							<li>Nama B · Anggota</li>
							<li>Nama C · Anggota</li>
							<li><button @click="closeAll(); open('showParticipants', selected)" class="text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua &raquo;</button></li>
						</ul>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Aktivitas Terakhir</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>2025-10-02 10:20 · Perlu konfirmasi</li>
							<li>2025-10-02 09:55 · Draft masuk inbox</li>
						</ul>
					</div>
					<div class="flex gap-2" x-show="['pending_ack','need_signature'].includes(selected?.status)">
						<button @click="closeAll(); open('showAcknowledge', selected)" x-show="selected?.status=='pending_ack'" class="flex-1 px-4 py-2 rounded-lg text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium">Konfirmasi</button>
						<button @click="closeAll(); open('showSign', selected)" x-show="selected?.status=='need_signature'" class="flex-1 px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Tanda Tangan</button>
						<button @click="closeAll(); open('showAttachments', selected)" x-show="selected?.files>0" class="flex-1 px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Lampiran</button>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-between pt-2">
				<button type="button" @click="closeAll(); open('showPreview', selected)" class="px-4 py-2 rounded-lg text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2">
					<i data-feather="eye" class="w-4 h-4"></i> Preview Surat
				</button>
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
