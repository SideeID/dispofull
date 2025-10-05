<template x-if="showView">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || selected?.temp"></p>
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
							<div class="text-gray-700 dark:text-gray-200" x-text="(selected?.start||'') + ' s/d ' + (selected?.end||'')"></div>
						</div>
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Status</div>
							<div><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="{
								'bg-slate-500/10 text-slate-600 dark:text-slate-300': selected?.status=='draft',
								'bg-rose-500/10 text-rose-600 dark:text-rose-400': selected?.status=='need_signature',
								'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400': selected?.status=='signed',
								'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': selected?.status=='published',
								'bg-slate-500/10 text-slate-600 dark:text-slate-400': selected?.status=='archived',
							}" x-text="selected?.status || 'draft'"></span></div>
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
						<p class="text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">Ringkasan/tujuan singkat surat tugas (placeholder) - tarik dari content / notes surat.</p>
					</div>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>Dr. Andi · Ketua Tim</li>
							<li>Ir. Budi · Anggota</li>
							<li><button @click="closeAll(); open('showParticipants', selected)" class="text-emerald-600 dark:text-emerald-400 hover:underline">Kelola &raquo;</button></li>
						</ul>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Aktivitas Terakhir</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>2025-10-02 09:40 · Peserta ditambahkan</li>
							<li>2025-10-02 09:12 · Draft dibuat</li>
						</ul>
					</div>
					<div class="flex gap-2" x-show="['draft','need_signature','signed'].includes(selected?.status || 'draft')">
						<button @click="closeAll(); open('showEdit', selected)" x-show="selected?.status=='draft'" class="flex-1 px-4 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium">Edit</button>
						<button @click="closeAll(); open('showSign', selected)" x-show="['draft','need_signature'].includes(selected?.status)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Tanda Tangan</button>
						<button @click="closeAll(); open('showPublish', selected)" x-show="selected?.status=='signed'" class="flex-1 px-4 py-2 rounded-lg text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium">Publish</button>
						<button @click="closeAll(); open('showAttachments', selected)" x-show="selected?.files>0" class="flex-1 px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Lampiran</button>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
