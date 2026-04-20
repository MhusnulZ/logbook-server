<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Book Digital Server Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 h-full w-sidebar bg-primary-main flex flex-col items-center py-6 gap-8 z-50 shadow-xl">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center text-white">
                <i data-lucide="layout-dashboard"></i>
            </div>
            <nav class="flex flex-col gap-4">
                <a href="{{ route('logs.index') }}" id="nav-dashboard" class="w-12 h-12 rounded-xl flex items-center justify-center transition-all {{ !(request()->has('filter') || request()->has('page')) ? 'bg-white text-primary-main shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i data-lucide="list"></i>
                </a>
                <a href="{{ route('logs.index') }}?filter=1" id="nav-history" class="w-12 h-12 rounded-xl flex items-center justify-center transition-all {{ (request()->has('filter') || request()->has('page')) ? 'bg-white text-primary-main shadow-lg' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i data-lucide="history"></i>
                </a>
            </nav>
        </aside>

        <!-- Main Viewport -->
        <main class="flex-1 ml-sidebar flex flex-col">
            <header class="h-header bg-white border-bottom border-slate-200 px-8 flex items-center justify-between sticky top-0 z-40 shadow-sm">
                <div>
                    <h1 id="page-title" class="text-xl font-bold text-slate-800">Log Book Digital Server Room</h1>
                    <p id="page-subtitle" class="text-xs text-slate-500">Pencatatan Akses Keluar/Masuk Ruang Server BBHIPMM</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-sm font-medium text-slate-600 bg-slate-100 px-4 py-1.5 rounded-full border border-slate-200">
                        <span id="current-date">-- --- ----</span> | 
                        <span id="current-time" class="text-primary-main font-bold">--.--</span>
                    </div>
                </div>
            </header>

            <div class="p-8 pb-20">
                @if(session('success'))
                    <div class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-xl mb-8 border border-emerald-200 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Dashboard View -->
                <div id="dashboard-view" class="{{ (request()->has('filter') || request()->has('page')) ? 'hidden' : 'block' }}">
                    <div class="flex flex-col gap-8">
                        <!-- Stats Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white p-6 rounded-premium shadow-sm border border-slate-200 flex flex-col gap-1 relative overflow-hidden group hover:shadow-md transition-shadow">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Akses Hari Ini</span>
                                <span id="total-access" class="text-3xl font-black text-slate-800">{{ $stats['totalToday'] }}</span>
                                <div class="absolute -right-2 -bottom-2 text-slate-50 opacity-10 group-hover:scale-110 transition-transform">
                                    <i data-lucide="bar-chart" class="w-16 h-16"></i>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-premium shadow-sm border border-slate-200 flex flex-col gap-1 relative overflow-hidden group hover:shadow-md transition-shadow">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Personel di Dalam</span>
                                <span id="personnel-inside" class="text-3xl font-black text-primary-main">{{ str_pad($stats['personnelInside'], 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="absolute -right-2 -bottom-2 text-slate-50 opacity-10 group-hover:scale-110 transition-transform">
                                    <i data-lucide="users" class="w-16 h-16"></i>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-premium shadow-sm border border-slate-200 flex flex-col gap-1 relative overflow-hidden group hover:shadow-md transition-shadow">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Keamanan</span>
                                <div class="flex items-center gap-2 text-emerald-500 font-extrabold mt-2">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    <span>TERJAGA</span>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-premium shadow-sm border border-slate-200 flex flex-col gap-1 relative overflow-hidden group hover:shadow-md transition-shadow">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Update Terakhir</span>
                                <div class="flex items-center gap-2 text-slate-700 font-bold mt-2 ">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 text-primary-main animate-spin-slow"></i>
                                    <span>Realtime</span>
                                </div>
                            </div>
                        </div>

                        <!-- Main Content Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Column 1: New Registration -->
                            <section class="bg-white rounded-premium shadow-sm border border-slate-200 p-8">
                                <div class="flex items-center gap-3 mb-8 border-b border-slate-100 pb-4">
                                    <div class="w-10 h-10 bg-primary-light text-primary-main rounded-xl flex items-center justify-center">
                                        <i data-lucide="user-plus"></i>
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-800">Pencatatan Baru</h2>
                                </div>
                                <form action="{{ route('logs.store') }}" method="POST" id="log-form" class="space-y-6">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label for="visitor-name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Pengunjung</label>
                                            <input type="text" name="visitor_name" id="visitor-name" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 focus:border-primary-main outline-none transition-all">
                                        </div>
                                        <div class="space-y-2">
                                            <label for="vendor-name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Perusahaan / Vendor</label>
                                            <input type="text" name="vendor_name" id="vendor-name" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 focus:border-primary-main outline-none transition-all">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label for="purpose" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tujuan / Keperluan</label>
                                            <select name="purpose" id="purpose" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 focus:border-primary-main outline-none transition-all">
                                                <option value="" disabled selected>Pilih Keperluan</option>
                                                <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                                                <option value="Perbaikan Darurat">Perbaikan Darurat</option>
                                                <option value="Pemasangan Perangkat">Pemasangan Perangkat</option>
                                                <option value="Audit Jaringan">Audit Jaringan</option>
                                                <option value="Inspeksi Keamanan">Inspeksi Keamanan</option>
                                                <option value="Pemantauan Kondisi Ruangan">Pemantauan Kondisi Ruangan</option>
                                                <option value="Kunjungan Tamu">Kunjungan Tamu</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="quantity" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Jumlah Orang</label>
                                            <div class="flex items-center gap-3">
                                                <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-20 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 focus:border-primary-main outline-none transition-all">
                                                <span class="text-sm font-medium text-slate-400">Personel</span>
                                            </div>
                                        </div>
                                    </div>

                                    <label class="flex items-center gap-3 cursor-pointer group bg-slate-50 p-3 rounded-lg border border-slate-100 hover:border-primary-main/30 transition-all">
                                        <input type="checkbox" id="manual-time-toggle" class="w-5 h-5 rounded border-slate-300 text-primary-main focus:ring-primary-main">
                                        <span class="text-sm font-bold text-primary-main/80 group-hover:text-primary-main transition-colors">Atur Waktu Secara Manual (Opsional)</span>
                                    </label>

                                    <!-- Manual Time Fields (Hidden by default) -->
                                    <div id="manual-time-fields" class="hidden animate-in fade-in zoom-in duration-200 space-y-4">
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="space-y-2">
                                                <label for="manual-date" class="text-[10px] font-black text-slate-400 uppercase">Tanggal</label>
                                                <input type="date" name="manual_date" id="manual-date" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="manual-time-in" class="text-[10px] font-black text-slate-400 uppercase">Jam Masuk</label>
                                                <input type="time" name="manual_time_in" id="manual-time-in" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="manual-time-out" class="text-[10px] font-black text-slate-400 uppercase">Jam Keluar</label>
                                                <input type="time" name="manual_time_out" id="manual-time-out" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="description" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Deskripsi Kerja / Detail</label>
                                        <textarea name="description" id="description" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 focus:border-primary-main outline-none transition-all placeholder:text-slate-300" placeholder="Detail pekerjaan..."></textarea>
                                    </div>

                                    <button type="submit" class="btn-primary flex items-center justify-center gap-3 w-full py-4 rounded-xl text-white font-bold transition-all hover:shadow-lg active:scale-[0.98]">
                                        <i data-lucide="user-plus" class="w-5 h-5"></i> SIMPAN LOG MASUK
                                    </button>
                                </form>
                            </section>

                            <!-- Column 2: Activity List -->
                            <section class="bg-white rounded-premium shadow-sm border border-slate-200 p-8">
                                <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center">
                                            <i data-lucide="history"></i>
                                        </div>
                                        <h2 class="text-lg font-bold text-slate-800">Aktivitas Terakhir</h2>
                                    </div>
                                    <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg font-black uppercase">Live SQL</span>
                                </div>
                                <div id="activity-list" class="flex flex-col gap-6">
                                    @forelse($dashboardLogs as $log)
                                        <div class="p-6 border border-slate-100 rounded-xl flex flex-col gap-4 relative group hover:border-primary-main/20 hover:bg-slate-50/50 transition-all">
                                            <div class="flex flex-wrap items-center gap-3 leading-none">
                                                <span class="font-bold text-slate-800">{{ $log->visitor_name }}</span>
                                                <div class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1.5">
                                                    <i data-lucide="users" class="w-3 h-3"></i>
                                                    {{ $log->quantity }} Personel
                                                </div>
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ $log->vendor_name }}</span>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase {{ $log->status == 'INSIDE' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                                                    {{ $log->status }}
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-300 ml-auto">{{ date('d M Y', strtotime($log->timestamp_in)) }}</span>
                                            </div>
                                            <div class="text-sm">
                                                <strong class="text-slate-700 block mb-1">{{ $log->purpose }}</strong>
                                                <p class="text-slate-400 italic text-xs leading-relaxed">"{{ $log->description }}"</p>
                                            </div>
                                            <div class="flex items-center gap-6 pt-3 border-t border-dashed border-slate-100">
                                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                    <span>MASUK: <span class="text-slate-700">{{ date('H:i', strtotime($log->timestamp_in)) }}</span></span>
                                                </div>
                                                @if($log->timestamp_out)
                                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                                                    <i data-lucide="log-out" class="w-3.5 h-3.5 text-primary-main"></i>
                                                    <span>KELUAR: <span class="text-primary-main">{{ date('H:i', strtotime($log->timestamp_out)) }}</span></span>
                                                </div>
                                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                                                    <i data-lucide="hourglass" class="w-3.5 h-3.5 text-amber-500"></i>
                                                    <span>DURASI: <span class="text-amber-600">{{ $log->duration }}</span></span>
                                                </div>
                                                @endif
                                            </div>
                                            @if($log->status == 'INSIDE')
                                            <form action="{{ route('logs.checkout', $log->id) }}" method="POST" class="absolute right-6 top-1/2 -translate-y-1/2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 border border-primary-main text-primary-main rounded-lg text-xs font-bold hover:bg-primary-main hover:text-white transition-all">
                                                    Checkout
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-10 text-slate-300">
                                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                                            <p class="text-sm font-medium">Belum ada aktivitas hari ini</p>
                                        </div>
                                    @endforelse
                                </div>
                            </section>
                        </div>
                    </div>
                </div>

                <!-- History View -->
                <div id="history-view" class="{{ (request()->has('filter') || request()->has('page')) ? 'block' : 'hidden' }}">
                    <div class="flex flex-col gap-8">
                        <!-- Export Buttons Above Filter -->
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('logs.export', request()->all()) }}" class="flex items-center gap-2 bg-white border border-emerald-200 px-6 py-2.5 rounded-lg text-emerald-600 hover:bg-emerald-50 font-black text-xs uppercase tracking-widest transition-all shadow-sm hover:shadow-md active:scale-95">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Ekspor Excel (XLSX)
                            </a>
                            <button id="btn-export-csv" class="flex items-center gap-2 bg-white border border-slate-200 px-6 py-2.5 rounded-lg text-primary-main hover:bg-slate-50 font-black text-xs uppercase tracking-widest transition-all shadow-sm hover:shadow-md active:scale-95">
                                <i data-lucide="download" class="w-4 h-4"></i> Ekspor CSV
                            </button>
                        </div>

                        <!-- Filter Bar -->
                        <div class="bg-white p-6 rounded-premium shadow-sm border border-slate-200">
                            <form action="{{ route('logs.index') }}" method="GET" id="filter-form" class="flex flex-wrap items-center gap-4">
                                <input type="hidden" name="filter" value="1">
                                <div class="flex-1 min-w-[300px] relative">
                                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                    <input type="text" name="search" id="filter-search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 outline-none transition-all text-sm" placeholder="Cari nama atau perusahaan...">
                                </div>
                                <div class="w-48 relative">
                                    <i data-lucide="filter" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                    <select name="purpose" id="filter-purpose" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 outline-none transition-all text-sm appearance-none">
                                        <option value="">Semua Tujuan</option>
                                        @foreach(['Pemeliharaan Rutin', 'Perbaikan Darurat', 'Pemasangan Perangkat', 'Audit Jaringan', 'Kunjungan Tamu', 'Lainnya'] as $p)
                                            <option value="{{ $p }}" {{ request('purpose') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-48 relative">
                                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                    <input type="date" name="date" id="filter-date" value="{{ request('date') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 outline-none transition-all text-sm">
                                </div>
                                <div class="w-32 relative">
                                    <i data-lucide="list-ordered" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                    <select name="per_page" id="filter-per-page" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-main/20 outline-none transition-all text-sm appearance-none">
                                        @foreach([10, 25, 50, 100] as $v)
                                            <option value="{{ $v }}" {{ request('per_page') == $v ? 'selected' : '' }}>{{ $v }} Data</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="bg-primary-main text-white px-8 py-2.5 rounded-lg font-bold text-sm hover:bg-primary-hover active:scale-95 transition-all shadow-md">
                                    CARI
                                </button>
                                <a href="{{ route('logs.index') }}?filter=1" class="bg-slate-100 text-slate-600 px-6 py-2.5 rounded-lg font-bold text-sm hover:bg-slate-200 transition-all">
                                    RESET
                                </a>
                            </form>
                        </div>

                        <!-- History Table -->
                        <div class="bg-white rounded-premium shadow-sm border border-slate-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/50 border-b border-slate-100">
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama & Instansi</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tujuan & Deskripsi</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Masuk</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Keluar</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Durasi</th>
                                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($logs as $log)
                                            <tr class="hover:bg-slate-50/30 transition-colors">
                                                <td class="px-8 py-5 text-xs font-bold text-slate-400">{{ date('Y-m-d', strtotime($log->timestamp_in)) }}</td>
                                                <td class="px-8 py-5">
                                                    <div class="font-bold text-slate-800">{{ $log->visitor_name }}</div>
                                                    <div class="text-[10px] font-black text-slate-400 uppercase leading-none mt-0.5">{{ $log->vendor_name }}</div>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[10px] font-black">{{ $log->quantity }}</span>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <div class="font-bold text-slate-700 text-sm leading-tight">{{ $log->purpose }}</div>
                                                    <div class="text-xs text-slate-400 italic mt-1 leading-normal line-clamp-1 max-w-[250px]">"{{ $log->description }}"</div>
                                                </td>
                                                <td class="px-8 py-5 font-bold text-slate-800 text-sm">{{ date('H:i', strtotime($log->timestamp_in)) }}</td>
                                                <td class="px-8 py-5 font-bold {{ $log->timestamp_out ? 'text-primary-main' : 'text-slate-300' }} text-sm">
                                                    {{ $log->timestamp_out ? date('H:i', strtotime($log->timestamp_out)) : '--:--' }}
                                                </td>
                                                <td class="px-8 py-5 font-bold text-amber-600 text-sm">
                                                    {{ $log->duration ?? '--' }}
                                                </td>
                                                <td class="px-8 py-5">
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase {{ $log->status == 'INSIDE' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                                                        {{ $log->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-8 py-20 text-center">
                                                    <i data-lucide="database" class="w-12 h-12 mx-auto mb-4 opacity-10 text-slate-900"></i>
                                                    <p class="text-sm font-bold text-slate-300">Tidak ada riwayat ditemukan</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Table Footer -->
                            <div class="bg-slate-50/50 px-8 py-6 border-t border-slate-100 italic">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold text-slate-400">Total: <span class="text-slate-700">{{ $logs->total() }} Data ditemukan</span></span>
                                </div>
                                
                                <!-- Pagination Links -->
                                <div class="pt-4 border-t border-slate-100">
                                    {{ $logs->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Setup Manual Time Toggle
            const manualToggle = document.getElementById('manual-time-toggle');
            const manualFields = document.getElementById('manual-time-fields');
            
            manualToggle.addEventListener('change', () => {
                if (manualToggle.checked) {
                    manualFields.classList.remove('hidden');
                    const now = new Date();
                    document.getElementById('manual-date').value = now.toISOString().split('T')[0];
                    document.getElementById('manual-time-in').value = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                } else {
                    manualFields.classList.add('hidden');
                }
            });

            // View Switching
            const dashView = document.getElementById('dashboard-view');
            const histView = document.getElementById('history-view');
            const navDash = document.getElementById('nav-dashboard');
            const navHist = document.getElementById('nav-history');

            const switchView = (view) => {
                const isHistory = view === 'history';
                dashView.classList.toggle('hidden', isHistory);
                histView.classList.toggle('hidden', !isHistory);
                
                // Sidebar state
                if (isHistory) {
                    navDash.classList.remove('bg-white', 'text-primary-main', 'shadow-lg');
                    navDash.classList.add('text-white/70', 'hover:bg-white/10', 'hover:text-white');
                    navHist.classList.add('bg-white', 'text-primary-main', 'shadow-lg');
                    navHist.classList.remove('text-white/70', 'hover:bg-white/10', 'hover:text-white');
                    document.getElementById('page-title').textContent = 'Data Riwayat Akses';
                    document.getElementById('page-subtitle').textContent = 'Arsip lengkap log aktivitas server room';
                } else {
                    navDash.classList.add('bg-white', 'text-primary-main', 'shadow-lg');
                    navDash.classList.remove('text-white/70', 'hover:bg-white/10', 'hover:text-white');
                    navHist.classList.remove('bg-white', 'text-primary-main', 'shadow-lg');
                    navHist.classList.add('text-white/70', 'hover:bg-white/10', 'hover:text-white');
                    document.getElementById('page-title').textContent = 'Log Book Digital Server Room';
                    document.getElementById('page-subtitle').textContent = 'Pencatatan Akses Keluar/Masuk Ruang Server BBHIPMM';
                }
            };

            navDash.addEventListener('click', (e) => { switchView('dashboard'); });
            navHist.addEventListener('click', (e) => { switchView('history'); });

            // Detect initial view from URL
            if (window.location.search.includes('filter=1') || window.location.search.includes('page=')) {
                switchView('history');
            }

            // Update time/date clock
            const updateHeaderMeta = () => {
                const now = new Date();
                const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit' });
                document.getElementById('current-date').textContent = dateStr;
                document.getElementById('current-time').textContent = timeStr;
            };
            updateHeaderMeta();
            setInterval(updateHeaderMeta, 60000);


            // CSV Export
            document.getElementById('btn-export-csv')?.addEventListener('click', () => {
                const table = document.querySelector('table');
                if (!table) return;
                const rows = Array.from(table.querySelectorAll('tr'));
                const csvContent = rows.map(row => {
                    const cells = Array.from(row.querySelectorAll('th, td'));
                    return cells.map(cell => `"${cell.textContent.trim()}"`).join(',');
                }).join('\n');

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.setAttribute("href", url);
                link.setAttribute("download", `logbook_export_${new Date().toISOString().split('T')[0]}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            // Auto-submit filters
            const filterForm = document.getElementById('filter-form');
            const filterInputs = filterForm?.querySelectorAll('input, select');
            
            filterInputs?.forEach(input => {
                input.addEventListener('change', () => {
                    filterForm.submit();
                });

                // For the search input, submit on Enter is default, 
                // but let's also trigger on a slight delay if they stop typing
                if (input.name === 'search') {
                    let timeout = null;
                    input.addEventListener('input', () => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            if (input.value.length >= 3 || input.value.length === 0) {
                                filterForm.submit();
                            }
                        }, 800);
                    });
                }
            });
        });
    </script>
</body>
</html>
