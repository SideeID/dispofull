<template x-if="showView">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Arsip Surat Tugas</h3>
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
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Tujuan / Tim</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.destination"></div>
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
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Durasi</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="(() => { if(!selected) return '-'; const sd = new Date(selected.start); const ed = new Date(selected.end); return Math.floor((ed - sd)/(1000*60*60*24))+1 + ' hari'; })()"></div>
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
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Alasan Arsip</div>
						<p class="text-gray-600 dark:text-gray-300 mt-1 leading-relaxed" x-text="selected?.reason"></p>
					</div>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>Dr. Andi · Ketua Tim</li>
							<li>Ir. Budi · Anggota</li>
							<li>Dr. Citra · Anggota</li>
							<li><button @click="closeAll(); open('showParticipants', selected)" class="text-slate-600 dark:text-slate-300 hover:underline">Lihat semua &raquo;</button></li>
						</ul>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Aktivitas Akhir</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>2025-10-02 10:30 · Diarsipkan</li>
							<li>2025-09-25 09:05 · Laporan diterima</li>
							<li>2025-09-19 16:10 · Kegiatan selesai</li>
						</ul>
					</div>
					<div class="flex gap-2">
						<button @click="closeAll(); open('showHistory', selected)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-slate-700 hover:bg-slate-600 text-white font-medium">Riwayat Lengkap</button>
						<button @click="closeAll(); open('showRestore', selected)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Pulihkan</button>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-slate-700 hover:bg-slate-600 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
