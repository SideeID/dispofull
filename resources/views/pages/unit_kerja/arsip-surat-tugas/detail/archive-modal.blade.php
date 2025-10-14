<div x-data="archiveModal()" x-show="isOpen" x-cloak class="fixed inset-0 z-40 flex items-end sm:items-center justify-center" @open-archive-modal.window="open()">
	<div class="absolute inset-0 bg-black/40" @click="close()"></div>
	<div class="relative bg-white dark:bg-gray-800 rounded-t-xl sm:rounded-xl w-full sm:max-w-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-5">
		<div class="flex items-center justify-between mb-3">
			<h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2"><i data-feather="archive" class="w-4 h-4 text-orange-500"></i> Arsipkan Surat</h3>
			<button @click="close()" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"><i data-feather="x" class="w-4 h-4"></i></button>
		</div>
		<div class="grid gap-4">
			<div>
				<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Cari Surat</label>
				<div class="relative">
					<input type="text" x-model="q" @input="debouncedSearch()" placeholder="Nomor / Perihal" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
					<i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
				</div>
				<p class="text-[11px] text-gray-400 mt-1">Menampilkan 20 hasil terbaru. Hanya surat keluar non-draft & belum diarsip.</p>
			</div>
			<div class="border rounded-lg overflow-hidden">
				<table class="w-full text-xs">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr>
							<th class="px-3 py-2 text-left">Nomor</th>
							<th class="px-3 py-2 text-left">Perihal</th>
							<th class="px-3 py-2 text-left">Tanggal</th>
							<th class="px-3 py-2 text-right">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<template x-for="r in results" :key="r.id">
							<tr @click="select(r)" class="border-t border-gray-100 dark:border-gray-700/60 transition-colors cursor-pointer" :class="selected && selected.id===r.id ? 'bg-amber-50 dark:bg-amber-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40'">
								<td class="px-3 py-2 font-mono text-[11px] text-gray-500" x-text="r.number"></td>
								<td class="px-3 py-2 text-gray-700 dark:text-gray-200" x-text="r.subject"></td>
								<td class="px-3 py-2 text-gray-600 dark:text-gray-300" x-text="r.date"></td>
								<td class="px-3 py-2 text-right">
									<button @click.stop="select(r)" class="px-2 py-1 rounded text-[11px]" :class="selected && selected.id===r.id ? 'bg-orange-600 text-white hover:bg-orange-500' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'" x-text="selected && selected.id===r.id ? 'Dipilih' : 'Pilih'"></button>
								</td>
							</tr>
						</template>
						<tr x-show="!loading && results.length===0">
							<td colspan="4" class="px-3 py-6 text-center text-[12px] text-gray-400">Tidak ada hasil</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="grid gap-3 md:grid-cols-3">
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
					<input type="date" x-model="start_date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
					<input type="date" x-model="end_date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div class="md:col-span-3">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Alasan Arsip</label>
					<input type="text" x-model="archive_reason" placeholder="Opsional, contoh: kegiatan selesai" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
				</div>
			</div>
		</div>
		<div class="mt-5 flex items-center justify-between">
			<div class="text-[11px] text-gray-500" x-text="selected ? ('Terpilih: '+selected.number) : 'Pilih surat di atas untuk diarsipkan'"></div>
			<div class="flex items-center gap-2">
				<button @click="close()" class="px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Batal</button>
				<button @click="submit()" :disabled="!selected || submitting" class="px-3 py-1.5 rounded bg-orange-600 hover:bg-orange-500 text-white disabled:opacity-50">Arsipkan</button>
			</div>
		</div>
	</div>
</div>

<script>
function archiveModal(){
	return {
		isOpen:false, q:'', results:[], loading:false, timer:null,
		selected:null, start_date:'', end_date:'', archive_reason:'', submitting:false,
		csrf(){ const el=document.querySelector('meta[name="csrf-token"]'); return el?el.content:''; },
		open(){ this.isOpen=true; this.search(); if(window.feather) feather.replace(); },
		close(){ this.isOpen=false; this.q=''; this.results=[]; this.selected=null; this.start_date=''; this.end_date=''; this.archive_reason=''; },
		debouncedSearch(){ if(this.timer) clearTimeout(this.timer); this.timer=setTimeout(()=>this.search(), 350); },
		async search(){
			this.loading=true;
			try{
				const url = new URL('/unit-kerja/api/letters/outgoing/search', window.location.origin);
				if(this.q) url.searchParams.set('q', this.q);
				const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
				const j = await r.json();
				this.results = j.success ? (j.data||[]) : [];
			} catch(e){ this.results=[]; }
			finally{ this.loading=false; if(window.feather) feather.replace(); }
		},
		select(r){ this.selected = r; },
		async submit(){
			if(!this.selected) return;
			this.submitting=true;
			try{
				const r = await fetch(`/unit-kerja/api/letters/${this.selected.id}/archive`, {
					method:'POST',
					headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.csrf() },
					body: JSON.stringify({ start_date:this.start_date||null, end_date:this.end_date||null, archive_reason:this.archive_reason||null })
				});
				const j = await r.json();
				if(!r.ok || !j.success) throw new Error(j.message||'Gagal mengarsipkan');
				this.close();
				window.location.reload();
			} catch(e){ alert(e.message||'Gagal mengarsipkan'); }
			finally{ this.submitting=false; }
		}
	}
}
</script>
