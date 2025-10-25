<template x-if="showRestore">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0"
		x-data="{
			note: '',
			async submitRestore() {
				if (!confirm('Yakin ingin memulihkan surat ini dari arsip?')) return;
				try {
					const res = await fetch(`/rektor/api/archives/assignments/${selected.id}/restore`, {
						method: 'POST',
						headers: {
							'Accept': 'application/json',
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
						},
						body: JSON.stringify({ note: this.note })
					});
					const data = await res.json();
					if (!res.ok) {
						alert(data.message || 'Gagal memulihkan surat');
						return;
					}
					alert(data.message || 'Surat berhasil dipulihkan!');
					closeAll();
					location.reload(); // Refresh halaman
				} catch (e) {
					console.error('Restore error:', e);
					alert('Gagal memulihkan surat. Silakan coba lagi.');
				}
			}
		}">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Pulihkan Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-5 text-sm" @submit.prevent="submitRestore()">
				<div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-lg p-4 text-xs text-amber-700 dark:text-amber-300 flex items-start gap-3">
					<i data-feather="alert-circle" class="w-4 h-4 mt-0.5"></i>
					<div>Memulihkan arsip akan mengembalikan surat ke status <span class="font-semibold">processed</span> dan kembali muncul di daftar aktif.</div>
				</div>
				<div>
					<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan Pemulihan (opsional)</label>
					<textarea x-model="note" rows="4" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" placeholder="Alasan memulihkan arsip..."></textarea>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Pulihkan</button>
				</div>
			</form>
		</div>
	</div>
</template>
