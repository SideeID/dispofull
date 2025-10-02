<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showAdd:false, showEdit:false, showView:false, showDelete:false,
			selected:null,
			open(modal, row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showAdd=false; this.showEdit=false; this.showView=false; this.showDelete=false; },
		 }"
		 @keydown.escape.window="closeAll()"
	>
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
						<i data-feather="file-text" class="w-5 h-5"></i>
					</span>
					Manajemen Jenis Surat
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="open('showAdd')" class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
					<i data-feather="plus" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Tambah Jenis</span>
				</button>
				<button @click="$dispatch('refresh-letter-types')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
					<i data-feather="refresh-cw" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Refresh</span>
				</button>
			</div>
		</div>

		<div class="bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
			<form method="GET" action="#" class="grid gap-4 md:gap-6 md:grid-cols-5 items-end">
				<div class="md:col-span-2">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
					<div class="relative">
						<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<i data-feather="search" class="w-4 h-4 text-gray-400"></i>
						</span>
						<input type="text" name="q" placeholder="Nama atau kode jenis surat"
							   class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kategori</label>
					<select name="category" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="internal">Internal</option>
						<option value="eksternal">Eksternal</option>
						<option value="keputusan">Keputusan</option>
						<option value="undangan">Undangan</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="1">Aktif</option>
						<option value="0">Nonaktif</option>
					</select>
				</div>
				<div class="md:col-span-1">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Format Nomor</label>
					<select name="format" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="default">Default</option>
						<option value="romawi">Romawi</option>
						<option value="custom">Custom</option>
					</select>
				</div>
				<div class="flex gap-2">
					<button class="flex-1 bg-amber-600 hover:bg-amber-500 text-white text-sm font-medium rounded-lg px-4 py-2 transition">Filter</button>
					<a href="#" class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-sm text-center">Reset</a>
				</div>
			</form>
		</div>

		<div class="bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700">
			<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="grid" class="w-4 h-4 text-amber-500"></i> Daftar Jenis Surat</h2>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr>
							<th class="text-left px-5 py-3 font-semibold">Kode</th>
							<th class="text-left px-5 py-3 font-semibold">Nama Jenis</th>
							<th class="text-left px-5 py-3 font-semibold">Kategori</th>
							<th class="text-left px-5 py-3 font-semibold">Format Nomor</th>
							<th class="text-left px-5 py-3 font-semibold">Status</th>
							<th class="text-left px-5 py-3 font-semibold">Digunakan</th>
							<th class="text-right px-5 py-3 font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@php
							$letterTypes = $letterTypes ?? [
								['id'=>1,'code'=>'UND','name'=>'Undangan Resmi','category'=>'undangan','format'=>'{SEQ}/UND/BKR/{ROMAN_MONTH}/{YEAR}','active'=>true,'used'=>12,'description'=>'Untuk undangan resmi internal & eksternal'],
								['id'=>2,'code'=>'SK','name'=>'Surat Keputusan','category'=>'keputusan','format'=>'{SEQ}/SK/UB/{YEAR}','active'=>true,'used'=>8,'description'=>'Surat keputusan rektorat'],
								['id'=>3,'code'=>'INT','name'=>'Internal Memo','category'=>'internal','format'=>'{SEQ}/INT/{MONTH}/{YEAR}','active'=>false,'used'=>3,'description'=>'Memo internal unit kerja'],
							];
						@endphp
						@foreach($letterTypes as $t)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition" x-data="{ row: {{ json_encode($t) }} }">
								<td class="px-5 py-3 font-mono text-xs text-gray-700 dark:text-gray-200">{{ $t['code'] }}</td>
								<td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $t['name'] }}</td>
								<td class="px-5 py-3">
									<span class="px-2 py-0.5 rounded-lg text-[11px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">{{ ucfirst($t['category']) }}</span>
								</td>
								<td class="px-5 py-3 text-[11px] font-mono text-gray-500 dark:text-gray-400">{{ Str::limit($t['format'], 32) }}</td>
								<td class="px-5 py-3">
									@if($t['active'])
										<span class="px-2 py-0.5 rounded-lg text-[11px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Aktif</span>
									@else
										<span class="px-2 py-0.5 rounded-lg text-[11px] font-medium bg-gray-200 text-gray-600 dark:bg-gray-600/40 dark:text-gray-300">Nonaktif</span>
									@endif
								</td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $t['used'] }}</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-end gap-1">
										<button @click="open('showView', row)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Detail"><i data-feather="eye" class="w-4 h-4"></i></button>
										<button @click="open('showEdit', row)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Edit"><i data-feather="edit-2" class="w-4 h-4"></i></button>
										<button @click="open('showDelete', row)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-rose-500" title="Hapus"><i data-feather="trash" class="w-4 h-4"></i></button>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ count($letterTypes) }} dari {{ count($letterTypes) }} jenis surat</span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded bg-amber-600 text-white">1</button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.admin.jenis-surat.detail.add-modal')
		@include('pages.admin.jenis-surat.detail.edit-modal')
		@include('pages.admin.jenis-surat.detail.view-modal')
		@include('pages.admin.jenis-surat.detail.delete-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
