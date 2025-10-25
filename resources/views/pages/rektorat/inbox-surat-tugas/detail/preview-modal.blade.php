<template x-if="showPreview">
	<div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full max-w-5xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<!-- Header Modal -->
			<div class="flex items-center justify-between gap-4 p-6 border-b border-gray-200 dark:border-gray-700">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Preview Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || 'Draft'"></p>
				</div>
				<div class="flex items-center gap-2">
					<button @click="printPreview()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">
						<i data-feather="printer" class="w-4 h-4"></i> Cetak
					</button>
					<button @click="closeAll()" class="text-gray-400 hover:text-rose-600">
						<i data-feather="x" class="w-5 h-5"></i>
					</button>
				</div>
			</div>
			
			<!-- Content Area - Scrollable -->
			<div class="overflow-y-auto px-8 py-8 text-sm leading-relaxed bg-gray-50 dark:bg-gray-800/40 flex-1">
				<div id="printableArea" class="max-w-3xl mx-auto space-y-8">
					<!-- Include shared styles -->
					@include('templates.components.styles')
					
					<!-- Header Surat -->
					@include('templates.components.header')
					
					<!-- Title -->
					<div class="text-center space-y-1">
						<div class="letter-title" x-text="selected?.perihal || 'Surat Tugas'"></div>
						<div class="letter-number">
							<span>Nomor :</span>
							<span x-text="selected?.number || 'DRAFT'"></span>
						</div>
					</div>
					
					<!-- Konten surat (HTML dari editor) -->
					<div class="content prose prose-sm dark:prose-invert max-w-none" 
						 x-init="console.log('DEBUG selected:', selected); console.log('DEBUG konten:', selected?.konten)"
						 x-html="selected?.konten || '<p><em>Konten surat belum diisi.</em></p>'"></div>
					
					<!-- Tanda tangan -->
					<div class="signature">
						<p>Jakarta, <span x-text="selected?.tanggal || selected?.date || '—'"></span></p>
						<template x-if="selected?.signature">
							<div class="mt-4">
								<template x-if="selected.signature.signature_path">
									<img :src="`/storage/${selected.signature.signature_path}`" class="h-20 mx-auto mb-2" alt="Signature">
								</template>
								<template x-if="!selected.signature.signature_path && selected.signature.signature_data">
									<img :src="selected.signature.signature_data" class="h-20 mx-auto mb-2" alt="Signature">
								</template>
							</div>
						</template>
						<template x-if="!selected?.signature">
							<br><br><br>
						</template>
						<p x-text="selected?.signature?.signer_name || selected?.signer?.nama || '[Nama Penandatangan]'"></p>
						<p x-text="selected?.signature?.signer_title || selected?.signer?.jabatan || '[Jabatan Penandatangan]'"></p>
					</div>
					
					<!-- Penerima -->
					<div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700" x-show="(selected?.tujuanInternal && selected.tujuanInternal.length) || (selected?.tujuanExternal && selected.tujuanExternal.length)">
						<h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">Penerima</h4>
						<ul class="grid md:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
							<template x-for="u in selected?.tujuanInternal" :key="'i'+u">
								<li class="px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" x-text="u"></li>
							</template>
							<template x-for="e in selected?.tujuanExternal" :key="'e'+e">
								<li class="px-3 py-1.5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" x-text="e"></li>
							</template>
						</ul>
					</div>
					
					<!-- Lampiran & Penandatangan -->
					<div class="pt-4 border-t border-dashed border-gray-300 dark:border-gray-700 flex items-start justify-between gap-8">
						<div class="flex-1">
							<h4 class="text-xs font-semibold tracking-wide uppercase text-gray-500 dark:text-gray-400 mb-2">
								Lampiran (<span x-text="Array.isArray(selected?.attachments) ? selected.attachments.length : 0"></span>)
							</h4>
							<ul class="space-y-1 text-xs">
								<template x-for="a in selected?.attachments" :key="a.id || a.filename">
									<li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
										<i data-feather='paperclip' class='w-3.5 h-3.5 text-violet-500'></i>
										<span x-text="a.filename || a.nama"></span>
									</li>
								</template>
							</ul>
						</div>
						<div class="w-60 flex flex-col items-center text-center">
							<div class="text-[11px] uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">Penandatangan</div>
							<template x-if="selected?.signature || selected?.signer">
								<div class="space-y-1">
									<div class="h-16 flex items-center justify-center text-[10px] text-gray-400 dark:text-gray-600 italic">(Tanda Tangan Digital)</div>
									<div class="font-semibold text-xs text-gray-700 dark:text-gray-200" x-text="selected?.signature?.signer_name || selected?.signer?.nama"></div>
									<div class="text-[10px] text-gray-500 dark:text-gray-400" x-text="selected?.signature?.signer_title || selected?.signer?.jabatan"></div>
								</div>
							</template>
							<template x-if="!selected?.signature && !selected?.signer">
								<div class="text-[10px] text-gray-400">Belum ada penandatangan.</div>
							</template>
						</div>
					</div>

					<!-- Footer -->
					@include('templates.components.footer')
				</div>
			</div>
			
			<!-- Footer Modal -->
			<div class="flex items-center justify-end gap-2 p-6 border-t border-gray-200 dark:border-gray-700">
				<button type="button" @click="printPreview()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">
					<i data-feather="printer" class="w-4 h-4"></i> Cetak
				</button>
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-600 hover:bg-gray-500 text-white font-medium">Tutup</button>
			</div>
		</div>
	</div>
</template>

<script>
function printPreview() {
	const printContents = document.getElementById('printableArea').innerHTML;
	const originalContents = document.body.innerHTML;
	
	// Tampilkan hanya area yang akan dicetak
	document.body.innerHTML = printContents;
	window.print();
	
	// Kembalikan konten asli
	document.body.innerHTML = originalContents;
	
	// Reload halaman untuk mengembalikan event listeners
	window.location.reload();
}
</script>
