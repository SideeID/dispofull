<div class="min-w-fit">
    <div class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <div id="sidebar"
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs' }}"
        :class="sidebarOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false">

        <div class="flex justify-between mb-6 pr-3 sm:px-2 relative">
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="sidebarOpen = !sidebarOpen"
                aria-controls="sidebar" :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>
            <a class="flex items-center group" href="/">
                <div class="h-11 w-11 mr-3 rounded-2xl bg-white dark:bg-gray-700 flex items-center justify-center shadow ring-1 ring-gray-200 dark:ring-gray-600 relative overflow-hidden">
                    <img src="{{ asset('images/bakrie-logo.png') }}" alt="Logo Bakrie" class="w-9 h-9 object-contain">
                    <span class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.25),transparent_60%)] transition"></span>
                </div>
                <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                    <span class="text-gray-800 dark:text-gray-100 font-bold text-[13px] tracking-wide block">Surat Bakrie</span>
                    <span class="text-[10px] font-medium text-amber-600 dark:text-amber-400">Internal Beta</span>
                </div>
            </a>
        </div>

        <div class="mb-6 -mx-4 px-4">
            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700/60 to-transparent"></div>
        </div>

        <div class="space-y-8">
            <div>
                <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                    <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                    <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Menu</span>
                </h3>
                @php
                    $user = Auth::user();
                    $isActive = function($patterns){
                        foreach((array)$patterns as $p){ if(request()->routeIs($p)) return true; }
                        return false;
                    };
                    $navItem = function($label,$icon,$route=null,$patterns=null,$badge=null,$badgeColor='bg-amber-500/10 text-amber-600 dark:text-amber-300 dark:bg-amber-400/10') use ($isActive){
                        $patterns = $patterns ?? $route;
                        $active = $route && $isActive((array)$patterns);
                        $base = 'relative group pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 transition-colors flex items-center';
                        $activeClasses = $active ? 'bg-gradient-to-r from-orange-500/15 via-orange-400/10 to-transparent ring-1 ring-orange-400/30 dark:from-orange-500/25 dark:via-orange-500/10 dark:ring-orange-400/40' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40';
                        $textClass = 'text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200';
                        $iconWrap = 'w-8 h-8 rounded-xl flex items-center justify-center text-[13px]';
                        $iconState = $active ? 'text-orange-600 dark:text-amber-400 bg-orange-500/10 dark:bg-orange-500/20' : 'text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700/60 group-hover:text-gray-600 group-hover:bg-gray-200/80 dark:group-hover:text-gray-300';
                        $labelColor = $active ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-100 group-hover:text-gray-900 dark:group-hover:text-white';
                        $url = $route && Route::has($route) ? route($route) : '#';
                        $badgeHtml = $badge !== null && $badge !== '' ? "<span class=\"ml-auto text-[11px] font-semibold px-2 py-0.5 rounded-full $badgeColor lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200\">$badge</span>" : '';
                        $indicator = '';
                        return <<<HTML
                            <li>$indicator<a href="$url" class="$base $activeClasses">
                                <span class="$iconWrap $iconState"><i data-feather="$icon" class="w-4 h-4"></i></span>
                                <span class="$textClass $labelColor">$label</span>
                                $badgeHtml
                            </a></li>
                        HTML;
                    };
                    
                    // Get counts from view composer or default to empty array
                    $counts = $sidebarCounts ?? [];
                @endphp
                <ul class="mt-3">
                    {{-- Common / Role-specific navigation --}}
                    @if($user->role === 'admin')
                        {!! $navItem('Dashboard','home','dashboard.admin',['dashboard.admin']) !!}
                        <div class="mt-5 mb-2 pl-3 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-600 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Manajemen</div>
                        {!! $navItem('Pengguna','users','dashboard.pengguna',['dashboard.pengguna'],12) !!}
                        {!! $navItem('Departemen','layers','dashboard.departemen',['dashboard.departemen'],6,'bg-blue-500/10 text-blue-600 dark:text-blue-300 dark:bg-blue-400/10') !!}
                        {!! $navItem('Jenis Surat','tag','dashboard.jenis-surat',['dashboard.jenis-surat'],14,'bg-purple-500/10 text-purple-600 dark:text-purple-300 dark:bg-purple-400/10') !!}
                        {!! $navItem('Monitoring Sistem','activity','dashboard.monitoring',['dashboard.monitoring'],null) !!}
                    @elseif($user->role === 'rektorat')
                        {!! $navItem('Dashboard','home','dashboard.rektorat',['dashboard.rektorat']) !!}
                        <div class="mt-5 mb-2 pl-3 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-600 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Surat & Tugas</div>
                        {!! $navItem('Surat Masuk','inbox','surat.masuk',['surat.masuk'],$counts['surat_masuk'] ?? null) !!}
                        {!! $navItem('Surat Tugas','file-text','surat.tugas',['surat.tugas'],$counts['surat_tugas'] ?? null,'bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 dark:bg-indigo-400/10') !!}
                        {!! $navItem('Buat / Tindaklanjuti ST','edit','tindaklanjut.surat.tugas',['tindaklanjut.surat.tugas']) !!}
                        {!! $navItem('History Disposisi','clock','history.disposisi',['history.disposisi'],$counts['history_disposisi'] ?? null,'bg-amber-500/10 text-amber-600 dark:text-amber-300 dark:bg-amber-400/10') !!}
                        {!! $navItem('Arsip Surat Tugas','archive','arsip.surat.tugas',['arsip.surat.tugas'],$counts['arsip_surat_tugas'] ?? null,'bg-gray-500/10 text-gray-600 dark:text-gray-300 dark:bg-gray-500/20') !!}
                    @elseif($user->role === 'unit_kerja')
                        {!! $navItem('Dashboard','home','dashboard.unit_kerja',['dashboard.unit_kerja']) !!}
                        <div class="mt-5 mb-2 pl-3 text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-600 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Surat</div>
                        {!! $navItem('Surat Masuk','inbox','unit_kerja.surat.masuk',['unit_kerja.surat.masuk']) !!}
                        {!! $navItem('Inbox Disposisi','send','unit_kerja.inbox.disposisi',['unit_kerja.inbox.disposisi'],null,'bg-indigo-500/10 text-indigo-600 dark:text-indigo-300 dark:bg-indigo-400/10') !!}
                        {!! $navItem('Buat Surat','file-plus','unit_kerja.buat.surat',['unit_kerja.buat.surat']) !!}
                        {!! $navItem('Arsip Surat Tugas','archive','unit_kerja.arsip.surat.tugas',['unit_kerja.arsip.surat.tugas'],null,'bg-gray-500/10 text-gray-600 dark:text-gray-300 dark:bg-gray-500/20') !!}
                    @endif
                </ul>
            </div>
        </div>

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
