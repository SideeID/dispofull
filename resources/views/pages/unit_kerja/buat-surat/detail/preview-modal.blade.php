<template x-if="showPreview">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Pratinjau Surat</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="computedNumber()"></p>
				</div>
				<div class="flex items-center gap-2">
					<button @click="open('showTemplates')" class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='layers' class='w-3.5 h-3.5'></i> Template</button>
					<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
				</div>
			</div>
			<div class="overflow-y-auto px-8 py-8 text-sm leading-relaxed bg-gray-50 dark:bg-gray-800/40 flex-1">
				<div class="max-w-3xl mx-auto space-y-8">
					<div class="text-center space-y-1">
						<div class="font-semibold text-gray-800 dark:text-gray-100 text-lg" x-text="form.perihal || 'Judul / Perihal Surat' "></div>
						<div class="text-[11px] text-gray-400" x-text="'Tanggal: ' + (form.tanggal || '—')"></div>
					</div>
					<div class="prose prose-sm dark:prose-invert max-w-none" x-html="(form.konten||'').replace(/\n/g,'<br>') || '<p><em>Konten surat belum diisi.</em></p>'"></div>
					<div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700">
						<h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">Penerima</h4>
						<ul class="grid md:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
							<template x-for="u in form.tujuanInternal" :key="'i'+u"><li class="px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" x-text="u"></li></template>
							<template x-for="e in form.tujuanExternal" :key="'e'+e"><li class="px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" x-text="e"></li></template>
						</ul>
					</div>
					<div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700 flex items-start justify-between gap-8">
						<div class="flex-1">
							<h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">Lampiran (<span x-text="attachments.length"></span>)</h4>
							<ul class="space-y-1 text-xs">
								<template x-for="a in attachments" :key="a.nama"><li class="flex items-center gap-2 text-gray-600 dark:text-gray-300"><i data-feather='paperclip' class='w-3.5 h-3.5 text-violet-500'></i><span x-text="a.nama"></span></li></template>
							</ul>
						</div>
						<div class="w-60 flex flex-col items-center text-center">
							<div class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">Penandatangan</div>
							<template x-if="signer">
								<div class="space-y-1">
									<div class="h-16 flex items-center justify-center text-[10px] text-gray-400 dark:text-gray-600 italic">(Tanda Tangan Digital)</div>
									<div class="font-semibold text-xs text-gray-700 dark:text-gray-200" x-text="signer.nama"></div>
									<div class="text-[10px] text-gray-500 dark:text-gray-400" x-text="signer.jabatan"></div>
								</div>
							</template>
							<template x-if="!signer"><div class="text-[10px] text-gray-400">Belum memilih penandatangan.</div></template>
						</div>
					</div>
				</div>
			</div>
			<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-3 bg-white dark:bg-gray-900 rounded-b-2xl">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
				<button type="button" @click="open('showSubmitConfirm')" class="px-4 py-2 rounded-lg text-sm bg-violet-600 hover:bg-violet-500 text-white flex items-center gap-2"><i data-feather='send' class='w-4 h-4'></i>Ajukan TTD</button>
			</div>
		</div>
	</div>
</template>
