<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showView:false, showCreate:false, showParticipants:false, showSign:false, showHistory:false,
			showConfirm:false, showChangeStatus:false, showPreview:false, showFollowup:false, showFollowupList:false,
			selected:null,
			items: [],
			availableDepartments: [],
			availableUsers: [],
			meta: { total: 0, current_page: 1, last_page: 1, per_page: 10 },
			filters: { q: '', date: '', start_from: '', end_to: '', status: '', priority: '' },
			loadingList: false,
			loadingDetail: false,
			loadingParticipants: false,
			loadingFollowups: false,
			participants: [],
			followupsList: [],
			newParticipant: { nama: '', nip: '', jabatan: 'anggota', status: '' },
			creating: false,
			createForm: { subject: '', letter_date: '', priority: 'normal', selected_user: '', attachments: [], notes: { ringkasan: '', klasifikasi: '', tujuanInternal: [], tujuanExternal: [], start_date: '', end_date: '', participants: [], catatanInternal: null, destination: '', selectedUsers: [] } },
			logs: [],
			confirmNote: '',
			statusForm: { status: '', note: '' },
			followupForm: { type: 'progress', followup_date: '', completion_percentage: 0, title: '', description: '', pic_name: '', department: '', notes: '', attachment: null },
			submittingFollowup: false,
			statusLabel(s){
				const map = { draft:'Draft', pending:'Pending', processed:'Diproses', rejected:'Ditolak', closed:'Ditutup', archived:'Arsip' };
				return map[s] ?? s;
			},
			csrf: document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') ?? '',
			refreshIcons(){
				// Defer to next tick so the DOM is updated before replacing icons
				queueMicrotask(() => { if(window.feather && typeof window.feather.replace === 'function'){ window.feather.replace(); } });
			},
			async fetchDepartments(){
				try{
					const res = await fetch('/rektor/api/departments', { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					if(json.success) this.availableDepartments = json.data ?? [];
				}catch(e){ console.error('Failed to load departments:', e); }
			},
			async fetchUsers(){
				try{
					const res = await fetch('/rektor/api/users', { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					if(json.success) this.availableUsers = json.data ?? [];
				}catch(e){ console.error('Failed to load users:', e); }
			},
			async fetchAssignments(page=1){
				this.loadingList = true;
				try{
					const params = new URLSearchParams({ ...this.filters, per_page: this.meta.per_page, page });
					const res = await fetch(`/rektor/api/assignments?${params.toString()}`, { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					this.items = json.data ?? [];
					this.meta = { ...this.meta, ...(json.meta ?? {}) };
					this.refreshIcons();
				}catch(e){ console.error(e); }
				finally{ this.loadingList = false; }
			},
			submitFilters(e){ e.preventDefault(); this.fetchAssignments(1); },
			resetFilters(){ this.filters={ q:'', date:'', start_from:'', end_to:'', status:'', priority:'' }; this.fetchAssignments(1); },
			open(modal,row=null){ 
				this.selected=row; 
				this[modal]=true; 
				if(modal==='showView' && row){ this.loadDetail(row.id); } 
				if(modal==='showParticipants' && row){ this.loadParticipants(row.id); } 
				if(modal==='showHistory' && row){ this.loadHistory(row.id);} 
				if(modal==='showFollowup' && row){ 
					this.followupForm = { 
						type: 'progress', 
						followup_date: new Date().toISOString().split('T')[0], 
						completion_percentage: 0, 
						title: '', 
						description: '', 
						pic_name: '', 
						department: '', 
						notes: '', 
						attachment: null 
					}; 
				}
				if(modal==='showFollowupList' && row){ this.loadFollowups(row.id); }
			},
			closeAll(){ this.showView=false; this.showCreate=false; this.showParticipants=false; this.showSign=false; this.showHistory=false; this.showConfirm=false; this.showChangeStatus=false; this.showFollowup=false; this.showFollowupList=false; },
			async loadDetail(id){
				this.loadingDetail = true;
				try{
					const res = await fetch(`/rektor/api/assignments/${id}`, { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					this.selected = json;
				}catch(e){ console.error(e); }
				finally{ this.loadingDetail = false; }
			},
			async loadParticipants(id){
				this.loadingParticipants = true;
				try{
					const res = await fetch(`/rektor/api/assignments/${id}/participants`, { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					this.participants = json.participants ?? [];
				}catch(e){ console.error(e); }
				finally{ this.loadingParticipants = false; }
			},
			async addParticipant(){
				if(!this.selected?.id) return;
				try{
					const res = await fetch(`/rektor/api/assignments/${this.selected.id}/participants`, {
						method:'POST',
						headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': this.csrf },
						body: JSON.stringify(this.newParticipant)
					});
					const json = await res.json();
					if(res.ok){ this.participants = json.participants ?? []; this.newParticipant = { nama:'', nip:'', jabatan:'anggota', status:'' }; }
				}catch(e){ console.error(e); }
			},
			async removeParticipant(idx){
				if(!this.selected?.id) return;
				try{
					const res = await fetch(`/rektor/api/assignments/${this.selected.id}/participants/${idx}`, { method:'DELETE', headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': this.csrf } });
					const json = await res.json();
					if(res.ok){ this.participants = json.participants ?? []; }
				}catch(e){ console.error(e); }
			},
			async loadHistory(id){
				try{
					const res = await fetch(`/rektor/api/assignments/${id}/history`, { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					this.logs = json.logs ?? [];
				}catch(e){ console.error(e); }
			},
			async loadFollowups(id){
				this.loadingFollowups = true;
				try{
					const res = await fetch(`/rektor/api/assignments/${id}/followups`, { headers: { 'Accept':'application/json' } });
					const json = await res.json();
					this.followupsList = json.data ?? [];
					this.refreshIcons();
				}catch(e){ console.error(e); }
				finally{ this.loadingFollowups = false; }
			},
			addSelectedUser(){
				if(!this.createForm.selected_user) return;
				try{
					const user = JSON.parse(this.createForm.selected_user);
					// Check if user already added
					const exists = this.createForm.notes.selectedUsers.some(u => u.id === user.id);
					if(!exists){
						this.createForm.notes.selectedUsers.push({
							id: user.id,
							name: user.name,
							position: user.position,
							department: user.department
						});
						this.refreshDestination();
					}
					this.createForm.selected_user = ''; // Reset selection
					this.refreshIcons();
				}catch(e){ console.error('Error parsing user:', e); }
			},
			refreshDestination(){
				// Update destination field with first selected user name
				if(this.createForm.notes.selectedUsers.length > 0){
					this.createForm.notes.destination = this.createForm.notes.selectedUsers[0].name;
					// Also update tujuanInternal with all selected user names
					this.createForm.notes.tujuanInternal = this.createForm.notes.selectedUsers.map(u => u.name);
				} else {
					this.createForm.notes.destination = '';
					this.createForm.notes.tujuanInternal = [];
				}
			},
			handleFileUpload(event){
				const files = event.target.files;
				if(files && files.length > 0){
					const file = files[0];
					// Check file size (max 5MB)
					if(file.size > 5 * 1024 * 1024){
						alert('File terlalu besar! Maksimal 5MB');
						event.target.value = '';
						return;
					}
					// Add to attachments array
					if(!this.createForm.attachments){
						this.createForm.attachments = [];
					}
					this.createForm.attachments.push({
						name: file.name,
						size: file.size,
						type: file.type,
						file: file
					});
					// Reset input
					event.target.value = '';
					this.refreshIcons();
				}
			},
			async submitCreate(){
				this.creating = true;
				try{
					// Validate required fields
					if(!this.createForm.subject || !this.createForm.letter_date){
						alert('Perihal dan Tanggal Surat harus diisi!');
						this.creating = false;
						return;
					}

					// Auto-update destination from selectedUsers
					this.refreshDestination();
					
					const res = await fetch('/rektor/api/assignments', {
						method:'POST',
						headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': this.csrf },
						body: JSON.stringify({
							subject: this.createForm.subject,
							letter_date: this.createForm.letter_date,
							priority: this.createForm.priority,
							notes: {
								ringkasan: this.createForm.notes.ringkasan || '',
								klasifikasi: this.createForm.notes.klasifikasi || '',
								tujuanInternal: this.createForm.notes.tujuanInternal || [],
								tujuanExternal: this.createForm.notes.tujuanExternal || [],
								start_date: this.createForm.notes.start_date || '',
								end_date: this.createForm.notes.end_date || '',
								participants: this.createForm.notes.participants || [],
								selectedUsers: this.createForm.notes.selectedUsers || [],
								catatanInternal: this.createForm.notes.catatanInternal ?? null,
							}
						})
					});
					
					const json = await res.json();
					
					if(res.ok){ 
						alert('Surat tugas berhasil dibuat!\nNomor: ' + (json.data?.number || 'DRAFT'));
						this.closeAll(); 
						// Reset form completely
						this.createForm = { 
							subject: '', 
							letter_date: '', 
							priority: 'normal', 
							selected_user: '', 
							attachments: [], 
							notes: { 
								ringkasan: '', 
								klasifikasi: '', 
								tujuanInternal: [], 
								tujuanExternal: [], 
								start_date: '', 
								end_date: '', 
								participants: [], 
								catatanInternal: null, 
								destination: '', 
								selectedUsers: [] 
							} 
						};
						this.newParticipant = { nama: '', nip: '', jabatan: 'anggota', status: '' };
						this.fetchAssignments(1); 
					} else {
						alert('Gagal membuat surat: ' + (json.message || 'Terjadi kesalahan'));
					}
				}catch(e){ 
					console.error(e); 
					alert('Terjadi kesalahan saat menyimpan surat tugas');
				}
				finally{ this.creating = false; }
			},
			async submitConfirm(){
				if(!this.selected?.id) return;
				try{
					const res = await fetch(`/rektor/api/assignments/${this.selected.id}/confirm`, {
						method:'POST',
						headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': this.csrf },
						body: JSON.stringify({ note: this.confirmNote })
					});
					if(res.ok){ this.closeAll(); await this.fetchAssignments(this.meta.current_page); }
				}catch(e){ console.error(e); }
			},
			async submitStatusChange(){
				if(!this.selected?.id) return;
				try{
					const res = await fetch(`/rektor/api/assignments/${this.selected.id}/status`, {
						method:'PATCH',
						headers:{ 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': this.csrf },
						body: JSON.stringify(this.statusForm)
					});
					if(res.ok){ this.closeAll(); await this.fetchAssignments(this.meta.current_page); }
				}catch(e){ console.error(e); }
			},
			async submitFollowup(){
				if(!this.selected?.id) return;
				this.submittingFollowup = true;
				try{
					const formData = new FormData();
					formData.append('type', this.followupForm.type);
					formData.append('followup_date', this.followupForm.followup_date);
					formData.append('completion_percentage', this.followupForm.completion_percentage || 0);
					formData.append('title', this.followupForm.title);
					formData.append('description', this.followupForm.description);
					if(this.followupForm.pic_name) formData.append('pic_name', this.followupForm.pic_name);
					if(this.followupForm.department) formData.append('department', this.followupForm.department);
					if(this.followupForm.notes) formData.append('notes', this.followupForm.notes);
					if(this.followupForm.attachment) formData.append('attachment', this.followupForm.attachment);

					const res = await fetch(`/rektor/api/assignments/${this.selected.id}/followups`, {
						method:'POST',
						headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': this.csrf },
						body: formData
					});
					const json = await res.json();
					if(res.ok){ 
						this.closeAll(); 
						await this.fetchAssignments(this.meta.current_page);
						// Show success notification (you can add toast notification here)
						alert('Tindak lanjut berhasil disimpan!');
					} else {
						alert(json.message || 'Gagal menyimpan tindak lanjut');
					}
				}catch(e){ 
					console.error(e); 
					alert('Terjadi kesalahan saat menyimpan tindak lanjut');
				} finally {
					this.submittingFollowup = false;
				}
			}
		 }"
		 x-init="fetchAssignments(); fetchDepartments(); fetchUsers();"
		 @refresh-assignment-letters.window="fetchAssignments(meta.current_page)"
		 @keydown.escape.window="closeAll()"
	>

		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
						<i data-feather="briefcase" class="w-5 h-5"></i>
					</span>
					Surat Tugas
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="open('showCreate')" class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
					<i data-feather="plus" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Buat Surat Tugas</span>
				</button>
				<button @click="$dispatch('refresh-assignment-letters')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
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
						<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<i data-feather="search" class="w-4 h-4 text-gray-400"></i>
						</span>
						<input type="text" x-model="filters.q" placeholder="Nomor / Perihal / Tujuan" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" x-model="filters.date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
					<input type="date" x-model="filters.start_from" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
					<input type="date" x-model="filters.end_to" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select x-model="filters.status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="draft">Draft</option>
						<option value="pending">Pending</option>
						<option value="processed">Diproses</option>
						<option value="rejected">Ditolak</option>
						<option value="closed">Ditutup</option>
						<option value="archived">Arsip</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select x-model="filters.priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="low">Low</option>
						<option value="normal">Normal</option>
						<option value="high">High</option>
						<option value="urgent">Urgent</option>
					</select>
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
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-amber-500"></i> Daftar Surat Tugas</h2>
				<div class="flex items-center gap-3 text-[11px] text-gray-500 dark:text-gray-400 flex-wrap">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
					<span class="mx-2 h-3 w-px bg-gray-300 dark:bg-gray-600"></span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Pending</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Diproses</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Ditolak</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-500"></span>Ditutup</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-500"></span>Arsip</span>
				</div>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr>
							<th class="text-left px-5 py-3 font-semibold">Nomor</th>
							<th class="text-left px-5 py-3 font-semibold">Perihal</th>
							<th class="text-left px-5 py-3 font-semibold">Tujuan / Tim</th>
							<th class="text-left px-5 py-3 font-semibold">Tanggal Surat</th>
							<th class="text-left px-5 py-3 font-semibold">Prioritas</th>
							<th class="text-left px-5 py-3 font-semibold">Status</th>
							<th class="text-left px-5 py-3 font-semibold">Peserta</th>
							<th class="text-left px-5 py-3 font-semibold">File</th>
							<th class="px-5 py-3"></th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						<template x-for="row in items" :key="row.id">
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
								<td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-200" x-text="row.number"></td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300 max-w-[260px]"><span class="line-clamp-1" x-text="row.subject"></span></td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300" x-text="row.destination"></td>
								<td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs" x-text="row.date"></td>
								<td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-medium" :class="{
									'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': row.priority=='low',
									'bg-slate-500/10 text-slate-600 dark:text-slate-300': row.priority=='normal',
									'bg-amber-500/10 text-amber-600 dark:text-amber-400': row.priority=='high',
									'bg-rose-500/10 text-rose-600 dark:text-rose-400': row.priority=='urgent'
								}" x-text="row.priority"></span></td>
								<td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-medium" :class="{
									'bg-slate-500/10 text-slate-600 dark:text-slate-300': row.status=='draft',
									'bg-amber-500/10 text-amber-600 dark:text-amber-400': row.status=='pending',
									'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': row.status=='processed',
									'bg-rose-500/10 text-rose-600 dark:text-rose-400': row.status=='rejected',
                                    'bg-gray-500/10 text-gray-600 dark:text-gray-300': row.status=='closed',
									'bg-slate-500/10 text-slate-600 dark:text-slate-400': row.status=='archived',
								}" x-text="statusLabel(row.status)"></span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300" x-text="row.participants"></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300" x-text="row.files"></td>
								<td class="px-5 py-3 text-right">
									<div class="flex items-center justify-end gap-1.5">
										<!-- Detail -->
										<button @click="open('showView', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Detail">
											<i data-feather="eye" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
											<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100">Detail</span>
										</button>
										<!-- Riwayat Tindak Lanjut -->
										<button @click="open('showFollowupList', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Riwayat Tindak Lanjut">
											<i data-feather="list" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
											<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100 whitespace-nowrap">Riwayat Tindak Lanjut</span>
										</button>
										<!-- Peserta -->
										<button @click="open('showParticipants', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Peserta">
											<i data-feather="users" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
											<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100">Peserta</span>
										</button>
										<!-- Konfirmasi (draft/pending) -->
										<template x-if="['draft','pending'].includes(row.status)">
											<button @click="open('showConfirm', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Konfirmasi">
												<i data-feather="check-circle" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
												<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100">Konfirmasi</span>
											</button>
										</template>
										<!-- Ubah Status -->
										<button @click="open('showChangeStatus', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Ubah Status">
											<i data-feather="sliders" class="w-4 h-4 text-slate-600 dark:text-slate-300"></i>
											<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100">Ubah Status</span>
										</button>

										<!-- Tindak Lanjut (processed status) -->
										<template x-if="['processed','pending'].includes(row.status)">
											<button @click="open('showFollowup', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Tindak Lanjut">
												<i data-feather="clipboard" class="w-4 h-4 text-violet-600 dark:text-violet-400"></i>
												<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100 whitespace-nowrap">Tindak Lanjut</span>
											</button>
										</template>

										<!-- TTD (draft/pending) -->
										<template x-if="['draft','pending'].includes(row.status)">
											<button @click="open('showSign', row)" class="group relative inline-flex items-center justify-center p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="TTD">
												<i data-feather="pen-tool" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
												<span class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 text-[10px] rounded bg-gray-800 dark:bg-gray-900 text-white opacity-0 group-hover:opacity-100">TTD</span>
											</button>
										</template>
									</div>
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span x-text="`Menampilkan ${items.length} dari ${meta.total} surat tugas`"></span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" :disabled="meta.current_page<=1" @click="fetchAssignments(meta.current_page-1)"><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded" :class="{'bg-amber-600 text-white': true}"><span x-text="meta.current_page"></span></button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" :disabled="meta.current_page>=meta.last_page" @click="fetchAssignments(meta.current_page+1)"><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.rektorat.surat-tugas.detail.view-modal')
		@include('pages.rektorat.surat-tugas.detail.preview-modal')
		@include('pages.rektorat.surat-tugas.detail.create-modal')
		@include('pages.rektorat.surat-tugas.detail.participants-modal')
		@include('pages.rektorat.surat-tugas.detail.sign-modal')
		@include('pages.rektorat.surat-tugas.detail.confirm-modal')
		@include('pages.rektorat.surat-tugas.detail.change-status-modal')
		@include('pages.rektorat.surat-tugas.detail.history-modal')
		@include('pages.rektorat.surat-tugas.detail.followup-modal')
		@include('pages.rektorat.surat-tugas.detail.followup-list-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
