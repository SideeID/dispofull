<template x-if="showAttachments">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Lampiran Surat</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola file lampiran untuk surat.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 text-sm">
				<div class="space-y-4">
					<div class="flex items-center justify-between">
						<div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Daftar Lampiran ( <span x-text="attachments.length"></span> )</div>
						<button type="button" @click="attachments=[]" class="text-[11px] text-rose-500 hover:underline">Kosongkan</button>
					</div>
					<ul class="space-y-2 max-h-64 overflow-y-auto pr-1">
						<template x-for="(a,i) in attachments" :key="a.nama+i">
							<li class="flex items-center gap-3 p-2 rounded bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600 text-xs">
								<div class="flex items-center gap-2 flex-1 min-w-0">
									<i data-feather='paperclip' class='w-3.5 h-3.5 text-violet-500 shrink-0'></i>
									<span class="truncate" x-text="a.nama"></span>
									<span class="text-[10px] text-gray-400" x-text="a.size"></span>
								</div>
								<div class="flex items-center gap-1">
									<button type="button" class="p-1.5 rounded bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-600 text-violet-600 dark:text-violet-400 hover:bg-violet-50"><i data-feather='eye' class='w-3.5 h-3.5'></i></button>
									<button type="button" @click="removeAttachment(i)" class="p-1.5 rounded bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-600 text-rose-500 hover:text-rose-600"><i data-feather='trash-2' class='w-3.5 h-3.5'></i></button>
								</div>
							</li>
						</template>
						<li class="text-[11px] text-gray-400" x-show="attachments.length===0">Belum ada lampiran.</li>
					</ul>
				</div>
				<div class="space-y-4">
					<div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tambah Lampiran</div>
					<form @submit.prevent="uploadAttachment()" class="space-y-3 text-xs">
						<input type="file" x-ref="attachmentInput" class="block w-full text-[11px] text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-violet-500/10 dark:file:text-violet-300" />
						<p class="text-[10px] text-gray-400">PDF / DOC / ZIP / Gambar (max 5MB)</p>
						<div class="flex items-center justify-end">
							<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-violet-600 hover:bg-violet-500 text-white font-medium">Upload</button>
						</div>
					</form>
				</div>
			</div>
			<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-end gap-3 bg-white dark:bg-gray-800 rounded-b-2xl">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
			</div>
		</div>
	</div>
</template>
