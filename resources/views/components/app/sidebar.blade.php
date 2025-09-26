<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <!-- Sidebar -->
    <div id="sidebar"
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs' }}"
        :class="sidebarOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false">

        <!-- Sidebar header -->
        <div class="flex justify-between mb-10 pr-3 sm:px-2">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="sidebarOpen = !sidebarOpen"
                aria-controls="sidebar" :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>
            <!-- Logo -->
            <a class="flex items-center" href="/">
                <img class="h-10 w-auto mr-3" src="{{ asset('images/logo.svg') }}" alt="Logo">
                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                    <span class="text-gray-800 dark:text-gray-100 font-bold text-sm block">Clan'a Private</span>
                    <span class="text-gray-800 dark:text-gray-100 font-bold text-sm block"></span>
                </div>
            </a>
        </div>

        <!-- Links -->
        <div class="space-y-8">
            <div>
                <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                    <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                    <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Menu</span>
                </h3>
                @php
                    $user = Auth::user();
                    // Helper closure untuk status aktif
                    $isActive = function($patterns){
                        foreach((array)$patterns as $p){ if(request()->routeIs($p)) return true; }
                        return false;
                    };
                    // Helper generate li
                    $navItem = function($label,$icon,$route=null,$patterns=null) use ($isActive){
                        $patterns = $patterns ?? $route;
                        $active = $route && $isActive((array)$patterns);
                        $classes = 'pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 transition-colors';
                        $activeBg = $active ? ' from-orange-500/[0.12] dark:from-orange-500/[0.24] to-orange-500/[0.04]' : '';
                        $textClass = $active ? 'text-gray-900 dark:text-white' : 'text-gray-800 dark:text-gray-100';
                        $iconClass = $active ? 'text-orange-500' : 'text-gray-400 dark:text-gray-500';
                        $hover = $active ? '' : 'hover:text-gray-900 dark:hover:text-white';
                        $url = $route && Route::has($route) ? route($route) : '#';
                        return "<li class=\"bg-linear-to-r$activeBg $classes\"><a href=\"$url\" class=\"block truncate $hover $textClass\"><div class=\"flex items-center\"><i data-feather=\"$icon\" class=\"shrink-0 $iconClass\" width=\"16\" height=\"16\"></i><span class=\"text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200\">$label</span></div></a></li>"; };
                @endphp
                <ul class="mt-3">
                    {{-- Common / Role-specific navigation --}}
                    @if($user->role === 'admin')
                        {!! $navItem('Dashboard','home','dashboard') !!}
                        <div class="mt-4 mb-2 pl-3 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-600">Manajemen</div>
                        {!! $navItem('Pengguna','users','admin.users.index',['admin.users.*']) !!}
                        {!! $navItem('Departemen','layers','admin.departments.index',['admin.departments.*']) !!}
                        {!! $navItem('Jenis Surat','tag','admin.letter-types.index',['admin.letter-types.*']) !!}
                        {!! $navItem('Template Surat','file-text','admin.templates.index',['admin.templates.*']) !!}
                        {!! $navItem('Monitoring Sistem','activity','admin.monitoring',['admin.monitoring','horizon.*','telescope']) !!}
                        {!! $navItem('Pengaturan','settings','admin.settings',['admin.settings*']) !!}
                    @elseif($user->role === 'rektorat')
                        {!! $navItem('Dashboard','home','dashboard') !!}
                        <div class="mt-4 mb-2 pl-3 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-600">Surat & Tugas</div>
                        {!! $navItem('Surat Masuk','inbox','letters.incoming.index',['letters.incoming.*']) !!}
                        {!! $navItem('Surat Tugas','file-text','letters.tasks.index',['letters.tasks.index','letters.tasks.show']) !!}
                        {!! $navItem('Inbox Surat Tugas','mail','letters.tasks.inbox',['letters.tasks.inbox']) !!}
                        {!! $navItem('Buat / Tindaklanjuti ST','edit','letters.tasks.create',['letters.tasks.create','letters.tasks.edit']) !!}
                        {!! $navItem('History Disposisi','clock','dispositions.history',['dispositions.history']) !!}
                        {!! $navItem('Arsip Surat Tugas','archive','letters.tasks.archive',['letters.tasks.archive']) !!}
                    @elseif($user->role === 'unit_kerja')
                        {!! $navItem('Dashboard','home','dashboard') !!}
                        <div class="mt-4 mb-2 pl-3 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-600">Surat</div>
                        {!! $navItem('Surat Masuk','inbox','letters.incoming.index',['letters.incoming.*']) !!}
                        {!! $navItem('Buat Surat','file-plus','letters.outgoing.create',['letters.outgoing.create','letters.outgoing.edit']) !!}
                        {!! $navItem('Arsip Surat Tugas','archive','letters.tasks.archive',['letters.tasks.archive']) !!}
                    @endif
                </ul>
            </div>
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="w-12 pl-4 pr-3 py-2">
                <button
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 transition-colors"
                    @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 sidebar-expanded:rotate-180"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M15 16a1 1 0 0 1-1-1V1a1 1 0 1 1 2 0v14a1 1 0 0 1-1 1ZM8.586 7H1a1 1 0 1 0 0 2h7.586l-2.793 2.793a1 1 0 1 0 1.414 1.414l4.5-4.5A.997.997 0 0 0 12 8.01M11.924 7.617a.997.997 0 0 0-.217-.324l-4.5-4.5a1 1 0 0 0-1.414 1.414L8.586 7M12 7.99a.996.996 0 0 0-.076-.373Z" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
