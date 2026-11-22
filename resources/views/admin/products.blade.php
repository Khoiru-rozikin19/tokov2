@extends('layouts.app', ['title' => 'Manajemen Produk'])

@section('content')
<div class="space-y-6 sm:space-y-8">
    
    @if(!$selectedCategory)
        <!-- ===================================================== -->
        <!-- CATEGORY CARDS VIEW (INITIAL /admin/products PAGE)   -->
        <!-- ===================================================== -->
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5 gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Kategori Produk Saya</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Pilih kategori di bawah ini untuk melihat dan mengelola produk sesuai jenis layanannya.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if(auth()->user()->role === 'admin')
                    <form action="{{ route('admin.products.sync_okeconnect') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-lg shadow-amber-500/20 active:scale-95 transition-all duration-200" title="Sinkronkan status Open/Close sekaligus Harga Modal produk dari OKEConnect">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            <span>Sinkron Modal Supplier</span>
                        </button>
                    </form>
                    <button onclick="toggleManageCategoriesModal(true)" class="flex items-center space-x-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl text-xs sm:text-sm font-bold shadow-sm active:scale-95 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"></path></svg>
                        <span>Kelola Kategori</span>
                    </button>
                    @endif
                    <button onclick="openCategoryCreateModal()" class="flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        <span>Tambah Produk</span>
                    </button>
                </div>
            </div>

            <!-- Category Rectangular Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @forelse($categories as $category)
                    @php
                        $cType = $category->type;
                    @endphp
                    <a href="{{ route('admin.products', ['category_id' => $category->id]) }}" class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-4 sm:p-5 flex items-center justify-between hover:border-blue-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 shadow-sm relative overflow-hidden">
                        <!-- Left Info -->
                        <div class="flex-1 min-w-0 pr-4">
                            <div class="mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400">
                                    {{ $category->products_count ?? 0 }} Produk
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $category->name }}
                            </h3>

                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">
                                @if($cType === 'kuota_xl')
                                    Paket kuota & pulsa otomatis OKEConnect
                                @elseif($cType === 'pln')
                                    Token listrik PLN prabayar (20 digit SN)
                                @elseif($cType === 'digital')
                                    Otomatisasi server VPS SSH & stok akun
                                @else
                                    Produk dan layanan digital toko
                                @endif
                            </p>

                            <span class="inline-flex items-center space-x-1 text-xs font-bold text-blue-600 dark:text-blue-400 mt-3">
                                <span>Kelola Produk</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                                </svg>
                            </span>
                        </div>

                        <!-- Right Image / Themed Icon Box -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center relative">
                            @if($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}"
                                     alt="{{ $category->name }}"
                                     class="max-w-full max-h-full object-contain"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full bg-blue-50 dark:bg-blue-950/20 items-center justify-center hidden">
                                    @if($cType === 'pln')
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-amber-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path></svg>
                                    @elseif($cType === 'kuota_xl')
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-indigo-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"></path></svg>
                                    @elseif($cType === 'digital')
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-13.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-16.5 0a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a3 3 0 013-3h13.5a3 3 0 013 3M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"></path></svg>
                                    @else
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    @endif
                                </div>
                            @else
                                <div class="w-full h-full bg-blue-50 dark:bg-blue-950/20 flex items-center justify-center">
                                    @if($cType === 'pln')
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-amber-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path></svg>
                                    @elseif($cType === 'kuota_xl')
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-indigo-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"></path></svg>
                                    @elseif($cType === 'digital')
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-13.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-16.5 0a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a3 3 0 013-3h13.5a3 3 0 013 3M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"></path></svg>
                                    @else
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500/80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum Ada Kategori</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto">Klik tombol "Kelola Kategori" untuk menambahkan kategori baru seperti Kuota XL, TOKEN PLN, atau Digital.</p>
                    </div>
                @endforelse

                @if($uncategorizedCount > 0)
                    <!-- Uncategorized Products Card -->
                    <a href="{{ route('admin.products', ['category_id' => 'uncategorized']) }}" class="group bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-4 sm:p-5 flex items-center justify-between hover:border-blue-500 hover:shadow-lg transition-all duration-300 shadow-sm">
                        <div class="flex-1 min-w-0 pr-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 mb-2">
                                {{ $uncategorizedCount }} Produk
                            </span>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 transition-colors">
                                Tanpa Kategori
                            </h3>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Produk yang belum dihubungkan ke kategori manapun</p>
                            <span class="inline-flex items-center space-x-1 text-xs font-bold text-blue-600 dark:text-blue-400 mt-3">
                                <span>Atur Kategori Produk</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
                            </span>
                        </div>
                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </a>
                @endif
            </div>
        </div>

    @else
        <!-- ===================================================== -->
        <!-- PRODUCTS MANAGEMENT VIEW FOR SELECTED CATEGORY        -->
        <!-- ===================================================== -->
        @php
            $catType = $selectedCategory->type ?? 'general';
        @endphp

        <!-- Selected Category Header & Breadcrumb -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Left: Back Button & Title -->
                <div class="flex items-center space-x-3.5 min-w-0">
                    <a href="{{ route('admin.products') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
                        <span>Kategori</span>
                    </a>

                    <div class="min-w-0">
                        <div class="flex items-center space-x-2">
                            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 truncate">
                                {{ $selectedCategory->name }}
                            </h2>
                            @if($catType === 'kuota_xl')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900/40">
                                    Kuota XL
                                </span>
                            @elseif($catType === 'pln')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">
                                    TOKEN PLN
                                </span>
                            @elseif($catType === 'digital')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40">
                                    Digital & VPN
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $products->count() }} produk terdaftar di kategori ini</p>
                    </div>
                </div>

                <!-- Right: Category Specific Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    @if($catType === 'kuota_xl')
                        @if(auth()->user()->role === 'admin' && Route::has('admin.tools.xl.index'))
                        <a href="{{ route('admin.tools.xl.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-2xl text-xs font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"></path></svg>
                            <span>Buka Tools XL</span>
                        </a>
                        @endif
                    @elseif($catType === 'digital')
                        @if(Route::has('admin.account_stocks'))
                        <a href="{{ route('admin.account_stocks') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path></svg>
                            <span>Kelola Stok Akun</span>
                        </a>
                        @endif
                        @if(auth()->user()->role === 'admin' && Route::has('admin.vpn_panel'))
                        <a href="{{ route('admin.vpn_panel') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl text-xs font-bold transition">
                            <span>VPN Panel</span>
                        </a>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'admin' && in_array($catType, ['kuota_xl', 'pln']))
                    <form action="{{ route('admin.products.sync_okeconnect') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center space-x-1.5 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-2xl text-xs font-bold transition" title="Sinkronkan harga modal & status OKEConnect">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                            <span>Sinkron Modal</span>
                        </button>
                    </form>
                    @endif

                    <!-- Tambah Produk Button -->
                    <button onclick="openCategoryCreateModal()" class="flex items-center space-x-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        <span>Tambah Produk</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats summary cards for this category -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Total Produk</span>
                <span class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1 block">{{ $products->count() }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Total Stok</span>
                <span class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1 block">{{ $products->sum('stock') }}</span>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Status Open</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">
                    {{ $products->filter(fn($p) => ($p->status ?? 'open') === 'open')->count() }}
                </span>
            </div>
            @if(auth()->user()->role === 'admin')
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Rata-rata Laba</span>
                @php
                    $avgProfit = $products->count() > 0 ? (int) $products->avg(fn($p) => $p->price - ($p->harga_modal ?? 0)) : 0;
                @endphp
                <span class="text-2xl font-black {{ $avgProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600' }} mt-1 block">
                    Rp {{ number_format(max(0, $avgProfit), 0, ',', '.') }}
                </span>
            </div>
            @endif
        </div>

        <!-- Desktop Products Table -->
        <div class="hidden md:block bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Nama Produk</th>
                            @if(in_array($catType, ['kuota_xl', 'pln']))
                                <th class="py-3 px-4">Kode Supplier</th>
                            @endif
                            @if(auth()->user()->role === 'admin')
                                <th class="py-3 px-4">Seller</th>
                            @endif
                            <th class="py-3 px-4">Harga & Modal / Laba</th>
                            @if($catType !== 'pln')
                                <th class="py-3 px-4">Masa Aktif</th>
                            @endif
                            <th class="py-3 px-4">Stok</th>
                            @if($catType === 'digital')
                                <th class="py-3 px-4">Otomatisasi / Server</th>
                            @elseif(in_array($catType, ['kuota_xl', 'pln']))
                                <th class="py-3 px-4">Status Supplier</th>
                            @endif
                            <th class="py-3 px-4">Visibilitas</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm font-medium text-slate-700 dark:text-slate-350">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all duration-150">
                                <td class="py-3 px-4 font-mono text-xs text-slate-400">#{{ $product->id }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-3">
                                        @if($product->image_path)
                                            <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-800 flex-shrink-0 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center relative">
                                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="max-w-full max-h-full object-contain"
                                                     onerror="this.style.display='none';">
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $product->name }}</span>
                                            @if($product->description)
                                                <span class="block text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs" title="{{ $product->description }}">{{ $product->description }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                @if(in_array($catType, ['kuota_xl', 'pln']))
                                    <td class="py-3 px-4">
                                        @if($product->orderkuota_product_code)
                                            <code class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 font-mono text-xs font-bold text-blue-600 dark:text-blue-400 select-all">
                                                {{ $product->orderkuota_product_code }}
                                            </code>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(auth()->user()->role === 'admin')
                                    <td class="py-3 px-4">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $product->seller ? $product->seller->name : 'Admin Utama' }}
                                        </span>
                                    </td>
                                @endif

                                <td class="py-3 px-4">
                                    <div class="text-xs">
                                        <span class="font-extrabold text-slate-800 dark:text-slate-100 text-sm block">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @if(auth()->user()->role === 'admin')
                                            @php
                                                $modal = (int) ($product->harga_modal ?? 0);
                                                $profit = (int) $product->price - $modal;
                                            @endphp
                                            <span class="text-slate-400 block mt-0.5">Modal: Rp {{ number_format($modal, 0, ',', '.') }}</span>
                                            <span class="font-bold {{ $profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600' }}">
                                                Laba: {{ $profit >= 0 ? '+' : '' }}Rp {{ number_format($profit, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                @if($catType !== 'pln')
                                    <td class="py-3 px-4 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        {{ $product->duration_days }} Hari
                                    </td>
                                @endif

                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $product->stock > 0 ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>

                                @if($catType === 'digital')
                                    <td class="py-3 px-4 text-xs">
                                        @if($product->vpsServer)
                                            <span class="font-mono font-semibold text-blue-600 dark:text-blue-400 block">{{ $product->vpsServer->name }}</span>
                                            <span class="text-[10px] text-slate-400">Otomatisasi SSH</span>
                                        @elseif($product->stocks && $product->stocks->where('status', 'ready')->isNotEmpty())
                                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 block">Stok Akun Dinamis</span>
                                            <span class="text-[10px] text-slate-400">{{ $product->stocks->where('status', 'ready')->count() }} akun ready</span>
                                        @else
                                            <span class="text-slate-400">Manual / Template</span>
                                        @endif
                                    </td>
                                @elseif(in_array($catType, ['kuota_xl', 'pln']))
                                    <td class="py-3 px-4 text-xs">
                                        @if(($product->status ?? 'open') === 'open')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                                OPEN
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                                                PRE-ORDER
                                            </span>
                                        @endif
                                    </td>
                                @endif

                                <td class="py-3 px-4 text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-400 font-semibold text-[10px]">
                                        {{ $product->visibility ?? 'all' }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button onclick="openEditModal({{ json_encode($product) }})" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/20 text-slate-600 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 rounded-xl transition-all duration-200" title="Edit Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini? Semua data pesanan terkait akan terhapus.')" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/20 text-slate-600 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 rounded-xl transition-all duration-200" title="Hapus Produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400 text-sm">
                                    Belum ada produk di kategori <strong>{{ $selectedCategory->name }}</strong>. Klik tombol <strong>+ Tambah Produk</strong> untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Products Cards -->
        <div class="space-y-3.5 md:hidden">
            @forelse($products as $product)
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 space-y-3 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs text-slate-400">#{{ $product->id }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->stock > 0 ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400' }}">
                            Stok: {{ $product->stock }}
                        </span>
                    </div>

                    <div class="flex items-start space-x-3">
                        @if($product->image_path)
                            <div class="w-12 h-12 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm truncate">{{ $product->name }}</h4>
                            @if($product->orderkuota_product_code)
                                <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 block mt-0.5">Supplier: {{ $product->orderkuota_product_code }}</span>
                            @endif
                            <span class="font-extrabold text-base text-slate-800 dark:text-slate-100 block mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800/80 pt-2.5 flex items-center justify-between text-xs">
                        @if(auth()->user()->role === 'admin')
                            @php
                                $mVal = (int) ($product->harga_modal ?? 0);
                                $eProf = (int) $product->price - $mVal;
                            @endphp
                            <span class="font-bold {{ $eProf >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Laba: {{ $eProf >= 0 ? '+' : '' }}Rp {{ number_format($eProf, 0, ',', '.') }}
                            </span>
                        @else
                            <span></span>
                        @endif
                        <div class="flex items-center space-x-2">
                            <button onclick="openEditModal({{ json_encode($product) }})" class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900/30 rounded-xl text-xs font-bold">
                                Edit
                            </button>
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30 rounded-xl text-xs font-bold">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 text-center text-slate-400 text-sm">
                    Belum ada produk di kategori ini.
                </div>
            @endforelse
        </div>
    @endif
</div>

<!-- ===================================================== -->
<!-- CREATE PRODUCT MODAL (TAILORED PER CATEGORY TYPE)     -->
<!-- ===================================================== -->
<div id="createProductModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative max-h-[calc(100vh-2rem)] overflow-y-auto">
        <button onclick="toggleCreateModal(false)" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="mb-5">
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight" id="createModalHeaderTitle">Tambah Produk Baru</h3>
            <p class="text-xs text-slate-400 mt-1">Lengkapi informasi produk, kode supplier, atau server VPS di bawah ini.</p>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Kategori Produk -->
            <div>
                <label for="create_category_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Kategori Produk</label>
                <select id="create_category_id" name="category_id" onchange="adaptFormToCategoryType('create', this.value)" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <option value="">-- Tanpa Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nama Produk -->
            <div>
                <label for="create_name" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Produk / Layanan</label>
                <input type="text" id="create_name" name="name" required placeholder="Contoh: Paket XL XBD 38GB atau Token PLN 20.000" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Kode Produk Supplier (H2H - Kuota XL & Token PLN) -->
            <div id="create_group_supplier">
                <label for="create_orderkuota_product_code" class="block text-xs font-bold text-slate-500 uppercase mb-2">Kode Produk Supplier (OKEConnect)</label>
                <div class="flex gap-2">
                    <input type="text" id="create_orderkuota_product_code" name="orderkuota_product_code" placeholder="Contoh: XBD38 atau PLN20" class="flex-1 px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <button type="button" id="btn_lookup_create" onclick="lookupOkeconnectCode('create')" class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold whitespace-nowrap shadow-md shadow-emerald-500/20 active:scale-95 transition-all">
                        🔍 Ambil Modal
                    </button>
                </div>
                <div id="create_okeconnect_info" class="hidden mt-2 p-3 rounded-xl text-xs font-semibold"></div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Sistem otomatis mendeteksi status open/close dan mengisi harga modal asli dari OKEConnect.</p>
            </div>

            <!-- Harga Jual & Harga Modal & Masa Aktif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="create_price" class="block text-xs font-bold text-slate-500 uppercase mb-2">Harga Jual ke Pembeli (Rp)</label>
                    <input type="number" id="create_price" name="price" required oninput="calculateLiveProfit('create')" placeholder="21000" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                </div>

                @if(auth()->user()->role === 'admin')
                <div>
                    <label for="create_harga_modal" class="block text-xs font-bold text-slate-500 uppercase mb-2">Harga Modal Supplier (Rp)</label>
                    <input type="number" id="create_harga_modal" name="harga_modal" oninput="calculateLiveProfit('create')" placeholder="20000" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                </div>
                @endif
            </div>

            <!-- Live Profit Badge -->
            <div id="create_profit_box" class="hidden p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs font-bold">
                <span class="text-slate-400">Estimasi Keuntungan:</span>
                <span id="create_profit_text" class="text-emerald-600 dark:text-emerald-400">Rp 0</span>
            </div>

            <!-- Masa Aktif (Khusus Kuota XL & Digital) -->
            <div id="create_group_duration">
                <label for="create_duration" class="block text-xs font-bold text-slate-500 uppercase mb-2">Masa Aktif (Hari)</label>
                <input type="number" id="create_duration" name="duration_days" value="30" placeholder="30" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Otomatisasi VPS (Khusus Digital / VPN) -->
            <div id="create_group_vps" class="space-y-4">
                @if(auth()->user()->role === 'admin')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="create_vps_server_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Otomatisasi Server VPS</label>
                        <select id="create_vps_server_id" name="vps_server_id" onchange="toggleVpsCommandInput('create')" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                            <option value="">-- Tanpa Otomatisasi SSH --</option>
                            @foreach($vpsServers as $server)
                                <option value="{{ $server->id }}">{{ $server->name }} ({{ $server->ip_address }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="create_vps_command_container" class="hidden">
                        <label for="create_vps_command_template" class="block text-xs font-bold text-slate-500 uppercase mb-2">Template Perintah CLI</label>
                        <input type="text" id="create_vps_command_template" name="vps_command_template" placeholder="Contoh: user-add-xray {username} {duration}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    </div>
                </div>
                @endif
            </div>

            <!-- Stok (Jumlah Stok Numerik) -->
            <div>
                <label for="create_stock" class="block text-xs font-bold text-slate-500 uppercase mb-2">Jumlah Stok (Statis)</label>
                <input type="number" id="create_stock" name="stock" value="999" placeholder="999" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                <p class="text-[10px] text-slate-400 mt-1">Untuk produk H2H otomatis, biarkan angka banyak (misal: 999) agar selalu siap dipesan pembeli.</p>
            </div>

            <!-- Foto Produk -->
            <div>
                <label for="create_image" class="block text-xs font-bold text-slate-500 uppercase mb-2">Foto / Gambar Produk (Opsional)</label>
                <input type="file" id="create_image" name="image" accept="image/*" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Deskripsi Produk -->
            <div>
                <label for="create_description" class="block text-xs font-bold text-slate-500 uppercase mb-2">Deskripsi Produk (Tampil di Katalog)</label>
                <textarea id="create_description" name="description" rows="2" placeholder="Contoh: Kuota utama 38GB masa aktif 30 hari..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200"></textarea>
            </div>

            <!-- Petunjuk Sukses -->
            <div>
                <label for="create_success_instruction" class="block text-xs font-bold text-slate-500 uppercase mb-2">Petunjuk Pembayaran Sukses</label>
                <textarea id="create_success_instruction" name="success_instruction" rows="2" placeholder="Petunjuk untuk pembeli setelah transaksi berhasil..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200"></textarea>
            </div>

            <!-- Visibilitas & Seller -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="create_visibility" class="block text-xs font-bold text-slate-500 uppercase mb-2">Visibilitas</label>
                    <select id="create_visibility" name="visibility" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                        <option value="all" selected>👥 Semua User</option>
                        <option value="admin_seller">🏪 Admin & Seller</option>
                        <option value="admin_only">🔒 Admin Only</option>
                    </select>
                </div>

                @if(auth()->user()->role === 'admin')
                <div>
                    <label for="create_user_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Pemilik Produk</label>
                    <select id="create_user_id" name="user_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ $seller->id == auth()->id() ? 'selected' : '' }}>
                                {{ $seller->name }} ({{ ucfirst($seller->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="toggleCreateModal(false)" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl text-sm font-semibold transition-all duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 transition-all duration-200">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===================================================== -->
<!-- EDIT PRODUCT MODAL (TAILORED PER CATEGORY TYPE)       -->
<!-- ===================================================== -->
<div id="editProductModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative max-h-[calc(100vh-2rem)] overflow-y-auto">
        <button onclick="toggleEditModal(false)" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight mb-5">Ubah Produk</h3>

        <form id="editForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Kategori -->
            <div>
                <label for="edit_category_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Kategori Produk</label>
                <select id="edit_category_id" name="category_id" onchange="adaptFormToCategoryType('edit', this.value)" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <option value="">-- Tanpa Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nama Produk -->
            <div>
                <label for="edit_name" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Produk / Layanan</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Kode Produk Supplier (H2H) -->
            <div id="edit_group_supplier">
                <label for="edit_orderkuota_product_code" class="block text-xs font-bold text-slate-500 uppercase mb-2">Kode Produk Supplier (OKEConnect)</label>
                <div class="flex gap-2">
                    <input type="text" id="edit_orderkuota_product_code" name="orderkuota_product_code" class="flex-1 px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <button type="button" id="btn_lookup_edit" onclick="lookupOkeconnectCode('edit')" class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold whitespace-nowrap shadow-md shadow-emerald-500/20 active:scale-95 transition-all">
                        🔍 Ambil Modal
                    </button>
                </div>
                <div id="edit_okeconnect_info" class="hidden mt-2 p-3 rounded-xl text-xs font-semibold"></div>
            </div>

            <!-- Harga Jual & Harga Modal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_price" class="block text-xs font-bold text-slate-500 uppercase mb-2">Harga Jual (Rp)</label>
                    <input type="number" id="edit_price" name="price" required oninput="calculateLiveProfit('edit')" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                </div>

                @if(auth()->user()->role === 'admin')
                <div>
                    <label for="edit_harga_modal" class="block text-xs font-bold text-slate-500 uppercase mb-2">Harga Modal (Rp)</label>
                    <input type="number" id="edit_harga_modal" name="harga_modal" oninput="calculateLiveProfit('edit')" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                </div>
                @endif
            </div>

            <!-- Live Profit Badge -->
            <div id="edit_profit_box" class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs font-bold">
                <span class="text-slate-400">Estimasi Keuntungan:</span>
                <span id="edit_profit_text" class="text-emerald-600 dark:text-emerald-400">Rp 0</span>
            </div>

            <!-- Masa Aktif -->
            <div id="edit_group_duration">
                <label for="edit_duration" class="block text-xs font-bold text-slate-500 uppercase mb-2">Masa Aktif (Hari)</label>
                <input type="number" id="edit_duration" name="duration_days" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Otomatisasi VPS (Digital) -->
            <div id="edit_group_vps" class="space-y-4">
                @if(auth()->user()->role === 'admin')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_vps_server_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Otomatisasi VPS</label>
                        <select id="edit_vps_server_id" name="vps_server_id" onchange="toggleVpsCommandInput('edit')" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                            <option value="">-- Tanpa Otomatisasi --</option>
                            @foreach($vpsServers as $server)
                                <option value="{{ $server->id }}">{{ $server->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="edit_vps_command_container" class="hidden">
                        <label for="edit_vps_command_template" class="block text-xs font-bold text-slate-500 uppercase mb-2">Template Perintah CLI</label>
                        <input type="text" id="edit_vps_command_template" name="vps_command_template" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    </div>
                </div>
                @endif
            </div>

            <!-- Stok -->
            <div>
                <label for="edit_stock" class="block text-xs font-bold text-slate-500 uppercase mb-2">Jumlah Stok</label>
                <input type="number" id="edit_stock" name="stock" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Foto Produk -->
            <div>
                <label for="edit_image" class="block text-xs font-bold text-slate-500 uppercase mb-2">Foto Produk</label>
                <div class="flex items-center space-x-3 mb-2">
                    <div id="edit_image_preview_container" class="hidden w-12 h-12 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 flex-shrink-0 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center">
                        <img id="edit_image_preview" src="" alt="Preview" class="max-w-full max-h-full object-contain">
                    </div>
                    <input type="file" id="edit_image" name="image" accept="image/*" class="flex-1 px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-xs font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="edit_description" class="block text-xs font-bold text-slate-500 uppercase mb-2">Deskripsi Produk</label>
                <textarea id="edit_description" name="description" rows="2" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200"></textarea>
            </div>

            <!-- Petunjuk Sukses -->
            <div>
                <label for="edit_success_instruction" class="block text-xs font-bold text-slate-500 uppercase mb-2">Petunjuk Pembayaran Sukses</label>
                <textarea id="edit_success_instruction" name="success_instruction" rows="2" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200"></textarea>
            </div>

            <!-- Visibilitas & Seller -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_visibility" class="block text-xs font-bold text-slate-500 uppercase mb-2">Visibilitas</label>
                    <select id="edit_visibility" name="visibility" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                        <option value="all">👥 Semua User</option>
                        <option value="admin_seller">🏪 Admin & Seller</option>
                        <option value="admin_only">🔒 Admin Only</option>
                    </select>
                </div>

                @if(auth()->user()->role === 'admin')
                <div>
                    <label for="edit_user_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Pemilik Produk</label>
                    <select id="edit_user_id" name="user_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}">
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="toggleEditModal(false)" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl text-sm font-semibold transition-all duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 transition-all duration-200">
                    Perbarui Produk
                </button>
            </div>
        </form>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<!-- ===================================================== -->
<!-- MANAGE CATEGORIES MODAL (CREATE, EDIT, REORDER)      -->
<!-- ===================================================== -->
<div id="manageCategoriesModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative max-h-[calc(100vh-2rem)] overflow-y-auto font-sans">
        <button onclick="toggleManageCategoriesModal(false)" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight mb-6">Kelola Kategori</h3>

        <div class="space-y-6">
            <!-- Form Tambah Kategori -->
            <div class="space-y-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-800">
                <label for="modalCategoryName" class="block text-xs font-bold text-slate-500 uppercase">Tambah Kategori Baru</label>
                <div class="space-y-2.5">
                    <input type="text" id="modalCategoryName" placeholder="Nama Kategori (misal: TOKEN PLN)..." class="w-full px-4 py-3 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    
                    <select id="modalCategoryType" class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:outline-none rounded-2xl text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <option value="">-- Tipe Kategori (Otomatis) --</option>
                        <option value="kuota_xl">📶 Kuota XL (H2H OKEConnect)</option>
                        <option value="pln">⚡ TOKEN PLN (Token Listrik Prabayar)</option>
                        <option value="digital">🌐 Digital (VPS SSH & Stok Akun)</option>
                        <option value="general">🏷️ Umum</option>
                    </select>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <div class="flex-1 min-w-0">
                            <label class="block text-[11px] font-semibold text-slate-400 mb-1">Gambar Kategori:</label>
                            <input type="file" id="modalCategoryImage" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        </div>
                        <button type="button" id="btnCreateCategory" onclick="createCategoryFromModal()" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200 flex-shrink-0">
                            + Tambah
                        </button>
                    </div>
                </div>
            </div>

            <!-- Daftar Kategori dengan Gambar, Hapus & Pengurutan -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Daftar Kategori</label>
                    <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">Geser item atau panah untuk mengurutkan</span>
                </div>
                @if($categories->count() > 0)
                <div id="categoriesSortableList" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @foreach($categories as $category)
                        <div class="flex items-center justify-between px-3.5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 category-item transition-all duration-200" data-id="{{ $category->id }}">
                            <div class="flex items-center min-w-0 flex-1 space-x-2.5">
                                <!-- Drag Handle -->
                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 flex-shrink-0 cursor-grab active:cursor-grabbing hover:text-slate-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9h.01M8 15h.01M12 9h.01M12 15h.01M16 9h.01M16 15h.01"></path></svg>

                                <!-- Category Image Thumbnail -->
                                <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 flex items-center justify-center flex-shrink-0">
                                    @if($category->image_path)
                                        <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="max-w-full max-h-full object-contain" onerror="this.style.display='none';">
                                    @else
                                        <svg class="w-5 h-5 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate block">
                                        {{ $category->name }}
                                    </span>
                                    <div class="mt-1">
                                        <select onchange="updateCategoryType({{ $category->id }}, this.value)" class="text-[10px] font-semibold bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2 py-0.5 text-slate-700 dark:text-slate-300 focus:outline-none focus:border-blue-500">
                                            <option value="kuota_xl" {{ $category->type === 'kuota_xl' ? 'selected' : '' }}>📶 Kuota XL</option>
                                            <option value="pln" {{ $category->type === 'pln' ? 'selected' : '' }}>⚡ TOKEN PLN</option>
                                            <option value="digital" {{ $category->type === 'digital' ? 'selected' : '' }}>🌐 Digital / VPN</option>
                                            <option value="general" {{ $category->type === 'general' ? 'selected' : '' }}>🏷️ Umum</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-1 flex-shrink-0">
                                <!-- Upload / Ubah Gambar Kategori -->
                                <label class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl text-[11px] font-bold cursor-pointer transition-all whitespace-nowrap" title="Atur / Ganti Gambar Kategori">
                                    <span>📷 Gambar</span>
                                    <input type="file" accept="image/*" class="hidden" onchange="uploadCategoryImage({{ $category->id }}, this)">
                                </label>
                                <!-- Move Up -->
                                <button type="button" onclick="moveCategoryUp({{ $category->id }}, this)" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 rounded-lg transition-all" title="Pindahkan ke atas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"></path></svg>
                                </button>
                                <!-- Move Down -->
                                <button type="button" onclick="moveCategoryDown({{ $category->id }}, this)" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 rounded-lg transition-all" title="Pindahkan ke bawah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path></svg>
                                </button>
                                <!-- Hapus -->
                                <button type="button" onclick="deleteCategory({{ $category->id }})" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-slate-400 hover:text-rose-500 rounded-xl transition-all duration-200" title="Hapus Kategori">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 text-center py-4">Belum ada kategori terdaftar.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // Current category context
    const currentCategoryId = "{{ $selectedCategory ? $selectedCategory->id : '' }}";
    const currentCategoryType = "{{ $selectedCategory ? ($selectedCategory->type ?? 'general') : 'general' }}";
    const currentCategoryName = "{{ $selectedCategory ? addslashes($selectedCategory->name) : '' }}";

    // Category types dictionary
    const categoryTypes = {
        @foreach($categories as $cat)
            '{{ $cat->id }}': '{{ $cat->type }}',
        @endforeach
    };

    // Open create modal adapted to category
    function openCategoryCreateModal() {
        const catId = currentCategoryId === 'uncategorized' ? '' : currentCategoryId;
        const catName = currentCategoryName || '';

        const catSelect = document.getElementById('create_category_id');
        if (catSelect) {
            catSelect.value = catId;
        }
        document.getElementById('createModalHeaderTitle').innerText = catId && catName ? ('Tambah Produk: ' + catName) : 'Tambah Produk Baru';

        adaptFormToCategoryType('create', catId);
        toggleCreateModal(true);
    }

    // Toggle form presets based on category type
    function adaptFormToCategoryType(prefix, categoryId) {
        let type = 'general';
        if (categoryId && categoryTypes[categoryId]) {
            type = categoryTypes[categoryId];
        } else if (currentCategoryType) {
            type = currentCategoryType;
        }

        const groupSupplier = document.getElementById(`${prefix}_group_supplier`);
        const groupVps = document.getElementById(`${prefix}_group_vps`);
        const groupDuration = document.getElementById(`${prefix}_group_duration`);
        const instructionField = document.getElementById(`${prefix}_success_instruction`);

        // VPS Automation, Supplier Code, and Duration are ALWAYS accessible for all categories
        if (groupVps) groupVps.classList.remove('hidden');
        if (groupSupplier) groupSupplier.classList.remove('hidden');
        if (groupDuration) groupDuration.classList.remove('hidden');

        // Preset for TOKEN PLN
        if (type === 'pln') {
            const durInput = document.getElementById(`${prefix}_duration`);
            if (durInput && prefix === 'create' && (!durInput.value || durInput.value === '30')) {
                durInput.value = '365';
            }
            if (instructionField && prefix === 'create' && !instructionField.value.trim()) {
                instructionField.value = 'Masukkan 20 digit nomor token listrik di atas ke meteran PLN Anda, lalu tekan tombol ENTER.';
            }
        }
    }

    // Live profit calculation
    function calculateLiveProfit(prefix) {
        const priceInput = document.getElementById(`${prefix}_price`);
        const modalInput = document.getElementById(`${prefix}_harga_modal`);
        const profitBox = document.getElementById(`${prefix}_profit_box`);
        const profitText = document.getElementById(`${prefix}_profit_text`);

        if (!priceInput || !profitBox || !profitText) return;

        const price = parseInt(priceInput.value) || 0;
        const modal = modalInput ? (parseInt(modalInput.value) || 0) : 0;

        if (price > 0 && modal > 0) {
            const profit = price - modal;
            const pct = modal > 0 ? ((profit / modal) * 100).toFixed(1) : 0;
            profitBox.classList.remove('hidden');

            if (profit >= 0) {
                profitText.className = 'text-emerald-600 dark:text-emerald-400 font-extrabold';
                profitText.innerText = `+Rp ${profit.toLocaleString('id-ID')} (+${pct}%)`;
            } else {
                profitText.className = 'text-rose-600 dark:text-rose-400 font-extrabold';
                profitText.innerText = `-Rp ${Math.abs(profit).toLocaleString('id-ID')} (${pct}%) RUGI`;
            }
        } else if (price > 0) {
            profitBox.classList.remove('hidden');
            profitText.className = 'text-blue-600 dark:text-blue-400 font-extrabold';
            profitText.innerText = `Rp ${price.toLocaleString('id-ID')} (Harga Jual)`;
        } else {
            profitBox.classList.add('hidden');
        }
    }

    // Modal controls
    function toggleCreateModal(show) {
        const modal = document.getElementById('createProductModal');
        if (show) {
            modal.classList.remove('hidden');
            calculateLiveProfit('create');
        } else {
            modal.classList.add('hidden');
        }
    }

    function openEditModal(product) {
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_duration').value = product.duration_days;
        document.getElementById('edit_stock').value = product.stock;
        document.getElementById('edit_category_id').value = product.category_id || '';
        
        adaptFormToCategoryType('edit', product.category_id);

        if (document.getElementById('edit_vps_server_id')) {
            document.getElementById('edit_vps_server_id').value = product.vps_server_id || '';
        }
        if (document.getElementById('edit_vps_command_template')) {
            document.getElementById('edit_vps_command_template').value = product.vps_command_template || '';
        }
        toggleVpsCommandInput('edit');

        if (document.getElementById('edit_harga_modal')) {
            document.getElementById('edit_harga_modal').value = product.harga_modal || 0;
        }
        if (document.getElementById('edit_orderkuota_product_code')) {
            document.getElementById('edit_orderkuota_product_code').value = product.orderkuota_product_code || '';
        }

        const editOkeInfo = document.getElementById('edit_okeconnect_info');
        if (editOkeInfo) {
            editOkeInfo.classList.add('hidden');
            editOkeInfo.innerHTML = '';
        }

        document.getElementById('edit_description').value = product.description || '';
        document.getElementById('edit_success_instruction').value = product.success_instruction || '';
        document.getElementById('edit_visibility').value = product.visibility || 'all';

        if (document.getElementById('edit_user_id')) {
            document.getElementById('edit_user_id').value = product.user_id;
        }
        
        const previewContainer = document.getElementById('edit_image_preview_container');
        const previewImg = document.getElementById('edit_image_preview');
        if (product.image_path) {
            previewImg.src = `/storage/${product.image_path}`;
            previewContainer.classList.remove('hidden');
        } else {
            previewImg.src = '';
            previewContainer.classList.add('hidden');
        }
        
        document.getElementById('editForm').action = `/admin/products/${product.id}/update`;
        calculateLiveProfit('edit');
        toggleEditModal(true);
    }

    function toggleEditModal(show) {
        const modal = document.getElementById('editProductModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function toggleVpsCommandInput(prefix) {
        const select = document.getElementById(`${prefix}_vps_server_id`);
        const container = document.getElementById(`${prefix}_vps_command_container`);
        const input = document.getElementById(`${prefix}_vps_command_template`);
        
        if (select && select.value !== '') {
            if (container) container.classList.remove('hidden');
            if (input) input.required = true;
        } else {
            if (container) container.classList.add('hidden');
            if (input) input.required = false;
            if (prefix === 'create' && input) {
                input.value = '';
            }
        }
    }

    let hasReordered = false;

    function toggleManageCategoriesModal(show) {
        const modal = document.getElementById('manageCategoriesModal');
        if (modal) {
            if (show) {
                modal.classList.remove('hidden');
                initDragAndDrop();
                setTimeout(() => {
                    document.getElementById('modalCategoryName')?.focus();
                }, 50);
            } else {
                modal.classList.add('hidden');
                if (hasReordered) {
                    window.location.reload();
                }
            }
        }
    }

    function initDragAndDrop() {
        const list = document.getElementById('categoriesSortableList');
        if (!list) return;

        let dragEl = null;

        list.querySelectorAll('.category-item').forEach(item => {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', function(e) {
                dragEl = this;
                e.dataTransfer.effectAllowed = 'move';
                this.classList.add('opacity-40', 'border-blue-500');
            });

            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });

            item.addEventListener('dragenter', function(e) {
                this.classList.add('bg-slate-100', 'dark:bg-slate-800');
            });

            item.addEventListener('dragleave', function(e) {
                this.classList.remove('bg-slate-100', 'dark:bg-slate-800');
            });

            item.addEventListener('drop', function(e) {
                e.stopPropagation();
                if (dragEl && dragEl !== this) {
                    const nodes = Array.from(list.children);
                    const sourceIdx = nodes.indexOf(dragEl);
                    const targetIdx = nodes.indexOf(this);

                    if (sourceIdx < targetIdx) {
                        list.insertBefore(dragEl, this.nextSibling);
                    } else {
                        list.insertBefore(dragEl, this);
                    }
                    saveCategoryOrder();
                }
            });

            item.addEventListener('dragend', function() {
                this.classList.remove('opacity-40', 'border-blue-500');
                list.querySelectorAll('.category-item').forEach(el => {
                    el.classList.remove('bg-slate-100', 'dark:bg-slate-800');
                });
            });
        });
    }

    function moveCategoryUp(id, btn) {
        const item = btn.closest('.category-item');
        const prev = item.previousElementSibling;
        if (prev && prev.classList.contains('category-item')) {
            item.parentNode.insertBefore(item, prev);
            saveCategoryOrder();
        }
    }

    function moveCategoryDown(id, btn) {
        const item = btn.closest('.category-item');
        const next = item.nextElementSibling;
        if (next && next.classList.contains('category-item')) {
            item.parentNode.insertBefore(next, item);
            saveCategoryOrder();
        }
    }

    function saveCategoryOrder() {
        const list = document.getElementById('categoriesSortableList');
        if (!list) return;

        const ids = Array.from(list.querySelectorAll('.category-item')).map(item => item.dataset.id);
        
        fetch("{{ route('admin.categories.reorder') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hasReordered = true;
            } else {
                alert('Gagal memperbarui urutan kategori.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi saat mengurutkan.');
        });
    }

    function escapeHtml(text) {
        return (text || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function createCategoryFromModal() {
        const input = document.getElementById('modalCategoryName');
        const typeSelect = document.getElementById('modalCategoryType');
        const imgInput = document.getElementById('modalCategoryImage');
        const btn = document.getElementById('btnCreateCategory');
        if (!input) return;
        const name = input.value.trim();
        if (!name) return;

        const formData = new FormData();
        formData.append('name', name);
        if (typeSelect && typeSelect.value) {
            formData.append('type', typeSelect.value);
        }
        if (imgInput && imgInput.files && imgInput.files[0]) {
            formData.append('image', imgInput.files[0]);
        }

        if (btn) btn.disabled = true;

        fetch("{{ route('admin.categories.store') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.success) {
                input.value = '';
                if (imgInput) imgInput.value = '';
                window.location.reload();
            } else {
                alert(data.message || 'Gagal menambahkan kategori.');
            }
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    function uploadCategoryImage(id, fileInput) {
        if (!fileInput || !fileInput.files || !fileInput.files[0]) return;
        const formData = new FormData();
        formData.append('image', fileInput.files[0]);

        fetch("{{ route('admin.categories.update', ':id') }}".replace(':id', id), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui gambar kategori.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat mengunggah gambar kategori.');
        });
    }

    function updateCategoryType(id, type) {
        fetch("{{ route('admin.categories.update', ':id') }}".replace(':id', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ type: type })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Gagal mengubah tipe kategori.');
            } else {
                hasReordered = true;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi saat mengubah tipe kategori.');
        });
    }

    function deleteCategory(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus kategori ini? Produk di kategori ini akan menjadi Tanpa Kategori.')) {
            return;
        }
        
        fetch("{{ route('admin.categories.delete', ':id') }}".replace(':id', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-HTTP-Method-Override': 'DELETE'
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                let message = 'Gagal menghapus kategori.';
                try {
                    const json = JSON.parse(text);
                    message = json.message || message;
                } catch(e) {}
                throw new Error(message + ` (HTTP ${res.status})`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = "{{ route('admin.products') }}";
            } else {
                alert(data.message || 'Gagal menghapus kategori.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan: ' + err.message);
        });
    }

    document.getElementById('modalCategoryName')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            createCategoryFromModal();
        }
    });

    function lookupOkeconnectCode(prefix) {
        const codeInput = document.getElementById(`${prefix}_orderkuota_product_code`);
        const modalInput = document.getElementById(`${prefix}_harga_modal`);
        const nameInput = document.getElementById(`${prefix}_name`);
        const infoBox = document.getElementById(`${prefix}_okeconnect_info`);
        const btn = document.getElementById(`btn_lookup_${prefix}`);

        if (!codeInput) return;
        const code = codeInput.value.trim();
        if (!code) {
            alert('Masukkan Kode Produk Supplier (OKEConnect) terlebih dahulu.');
            codeInput.focus();
            return;
        }

        const originalText = btn ? btn.innerHTML : '🔍 Ambil Modal';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '⏳ Mengecek...';
        }

        fetch(`{{ route('admin.products.lookup_okeconnect') }}?code=${encodeURIComponent(code)}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            if (data.success) {
                if (modalInput) {
                    modalInput.value = data.price;
                }
                if (nameInput && !nameInput.value.trim() && data.description) {
                    nameInput.value = data.description;
                }
                calculateLiveProfit(prefix);
                if (infoBox) {
                    const statusBadge = data.status === 'open'
                        ? '<span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold">OPEN</span>'
                        : '<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold">CLOSE</span>';
                    infoBox.className = 'mt-2 p-3 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 space-y-1';
                    infoBox.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span>✅ <strong>${escapeHtml(data.code)}</strong> ditemukan di OKEConnect</span>
                            ${statusBadge}
                        </div>
                        <div class="text-slate-600 dark:text-slate-300">${escapeHtml(data.description || '-')}</div>
                        <div class="font-extrabold text-emerald-700 dark:text-emerald-400">Harga Modal OKEConnect: ${escapeHtml(data.formatted_price)} (Otomatis Terisi)</div>
                    `;
                }
            } else {
                if (infoBox) {
                    infoBox.className = 'mt-2 p-3 rounded-xl text-xs font-semibold bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-400';
                    infoBox.innerHTML = `⚠️ ${escapeHtml(data.message || 'Kode tidak ditemukan di OKEConnect.')}`;
                } else {
                    alert(data.message || 'Kode tidak ditemukan di OKEConnect.');
                }
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
            alert('Gagal menghubungi server untuk mengecek kode OKEConnect.');
        });
    }
</script>
@endsection
