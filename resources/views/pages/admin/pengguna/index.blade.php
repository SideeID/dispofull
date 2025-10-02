<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="{ showAdd: false, showEdit: false, showView: false, showDelete: false, selectedUser: null }">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold flex items-center gap-3">
                    <span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
						<i data-feather="users" class="w-5 h-5"></i>
					</span>
                    Manajemen Pengguna
                </h1>
            </div>
            <button @click="showAdd = true" class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
                <i data-feather="user-plus" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Tambah Pengguna</span>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700 p-6">
            <div class="mb-6 flex justify-between items-center">
                <form method="GET" action="#" class="w-full max-w-xs">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-feather="search" class="w-4 h-4 text-gray-400 dark:text-gray-400"></i>
                        </span>
                        <input type="text" name="search" class="bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400 text-sm rounded-lg pl-10 p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 dark:focus:ring-orange-500 dark:focus:border-orange-500" placeholder="Cari nama, email, atau NIP">
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-left text-gray-600 dark:text-gray-200">
                            <th class="px-4 py-3 rounded-l-lg">No.</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">NIP</th>
                            <th class="px-4 py-3">Departemen</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $users = [
                                ['id'=>1,'name'=>'Admin Surat','email'=>'admin@bakrie.ac.id','nip'=>'1978123456','department'=>'Rektorat','role'=>'admin','status'=>'Aktif'],
                                ['id'=>2,'name'=>'Bagian Akademik','email'=>'baa@bakrie.ac.id','nip'=>'1987654321','department'=>'BAA','role'=>'unit_kerja','status'=>'Aktif'],
                                ['id'=>3,'name'=>'Wakil Rektor II','email'=>'wr2@bakrie.ac.id','nip'=>'1978001122','department'=>'WR II','role'=>'rektorat','status'=>'Nonaktif'],
                            ];
                        @endphp
                        @foreach($users as $i => $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">{{ $i+1 }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $u['name'] }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $u['email'] }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $u['nip'] }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $u['department'] }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $u['role']=='admin' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : ($u['role']=='rektorat' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300') }}">{{ ucfirst($u['role']) }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $u['status']=='Aktif' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-gray-200 text-gray-700 dark:bg-gray-600/40 dark:text-gray-300' }}">{{ $u['status'] }}</span>
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <button @click="selectedUser = {{ Js::from($u) }}; showView = true" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Lihat Detail">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click="selectedUser = {{ Js::from($u) }}; showEdit = true" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button @click="selectedUser = {{ Js::from($u) }}; showDelete = true" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-rose-500" title="Hapus">
                                    <i data-feather="trash" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- <x-dynamic-component :component="'pages.admin.pengguna.detail.add-modal'" x-show="showAdd" @close="showAdd = false" />
        <x-dynamic-component :component="'pages.admin.pengguna.detail.edit-modal'" x-show="showEdit" :user="selectedUser" @close="showEdit = false" />
        <x-dynamic-component :component="'pages.admin.pengguna.detail.view-modal'" x-show="showView" :user="selectedUser" @close="showView = false" />
        <x-dynamic-component :component="'pages.admin.pengguna.detail.delete-modal'" x-show="showDelete" :user="selectedUser" @close="showDelete = false" /> --}}
    </div>
</x-app-layout>
