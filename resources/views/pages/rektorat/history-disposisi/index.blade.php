<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			// Modals
			showView:false, showRoute:false, showNotes:false, showAttachments:false, showHistory:false,
			// Data
			items: [],
			meta: { total: 0, current_page: 1, last_page: 1, per_page: 10 },
			filters: { q:'', date:'', status:'', priority:'', to:'' },
			selected:null,
			steps: [],
			notes: [],
			attachmentsList: [],
			logs: [],
			loading:false,
			refreshIcons(){ queueMicrotask(()=>{ if(window.feather?.replace) window.feather.replace(); }); },
			statusClass(s){
				const map = { in_progress:'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400', forwarded:'bg-blue-500/10 text-blue-600 dark:text-blue-400', completed:'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', archived:'bg-slate-500/10 text-slate-600 dark:text-slate-400' };
				return map[s] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-300';
			},
			priorityClass(p){
				const map = { low:'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', normal:'bg-slate-500/10 text-slate-600 dark:text-slate-300', high:'bg-amber-500/10 text-amber-600 dark:text-amber-400', urgent:'bg-rose-500/10 text-rose-600 dark:text-rose-400' };
				return map[p] ?? '';
			},
			priorityDot(p){
				return p==='urgent'?'bg-rose-500':(p==='high'?'bg-amber-500':(p==='normal'?'bg-slate-400':'bg-emerald-500'));
			},
			normalizeStatus(row){
				// Map archived based on letter_status; map forwarded from pending to label only
				if(row.letter_status==='archived') return 'archived';
				return row.status==='pending' ? 'forwarded' : row.status;
			},
			async fetchDispositions(page=1){
				this.loading=true;
				try{
					const params = new URLSearchParams({
						q: this.filters.q || '',
						status: this.filters.status || '',
						priority: this.filters.priority || '',
						to: this.filters.to || '',
						per_page: this.meta.per_page,
						page,
					});
					if(this.filters.date){ params.set('date_from', this.filters.date); params.set('date_to', this.filters.date); }
					const res = await fetch(`/rektor/api/history/dispositions?${params.toString()}`, { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					this.items = json.data ?? [];
					this.meta = { ...this.meta, ...(json.meta ?? {}) };
					this.refreshIcons();
				}catch(e){ console.error(e); }
				finally{ this.loading=false; }
			},
			submitFilters(e){ e?.preventDefault?.(); this.fetchDispositions(1); },
			resetFilters(){ this.filters={ q:'', date:'', status:'', priority:'', to:'' }; this.fetchDispositions(1); },
			open(modal,row=null){ this.selected=row; this[modal]=true; },
			closeAll(){ this.showView=false; this.showRoute=false; this.showNotes=false; this.showAttachments=false; this.showHistory=false; },
			async loadDetail(){ if(!this.selected?.id) return; try{ const res=await fetch(`/rektor/api/history/dispositions/${this.selected.id}`,{headers:{'Accept':'application/json'}}); const j=await res.json(); this.selected = { ...(this.selected||{}), ...(j.data||{}) }; }catch(e){ console.error(e); } },
			async loadRoute(){ if(!this.selected?.id) return; try{ const res=await fetch(`/rektor/api/history/dispositions/${this.selected.id}/route`,{headers:{'Accept':'application/json'}}); const j=await res.json(); this.steps = j.data ?? []; }catch(e){ console.error(e); } },
			async loadNotes(){ if(!this.selected?.id) return; try{ const res=await fetch(`/rektor/api/history/dispositions/${this.selected.id}/notes`,{headers:{'Accept':'application/json'}}); const j=await res.json(); this.notes = j.data ?? []; }catch(e){ console.error(e); } },
			async loadAttachments(){ if(!this.selected?.id) return; try{ const res=await fetch(`/rektor/api/history/dispositions/${this.selected.id}/attachments`,{headers:{'Accept':'application/json'}}); const j=await res.json(); this.attachmentsList = j.data ?? []; }catch(e){ console.error(e); } },
			async loadTimeline(){ if(!this.selected?.id) return; try{ const res=await fetch(`/rektor/api/history/dispositions/${this.selected.id}/history`,{headers:{'Accept':'application/json'}}); const j=await res.json(); this.logs = j.data ?? []; }catch(e){ console.error(e); } },
		 }"
		 x-init="fetchDispositions()"
		 @refresh-history-disposisi.window="fetchDispositions(meta.current_page)"
		 @keydown.escape.window="closeAll()"
	>

		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-indigo-400/30">
						<i data-feather="git-branch" class="w-6 h-6"></i>
					</span>
					History Disposisi
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="$dispatch('refresh-history-disposisi')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
					<i data-feather="refresh-cw" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Refresh</span>
				</button>
			</div>
		</div>

		<!-- Filter -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
			<form @submit.prevent="submitFilters" class="grid gap-4 md:gap-6 md:grid-cols-7 items-end text-sm">
				<div class="md:col-span-2">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
					<div class="relative">
						<i data-feather="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
						<input type="text" x-model="filters.q" placeholder="No / Perihal / Asal" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" x-model="filters.date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select x-model="filters.status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="in_progress">Dalam Proses</option>
						<option value="forwarded">Diteruskan</option>
						<option value="completed">Selesai</option>
						<option value="archived">Arsip</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select x-model="filters.priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="urgent">Urgent</option>
						<option value="high">High</option>
						<option value="normal">Normal</option>
						<option value="low">Low</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tujuan Akhir</label>
					<input type="text" x-model="filters.to" placeholder="Unit / Jabatan" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div class="flex gap-2 md:col-span-1">
					<button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium rounded-lg px-4 py-2 transition">Filter</button>
					<button type="button" @click="resetFilters" class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-xs text-center">Reset</button>
				</div>
			</form>
		</div>

		<!-- Table -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
			<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-violet-500"></i> Riwayat Disposisi</h2>
				<div class="flex items-center gap-3 text-[11px] text-gray-500 dark:text-gray-400">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
				</div>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr class="text-[11px] uppercase tracking-wide">
							<th class="px-5 py-3 text-left font-semibold">No Surat</th>
							<th class="px-5 py-3 text-left font-semibold">Perihal</th>
							<th class="px-5 py-3 text-left font-semibold">Asal</th>
							<th class="px-5 py-3 text-left font-semibold">Tgl Surat</th>
							<th class="px-5 py-3 text-left font-semibold">Tujuan Akhir</th>
							<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
							<th class="px-5 py-3 text-left font-semibold">Status</th>
							<th class="px-5 py-3 text-center font-semibold">Langkah</th>
							<th class="px-5 py-3 text-center font-semibold">Lampiran</th>
							<th class="px-5 py-3 text-center font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						<template x-for="row in items" :key="row.id">
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
								<td class="px-5 py-3 font-mono text-xs text-violet-600 dark:text-violet-400" x-text="row.number"></td>
								<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
									<div class="font-medium flex items-center gap-2">
										<span class="w-1.5 h-1.5 rounded-full" :class="priorityDot(row.priority)"></span>
										<span class="line-clamp-1" x-text="row.subject"></span>
									</div>
								</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300" x-text="row.origin"></td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300" x-text="row.date"></td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300" x-text="row.to"></td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="priorityClass(row.priority)" x-text="row.priority"></span></td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="statusClass(normalizeStatus(row))" x-text="normalizeStatus(row)"></span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300" x-text="row.chain"></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300" x-text="row.attachments"></td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-center gap-1">
										<button @click="selected=row; showView=true; await loadDetail(); await loadTimeline();" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Detail"><i data-feather="eye" class="w-3.5 h-3.5"></i></button>
										<button @click="selected=row; showRoute=true; loadRoute();" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Alur"><i data-feather="git-commit" class="w-3.5 h-3.5"></i></button>
										<button @click="selected=row; showNotes=true; loadNotes();" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Catatan"><i data-feather="message-circle" class="w-3.5 h-3.5"></i></button>
										<button @click="selected=row; showAttachments=true; loadAttachments();" x-show="row.attachments>0" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Lampiran"><i data-feather="paperclip" class="w-3.5 h-3.5"></i></button>
										{{-- <button @click="selected=row; showHistory=true; loadTimeline();" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Riwayat"><i data-feather="clock" class="w-3.5 h-3.5"></i></button> --}}
									</div>
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span x-text="`Menampilkan ${items.length} dari ${meta.total} disposisi`"></span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" :disabled="meta.current_page<=1" @click="fetchDispositions(meta.current_page-1)"><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded" :class="{'bg-violet-600 text-white': true}"><span x-text="meta.current_page"></span></button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" :disabled="meta.current_page>=meta.last_page" @click="fetchDispositions(meta.current_page+1)"><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.rektorat.history-disposisi.detail.view-modal')
		@include('pages.rektorat.history-disposisi.detail.route-modal')
		@include('pages.rektorat.history-disposisi.detail.notes-modal')
		@include('pages.rektorat.history-disposisi.detail.attachment-modal')
		@include('pages.rektorat.history-disposisi.detail.history-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
