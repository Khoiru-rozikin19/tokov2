@extends('layouts.app', ['title' => 'Katalog Produk'])

@section('content')
<style>
    /* Custom scrollbar for VPN configurations */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    /* Progress card step animation */
    @keyframes progressFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .progress-step-animate {
        animation: progressFadeIn 0.4s ease-out forwards;
    }
    @keyframes progressPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .progress-status-pulse {
        animation: progressPulse 2s ease-in-out infinite;
    }
</style>
<div class="space-y-8">
    
    <!-- Catalog Header (Only shown if QRIS is not configured) -->
    @if(!$qris_configured)
    <div class="border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-xl text-amber-800 dark:text-amber-400 text-xs flex items-center space-x-2">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span><strong>Catatan:</strong> QRIS Statis belum dikonfigurasi oleh admin. Checkout tidak dapat diproses.</span>
        </div>
    </div>
    @endif

    @if(!request()->filled('category_id'))
        <!-- Category Rectangular Cards View (Initial /catalog Page) -->
        <div class="space-y-4">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Pilih Kategori Produk</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Pilih kategori di bawah ini untuk melihat daftar produk yang tersedia.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @forelse($categories as $category)
                    <a href="{{ route('catalog', ['category_id' => $category->id]) }}" class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-4 sm:p-5 flex items-center justify-between hover:border-blue-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 shadow-sm">
                        <!-- Left: Category Name & Info -->
                        <div class="flex-1 min-w-0 pr-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 mb-2">
                                {{ $category->visible_products_count ?? 0 }} Produk
                            </span>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $category->name }}
                            </h3>
                            <span class="inline-flex items-center space-x-1 text-xs font-bold text-blue-600 dark:text-blue-400 mt-3">
                                <span>Buka Kategori</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                                </svg>
                            </span>
                        </div>

                        <!-- Right: Category Image Box -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center relative">
                            @if($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}"
                                     alt="{{ $category->name }}"
                                     class="max-w-full max-h-full object-contain"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full bg-blue-50 dark:bg-blue-950/20 items-center justify-center hidden">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-full h-full bg-blue-50 dark:bg-blue-950/20 flex items-center justify-center">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum Ada Kategori Produk</h3>
                        <p class="text-xs text-slate-450 dark:text-slate-500 mt-1 max-w-sm mx-auto">Kategori produk belum dibuat untuk saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Selected Category Header + Back Button -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3 min-w-0">
                <a href="{{ route('catalog') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                    </svg>
                    <span>Kembali</span>
                </a>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 dark:text-slate-100 truncate">
                        {{ $selectedCategory->name ?? 'Kategori Produk' }}
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $products->count() }} produk ditemukan</p>
                </div>
            </div>

            @if($selectedCategory && $selectedCategory->image_path)
                <div class="w-12 h-12 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center flex-shrink-0 ml-3">
                    <img src="{{ asset('storage/' . $selectedCategory->image_path) }}"
                         alt="{{ $selectedCategory->name }}"
                         class="max-w-full max-h-full object-contain"
                         onerror="this.parentElement.style.display='none';">
                </div>
            @endif
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
        @forelse($products as $product)
            @php
                $finalPriceQris = $product->getFinalPriceForUser(auth()->user(), 'qris');
                $finalPriceBalance = $product->getFinalPriceForUser(auth()->user(), 'balance');
                $discountQris = $product->getDiscountAmountForUser(auth()->user(), 'qris');
                $discountBalance = $product->getDiscountAmountForUser(auth()->user(), 'balance');
                $hasDiscount = ($discountQris > 0 || $discountBalance > 0);
                $cardDiscountAmount = max($discountQris, $discountBalance);
                $cardFinalPrice = min($finalPriceQris, $finalPriceBalance);
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-3 sm:p-4 flex flex-col justify-between hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 shadow-sm">
                <div>
                    <!-- Badge Stock & Supplier Status -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-1.5">
                            @if($product->stock > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 space-x-1">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                    <span>{{ $product->stock }} stok</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 space-x-1">
                                    <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                    <span>Habis</span>
                                </span>
                            @endif

                            @if($hasDiscount)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50">
                                    Diskon
                                </span>
                            @endif
                        </div>

                        @if($product->orderkuota_product_code)
                            @if(($product->status ?? 'open') === 'open')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Open</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Pre-Order</span>
                                </span>
                            @endif
                        @endif
                    </div>

                    <!-- Package Icon Container or Product Image -->
                    @if($product->image_path)
                        <div class="w-full h-20 sm:h-24 mb-3 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 flex items-center justify-center relative">
                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="max-w-full max-h-full object-contain"
                                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-blue-50 dark:bg-blue-950/20 items-center justify-center hidden">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="w-full h-20 sm:h-24 bg-blue-50 dark:bg-blue-950/20 rounded-xl flex items-center justify-center mb-3 border border-blue-100/50 dark:border-blue-950/50">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- Title & Details -->
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug mb-1 line-clamp-2">{{ $product->name }}</h3>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mb-2 line-clamp-2 leading-relaxed">
                        @if($product->description)
                            {!! $product->description !!}
                        @else
                            Masa aktif: <strong>{{ $product->duration_days }} hari</strong>
                        @endif
                    </div>

                    <div class="flex items-center space-x-1 text-[10px] text-slate-400 dark:text-slate-500 mb-3">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="truncate">Seller: {{ $product->seller ? $product->seller->name : 'Admin Utama' }}</span>
                    </div>
                </div>

                <!-- Footer Card Info & Action -->
                <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex flex-col">
                        @if($hasDiscount)
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 line-through leading-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-sm sm:text-base font-extrabold text-emerald-600 dark:text-emerald-400 leading-tight">Rp {{ number_format($cardFinalPrice, 0, ',', '.') }}</span>
                        @else
                            <span class="text-sm sm:text-base font-extrabold text-blue-600 dark:text-blue-400">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="flex space-x-1.5">
                        <button data-description="{{ $product->description }}" onclick="openDetailModal('{{ addslashes($product->name) }}', '{{ $cardFinalPrice }}', '{{ $product->duration_days }}', this.getAttribute('data-description'), '{{ $product->price }}', '{{ $cardDiscountAmount }}')" class="px-2 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-[10px] font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
                            Detail
                        </button>
                        
                        @if($product->stock > 0 && (($product->status ?? 'open') === 'open' || !empty($product->orderkuota_product_code)) && $qris_configured)
                            @auth
                                <button onclick="openBuyModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->orderkuota_product_code }}', {{ ($product->status ?? 'open') === 'close' ? 'true' : 'false' }}, {{ $finalPriceQris }}, {{ $discountQris }}, {{ $finalPriceBalance }}, {{ $discountBalance }})" class="flex items-center space-x-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-750 active:scale-95 text-white rounded-lg text-[10px] font-bold transition-all duration-200 shadow-sm shadow-blue-500/10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>{{ ($product->status ?? 'open') === 'close' ? 'Pre-Order' : 'Beli' }}</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center space-x-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-750 active:scale-95 text-white rounded-lg text-[10px] font-bold transition-all duration-200 shadow-sm shadow-blue-500/10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>{{ ($product->status ?? 'open') === 'close' ? 'Pre-Order' : 'Beli' }}</span>
                                </a>
                            @endauth
                        @else
                            <button disabled class="flex items-center space-x-1 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 rounded-lg text-[10px] font-bold cursor-not-allowed">
                                <span>Tutup</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">
                    {{ request()->filled('category_id') ? 'Tidak Ada Produk Di Kategori Ini' : 'Katalog Produk Kosong' }}
                </h3>
                <p class="text-xs text-slate-450 dark:text-slate-500 mt-1 max-w-sm mx-auto">
                    {{ request()->filled('category_id') ? 'Silakan pilih kategori produk lain atau kembali ke katalog semua.' : 'Belum ada produk aktif yang tersedia untuk saat ini.' }}
                </p>
                @if(request()->filled('category_id'))
                    <a href="{{ route('catalog') }}" class="inline-flex items-center space-x-2 px-4 py-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all duration-200">
                        <span>Kembali ke Daftar Kategori</span>
                    </a>
                @endif
            </div>
        @endforelse
        </div>
    @endif
</div>

<!-- PURCHASE MODAL -->
<div id="purchaseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative max-h-[calc(100vh-2rem)] overflow-y-auto">
        <!-- Close Button -->
        <button onclick="closeModal()" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Dynamic Modal States -->
        <div id="modalStepInput">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight">Konfirmasi Pembelian</h3>
            <p id="modalProductTitle" class="text-sm text-blue-600 dark:text-blue-400 font-semibold mt-1">Nama Paket</p>
            
            <form id="purchaseForm" onsubmit="submitPurchase(event)" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" id="formProductId" name="product_id">
                <input type="hidden" id="email_or_whatsapp" name="email_or_whatsapp" value="{{ Auth::check() ? (Auth::user()->email ?: Auth::user()->phone) : '' }}">

                <div id="purchaseInputError" class="hidden p-3.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-2xl text-xs font-bold text-rose-700 dark:text-rose-400"></div>

                <div id="targetPhoneContainer" class="hidden">
                    <label for="target_phone" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nomor HP Tujuan / ID Pelanggan</label>
                    <input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="15" autocomplete="off" id="target_phone" name="target_phone" placeholder="Contoh: 081234567890" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1.5">Nomor HP ini akan diisi pulsa/kuota otomatis oleh supplier setelah pembayaran sukses.</p>
                </div>

                <div id="preorderNameContainer" class="hidden">
                    <label for="preorder_name" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Penerima Pre-Order</label>
                    <input type="text" id="preorder_name" name="preorder_name" placeholder="Contoh: Budi Santoso" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1.5">Masukkan nama penerima untuk catatan transaksi pre-order.</p>
                </div>

                @auth
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 bg-slate-50 dark:bg-slate-900 border-2 border-blue-600 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition" id="payMethodQrisLabel">
                            <input type="radio" name="payment_method" value="qris" checked onchange="togglePaymentMethod('qris')" class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500">
                            <span class="font-bold text-sm text-slate-850 dark:text-slate-200">QRIS</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Otomatis / Instan</span>
                        </label>
                        <label class="relative flex flex-col p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition" id="payMethodBalanceLabel">
                            <input type="radio" name="payment_method" value="balance" onchange="togglePaymentMethod('balance')" class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500">
                            <span class="font-bold text-sm text-slate-850 dark:text-slate-200">Saldo Akun</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1" id="checkoutUserBalanceText">Memuat...</span>
                        </label>
                    </div>
                </div>
                @else
                <input type="hidden" name="payment_method" value="qris">
                @endauth

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-xs text-slate-400 dark:text-slate-500 block">Harga Dasar:</span>
                        <span id="modalProductPrice" class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Rp 0</span>
                    </div>
                    <button type="submit" id="submitBtn" class="w-full sm:w-auto flex items-center justify-center space-x-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                        <span>Lanjut ke Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- PAYMENT INSTRUCTION STATE -->
        <div id="modalStepPayment" class="hidden text-center space-y-6">
            <div>
                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-full text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Menunggu Pembayaran</span>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight mt-3">Scan QRIS Untuk Bayar</h3>
                <p id="paymentOrderCode" class="text-xs text-slate-400 dark:text-slate-500 mt-1">Order ID: ORD-XXXXXX</p>
            </div>

            <!-- QR Code Render container -->
            <div class="relative w-60 h-60 mx-auto bg-white p-3 border border-slate-100 dark:border-slate-800 rounded-3xl flex items-center justify-center shadow-lg">
                <img id="qrisImage" src="" alt="QRIS QR Code" class="w-full h-full object-contain">
                
                <!-- QR Loading overlay -->
                <div id="qrLoading" class="absolute inset-0 bg-white/95 dark:bg-slate-950/95 flex flex-col items-center justify-center rounded-3xl">
                    <svg class="animate-spin w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">Membuat QRIS Dinamis...</span>
                </div>
            </div>

            <!-- Amount details with COPY button -->
            <div class="bg-slate-50 dark:bg-slate-900 p-4 border border-slate-150 dark:border-slate-800/80 rounded-2xl space-y-1 relative">
                <span class="text-xs text-slate-400 dark:text-slate-500 block uppercase tracking-wider font-semibold">Jumlah yang Harus Dibayar:</span>
                <div class="flex items-center justify-center space-x-2">
                    <span id="paymentTotalAmount" class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">Rp 0</span>
                    <button onclick="copyAmount()" class="p-1.5 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 rounded-lg transition-all duration-150" title="Salin Nominal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    </button>
                </div>
                <p class="text-[10px] text-amber-600 dark:text-amber-400 font-medium leading-relaxed">
                    *PENTING: Transfer HARUS persis hingga 3 angka terakhir untuk verifikasi otomatis!
                </p>
            </div>

            <!-- Countdown Timer -->
            <div class="flex items-center justify-center space-x-2.5 text-slate-600 dark:text-slate-400">
                <svg class="w-5 h-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm">Batas Waktu:</span>
                <span id="paymentCountdown" class="text-sm font-bold bg-slate-100 dark:bg-slate-900 px-3 py-1 rounded-xl text-slate-800 dark:text-slate-200">15:00</span>
            </div>

            <!-- Real-time Loading Spinner -->
            <div class="flex items-center justify-center space-x-3 text-sm text-slate-400 dark:text-slate-500">
                <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Mencocokkan mutasi otomatis...</span>
            </div>

            <!-- QRIS Action Buttons -->
            <div id="qrisActionButtons" class="flex flex-col sm:flex-row gap-3 pt-2">
                <button onclick="checkPaymentStatus()" id="btnCheckPayment" class="flex-1 flex items-center justify-center space-x-2 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-bold transition shadow-lg shadow-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Cek Status Pembayaran</span>
                </button>
                <button onclick="cancelOrder()" id="btnCancelOrder" class="flex-1 flex items-center justify-center space-x-2 py-3 px-4 bg-white hover:bg-rose-50 dark:bg-slate-900 dark:hover:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-2xl text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Batalkan Pesanan</span>
                </button>
            </div>
        </div>

        <!-- TRANSACTION SUCCESS STATE -->
        <div id="modalStepSuccess" class="hidden space-y-6">
            <div id="successHeaderDefault" class="text-center">
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight">Detail Pesanan Anda</h3>
                <p id="successModalOrderId" class="text-xs text-slate-400 dark:text-slate-500 mt-1">Order ID: ORD-XXXXXX</p>
            </div>

            <div id="successDetailsContainer" class="bg-blue-50 dark:bg-blue-950/20 p-5 border border-blue-100 dark:border-blue-900/50 rounded-3xl text-slate-800 dark:text-slate-200">
                <!-- Dinamis diisi lewat JavaScript -->
            </div>

            <div id="successFooterAction" class="pt-2">
                <button type="button" onclick="closeModal()" class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-lg shadow-blue-500/20 active:scale-95">
                    Lanjut Belanja
                </button>
            </div>
        </div>

        <!-- EXPIRED / ERROR STATE -->
        <div id="modalStepExpired" class="hidden text-center space-y-6 py-6">
            <div class="w-20 h-20 bg-rose-100 dark:bg-rose-950/40 rounded-full flex items-center justify-center mx-auto text-rose-600 shadow-xl shadow-rose-500/10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>

            <div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100">Batas Waktu Habis</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Maaf, pesanan Anda telah kedaluwarsa karena tidak ada pembayaran yang terdeteksi dalam 15 menit.</p>
            </div>

            <button onclick="closeModal()" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl text-sm font-semibold transition-all duration-200">
                Tutup & Coba Lagi
            </button>
        </div>

        <!-- TRANSACTION FAILED / AUTO-REFUND STATE -->
        <div id="modalStepFailed" class="hidden text-center space-y-5 py-4">
            <div class="w-24 h-24 rounded-full bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center mx-auto">
                <div class="w-16 h-16 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-rose-500/20">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            <div class="space-y-1.5">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50">
                    Transaksi Gagal
                </span>
                <h3 id="failedModalTitle" class="text-2xl font-black text-slate-800 dark:text-slate-100 pt-1">Pesanan Gagal Diproses</h3>
                <p id="failedModalOrderId" class="hidden text-xs font-mono text-slate-400 dark:text-slate-500"></p>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-left space-y-2.5">
                <p id="failedModalMessage" class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed"></p>
                <div id="failedModalReasonWrap" class="hidden pt-2 border-t border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Keterangan Supplier:</span>
                    <p id="failedModalReason" class="text-xs font-mono text-rose-600 dark:text-rose-400 break-words"></p>
                </div>
            </div>

            <div id="failedModalRefundBadge" class="hidden p-3.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl flex items-center justify-center space-x-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                <span>↩️</span>
                <span>Saldo Anda telah dikembalikan otomatis ke akun.</span>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="retryFromFailedModal()" class="py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-xs sm:text-sm transition shadow-lg shadow-blue-500/20">
                    Coba Lagi
                </button>
                <button type="button" onclick="closeModal()" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs sm:text-sm transition">
                    Tutup
                </button>
            </div>
        </div>

        <!-- ANIMATED PROGRESS CARD STATE -->
        <div id="modalStepProgress" class="hidden space-y-5">
            <!-- Status Badge + Header -->
            <div class="text-center space-y-2">
                <span id="progressStatusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 animate-pulse">
                    ⏳ Sedang Diproses
                </span>
                <h3 id="progressTitle" class="text-xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight">Memproses Pesanan...</h3>
                <p id="progressOrderId" class="text-xs font-mono text-slate-400 dark:text-slate-500"></p>
            </div>

            <!-- Transaction Info Card -->
            <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 space-y-2.5">
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                    <div>
                        <span class="text-slate-400 dark:text-slate-500 block">Tanggal</span>
                        <span id="progressDate" class="font-semibold text-slate-700 dark:text-slate-200">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 dark:text-slate-500 block">Kategori</span>
                        <span id="progressCategory" class="font-semibold text-slate-700 dark:text-slate-200">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 dark:text-slate-500 block">Produk</span>
                        <span id="progressProductName" class="font-semibold text-slate-700 dark:text-slate-200">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 dark:text-slate-500 block">Email</span>
                        <span id="progressEmail" class="font-semibold text-slate-700 dark:text-slate-200 truncate block">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 dark:text-slate-500 block">Nomor HP</span>
                        <span id="progressPhone" class="font-semibold text-slate-700 dark:text-slate-200">-</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-400 dark:text-slate-500">Total Pembayaran</span>
                    <span id="progressTotal" class="text-base font-extrabold text-blue-600 dark:text-blue-400">Rp 0</span>
                </div>
            </div>

            <!-- Animated Progress Steps -->
            <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 space-y-0">
                <div id="progressSteps" class="space-y-0">
                    <!-- Step 1: Verifikasi Pembayaran (always present) -->
                    <div id="stepPayment" class="flex items-center space-x-3 py-3 border-b border-slate-100 dark:border-slate-800">
                        <div id="stepPaymentIcon" class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Memverifikasi Pembayaran</span>
                            <span id="stepPaymentStatus" class="block text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Selesai</span>
                        </div>
                    </div>

                    <!-- Step 2: VPN (conditional) -->
                    <div id="stepVpn" class="hidden flex items-center space-x-3 py-3 border-b border-slate-100 dark:border-slate-800">
                        <div id="stepVpnIcon" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Sedang Membuat Akun VPN</span>
                            <span id="stepVpnStatus" class="block text-[10px] text-slate-400 dark:text-slate-500 font-medium">Menunggu...</span>
                        </div>
                    </div>

                    <!-- Step 3: Supplier (conditional) -->
                    <div id="stepSupplier" class="hidden flex items-center space-x-3 py-3">
                        <div id="stepSupplierIcon" class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span id="stepSupplierTitle" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Sedang Memeriksa Nomor HP</span>
                            <span id="stepSupplierStatus" class="block text-[10px] text-slate-400 dark:text-slate-500 font-medium">Menunggu...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Section (shown when done/failed) -->
            <div id="progressResult" class="hidden space-y-4">
                <!-- Success result -->
                <div id="progressResultSuccess" class="hidden">
                    <div id="progressSnContainer" class="hidden p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-center">
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider block">Serial Number</span>
                        <span id="progressSn" class="text-sm font-mono font-bold text-emerald-700 dark:text-emerald-400 select-all"></span>
                    </div>
                    <div id="progressVpnConfig" class="hidden"></div>
                    <div id="progressSuccessInstruction" class="hidden p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl text-left">
                        <span class="text-xs text-emerald-700 dark:text-emerald-400 font-bold block uppercase tracking-wider mb-1">Informasi Pesanan:</span>
                        <div id="progressSuccessInstructionText" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap"></div>
                    </div>
                </div>

                <!-- Failed result -->
                <div id="progressResultFailed" class="hidden space-y-3">
                    <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 rounded-2xl text-left">
                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400 block mb-1">Penyebab Gagal:</span>
                        <p id="progressFailReason" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed"></p>
                    </div>
                    <div id="progressRefundBadge" class="hidden p-3.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl flex items-center justify-center space-x-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                        <span>↩️</span>
                        <span>Saldo Anda telah dikembalikan otomatis ke akun.</span>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div id="progressFooter" class="hidden pt-2">
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="retryFromProgressModal()" id="progressBtnRetry" class="hidden py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-xs sm:text-sm transition shadow-lg shadow-blue-500/20">
                        Coba Lagi
                    </button>
                    <button type="button" onclick="closeModal()" id="progressBtnClose" class="py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-lg shadow-blue-500/20 col-span-2">
                        Lanjut Belanja
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- INFO DETAIL MODAL -->
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative max-h-[calc(100vh-2rem)] overflow-y-auto">
        <button onclick="closeDetailModal()" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <h3 id="detailProductTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Detail Produk</h3>
        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
            <p><strong>Nama:</strong> <span id="detailName"></span></p>
            <p><strong>Harga:</strong> <span id="detailPrice"></span></p>
            <p><strong>Masa Aktif:</strong> <span id="detailDuration"></span> Hari</p>
            <p><strong>Deskripsi:</strong></p>
            <p id="detailDesc" class="text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800 whitespace-pre-wrap leading-relaxed"></p>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeDetailModal()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all duration-200">
                Mengerti
            </button>
        </div>
    </div>
</div>

<!-- Polling & Countdown Script -->
<script>
    let activeOrderId = null;
    let pollInterval = null;
    let countdownInterval = null;
    let currentRawAmount = 0;
    let progressPollInterval = null;
    let checkPaymentCooldown = false;

    // Open detail modal
    function openDetailModal(name, price, duration, description, normalPrice, discountAmount) {
        document.getElementById('detailName').innerText = name;
        if (discountAmount && parseInt(discountAmount) > 0) {
            document.getElementById('detailPrice').innerHTML = `
                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">Rp ${parseInt(price).toLocaleString('id-ID')}</span>
                <span class="text-xs text-slate-400 line-through ml-1.5">Rp ${parseInt(normalPrice).toLocaleString('id-ID')}</span>
                <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 rounded-md">Hemat Rp ${parseInt(discountAmount).toLocaleString('id-ID')}</span>
            `;
        } else {
            document.getElementById('detailPrice').innerText = 'Rp ' + parseInt(price).toLocaleString('id-ID');
        }
        document.getElementById('detailDuration').innerText = duration;
        document.getElementById('detailDesc').innerHTML = description || `Masa aktif: <strong>${duration} hari</strong>. Konfigurasi siap pakai langsung unduh setelah sukses verifikasi pembayaran.`;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    let currentBuyPricing = null;
    let currentUserBalance = 0;

    function updateModalPriceDisplay(method) {
        if (!currentBuyPricing) return;
        const targetPrice = (method === 'balance') ? currentBuyPricing.priceBalance : currentBuyPricing.priceQris;
        const targetDisc = (method === 'balance') ? currentBuyPricing.discBalance : currentBuyPricing.discQris;
        
        if (targetDisc && parseInt(targetDisc) > 0) {
            document.getElementById('modalProductPrice').innerHTML = `
                <span class="text-emerald-600 dark:text-emerald-400">Rp ${parseInt(targetPrice).toLocaleString('id-ID')}</span>
                <span class="text-xs text-slate-400 line-through font-normal ml-1.5">Rp ${parseInt(currentBuyPricing.normalPrice).toLocaleString('id-ID')}</span>
                <span class="ml-1.5 px-2 py-0.5 text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-md">Diskon Rp ${parseInt(targetDisc).toLocaleString('id-ID')}</span>
            `;
        } else {
            document.getElementById('modalProductPrice').innerText = 'Rp ' + parseInt(targetPrice).toLocaleString('id-ID');
        }
    }

    // Open buy modal
    function openBuyModal(productId, productName, normalPrice, hasSupplierCode, isClosed, priceQris, discQris, priceBalance, discBalance) {
        currentBuyPricing = {
            normalPrice: normalPrice,
            priceQris: priceQris,
            discQris: discQris,
            priceBalance: priceBalance,
            discBalance: discBalance
        };

        document.getElementById('formProductId').value = productId;
        document.getElementById('modalProductTitle').innerText = productName;
        updateModalPriceDisplay('qris');
        
        const targetPhoneContainer = document.getElementById('targetPhoneContainer');
        const targetPhoneInput = document.getElementById('target_phone');

        // Selalu kosongkan kolom nomor HP setiap kali modal dibuka
        targetPhoneInput.value = '';
        
        if (hasSupplierCode && hasSupplierCode !== '') {
            targetPhoneContainer.classList.remove('hidden');
            targetPhoneInput.required = true;
        } else {
            targetPhoneContainer.classList.add('hidden');
            targetPhoneInput.required = false;
        }

        const preorderNameContainer = document.getElementById('preorderNameContainer');
        const preorderNameInput = document.getElementById('preorder_name');
        
        if (isClosed) {
            preorderNameContainer.classList.remove('hidden');
            preorderNameInput.required = true;
        } else {
            preorderNameContainer.classList.add('hidden');
            preorderNameInput.required = false;
            preorderNameInput.value = '';
        }

        // Set default payment method selection to QRIS
        const qrisInput = document.querySelector('input[name="payment_method"][value="qris"]');
        if (qrisInput) {
            qrisInput.checked = true;
            togglePaymentMethod('qris');
        }

        // Fetch balance if user is authenticated
        const balanceText = document.getElementById('checkoutUserBalanceText');
        const balanceInput = document.querySelector('input[name="payment_method"][value="balance"]');
        const balanceLabel = document.getElementById('payMethodBalanceLabel');
        if (balanceText) {
            balanceText.innerText = 'Memuat...';
            if (balanceInput) {
                balanceInput.disabled = true;
                balanceLabel.classList.add('opacity-50', 'cursor-not-allowed');
            }
            fetch('/api/balance')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentUserBalance = data.balance;
                    balanceText.innerText = data.formatted_balance;
                    if (data.balance >= currentBuyPricing.priceBalance) {
                        if (balanceInput) {
                            balanceInput.disabled = false;
                            balanceLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    } else {
                        balanceText.innerText += ' (Kurang)';
                    }
                } else {
                    balanceText.innerText = 'Gagal memuat';
                }
            })
            .catch(() => {
                balanceText.innerText = 'Error';
            });
        }
        
        // Reset states
        document.getElementById('modalStepInput').classList.remove('hidden');
        document.getElementById('modalStepPayment').classList.add('hidden');
        document.getElementById('modalStepSuccess').classList.add('hidden');
        document.getElementById('modalStepExpired').classList.add('hidden');
        document.getElementById('modalStepFailed').classList.add('hidden');
        document.getElementById('modalStepProgress').classList.add('hidden');
        const inputErr = document.getElementById('purchaseInputError');
        if (inputErr) {
            inputErr.classList.add('hidden');
            inputErr.innerText = '';
        }
        
        document.getElementById('purchaseModal').classList.remove('hidden');
    }

    function togglePaymentMethod(method) {
        const qrisLabel = document.getElementById('payMethodQrisLabel');
        const balanceLabel = document.getElementById('payMethodBalanceLabel');
        const submitBtn = document.getElementById('submitBtn');

        if (!qrisLabel || !balanceLabel) return;

        updateModalPriceDisplay(method);

        if (method === 'qris') {
            qrisLabel.className = "relative flex flex-col p-4 bg-slate-50 dark:bg-slate-900 border-2 border-blue-600 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition";
            balanceLabel.className = "relative flex flex-col p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition";
            if (submitBtn) {
                submitBtn.innerHTML = `
                    <span>Lanjut ke Pembayaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                `;
            }
        } else {
            balanceLabel.className = "relative flex flex-col p-4 bg-slate-50 dark:bg-slate-900 border-2 border-blue-600 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition";
            qrisLabel.className = "relative flex flex-col p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-900/50 transition";
            if (submitBtn) {
                submitBtn.innerHTML = `
                    <span>Bayar Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                `;
            }
        }
    }

    function closeModal() {
        document.getElementById('purchaseModal').classList.add('hidden');
        const progressEl = document.getElementById('modalStepProgress');
        if (progressEl) progressEl.classList.add('hidden');
        resetOrderTracker();

        // Bersihkan kolom nomor HP tujuan & nama pre-order saat modal ditutup
        const targetPhoneInput = document.getElementById('target_phone');
        if (targetPhoneInput) targetPhoneInput.value = '';
        const preorderNameInput = document.getElementById('preorder_name');
        if (preorderNameInput) preorderNameInput.value = '';
    }

    // =====================================================
    // Auto-correct Nomor HP Tujuan (hanya angka, format 08xxx)
    // =====================================================
    function normalizeTargetPhone(raw) {
        let digits = String(raw || '').replace(/[^0-9]/g, '');
        if (digits.startsWith('628')) {
            digits = '0' + digits.substring(2);          // +62 878... / 62878... -> 0878...
        } else if (digits.startsWith('8') && digits.length >= 9) {
            digits = '0' + digits;                        // 878... -> 0878...
        }
        return digits;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('target_phone');
        if (!phoneInput) return;

        // 1. Tolak karakter selain angka saat diketik
        phoneInput.addEventListener('beforeinput', function (e) {
            if (e.inputType === 'insertText' && e.data && /[^0-9]/.test(e.data)) {
                e.preventDefault();
            }
        });

        // 2. Saat menempel (paste), rapikan otomatis menjadi format 08xxxxxxxxxx
        phoneInput.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            const start = phoneInput.selectionStart ?? phoneInput.value.length;
            const end = phoneInput.selectionEnd ?? phoneInput.value.length;
            const combined = phoneInput.value.slice(0, start) + pasted + phoneInput.value.slice(end);
            phoneInput.value = normalizeTargetPhone(combined);
        });

        // 3. Jaring pengaman (autofill / keyboard HP / drag-drop): buang semua karakter non-angka
        phoneInput.addEventListener('input', function () {
            const cleaned = phoneInput.value.replace(/[^0-9]/g, '');
            if (cleaned !== phoneInput.value) {
                phoneInput.value = normalizeTargetPhone(phoneInput.value);
            }
        });

        // 4. Saat kolom ditinggalkan, rapikan awalan 62 / 8 menjadi 08
        phoneInput.addEventListener('blur', function () {
            phoneInput.value = normalizeTargetPhone(phoneInput.value);
        });
    });

    function resetOrderTracker() {
        activeOrderId = null;
        if (pollInterval) clearInterval(pollInterval);
        if (countdownInterval) clearInterval(countdownInterval);
        if (progressPollInterval) {
            clearInterval(progressPollInterval);
            progressPollInterval = null;
        }
    }

    // Submit purchase form via AJAX
    function submitPurchase(e) {
        e.preventDefault();
        
        const emailOrWhatsapp = document.getElementById('email_or_whatsapp').value;
        const productId = document.getElementById('formProductId').value;
        const targetPhoneInput = document.getElementById('target_phone');
        const targetPhoneContainer = document.getElementById('targetPhoneContainer');
        targetPhoneInput.value = normalizeTargetPhone(targetPhoneInput.value);
        const targetPhone = targetPhoneInput.value.trim();
        
        const inputErr = document.getElementById('purchaseInputError');
        if (inputErr) {
            inputErr.classList.add('hidden');
            inputErr.innerText = '';
        }

        // Client-side validation for target_phone if visible
        if (!targetPhoneContainer.classList.contains('hidden')) {
            if (!/^[0-9]+$/.test(targetPhone) || targetPhone.length < 10 || targetPhone.length > 13) {
                if (inputErr) {
                    inputErr.innerText = 'Nomor HP Tujuan harus berupa angka dengan panjang 10-13 karakter!';
                    inputErr.classList.remove('hidden');
                }
                return;
            }
        }

        const paymentMethodEl = document.querySelector('input[name="payment_method"]:checked');
        const paymentMethod = paymentMethodEl ? paymentMethodEl.value : 'qris';
        
        const submitBtn = document.getElementById('submitBtn');
        const defaultBtnHtml = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Memproses...';
 
        const preorderNameInput = document.getElementById('preorder_name');
        const preorderName = preorderNameInput ? preorderNameInput.value.trim() : '';

        fetch("{{ route('buy') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                email_or_whatsapp: emailOrWhatsapp,
                target_phone: targetPhone,
                preorder_name: preorderName,
                payment_method: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = defaultBtnHtml;
            
            if (data.success) {
                if (data.payment_method === 'balance') {
                    document.getElementById('modalStepInput').classList.add('hidden');
                    if (data.deferred && data.progress) {
                        // Show animated progress card for deferred fulfillment
                        showProgressCard(data.progress);
                    } else {
                        document.getElementById('modalStepSuccess').classList.remove('hidden');
                        populateSuccessDetails(data.order.vpn_config, data.order.success_instruction, data.order.id, data.order.status);
                        if (data.order.status === 'proses') {
                            activeOrderId = data.order.id;
                            startPolling(false);
                        }
                    }
                } else {
                    renderPaymentInstructions(data.order);
                }
            } else {
                showFailedRefundState(
                    data.refunded === true,
                    data.order_id || '',
                    data.message || 'Gagal memproses pesanan.',
                    data.reason || ''
                );
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = defaultBtnHtml;
            showFailedRefundState(false, '', 'Terjadi kesalahan koneksi server. Silakan periksa riwayat pesanan Anda.', '');
            console.error(err);
        });
    }

    // Show QRIS details and start countdown + polling
    function renderPaymentInstructions(order) {
        activeOrderId = order.id;
        currentRawAmount = order.total_amount;
        
        // Show payment state, hide input state
        document.getElementById('modalStepInput').classList.add('hidden');
        document.getElementById('modalStepPayment').classList.remove('hidden');
        
        // Populate instructions
        document.getElementById('paymentOrderCode').innerText = 'Order ID: ' + order.id;
        document.getElementById('paymentTotalAmount').innerText = 'Rp ' + parseInt(order.total_amount).toLocaleString('id-ID');
        
        // Load QR Code Image
        const qrLoading = document.getElementById('qrLoading');
        const qrisImage = document.getElementById('qrisImage');
        qrLoading.classList.remove('hidden');
        
        const qrisPayloadUrl = encodeURIComponent(order.qris_payload);
        qrisImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${qrisPayloadUrl}`;
        
        qrisImage.onload = () => {
            qrLoading.classList.add('hidden');
        };

        // Start countdown timer
        startCountdown(order.expired_at, order.server_time);
        
        // Start polling for payment verification
        startPolling();
    }

    // Countdown Timer logic
    function startCountdown(expiryStr, serverTimeStr) {
        const expiry = new Date(expiryStr).getTime();
        const serverTime = new Date(serverTimeStr).getTime();
        const localStartTime = new Date().getTime();
        const serverDiff = serverTime - localStartTime; // calculate delay between server and client clock

        const countdownElement = document.getElementById('paymentCountdown');
        
        if (countdownInterval) clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            const now = new Date().getTime() + serverDiff;
            const distance = expiry - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                showExpiredState();
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerText = 
                (minutes < 10 ? '0' : '') + minutes + ':' + 
                (seconds < 10 ? '0' : '') + seconds;
        }, 1000);
    }

    // Polling logic
    function startPolling(watchPayment = true) {
        if (pollInterval) clearInterval(pollInterval);
        
        pollInterval = setInterval(() => {
            if (!activeOrderId) return;
            
            fetch(`/orders/${activeOrderId}/status`)
            .then(res => res.json())
            .then(data => {
                if (data.group === 'success') {
                    showSuccessState(data.vpn_config, data.success_instruction, data.status, data.sn);
                } else if (data.status === 'proses') {
                    if (watchPayment) {
                        // QRIS payment confirmed → transition to progress card if fulfillment is running
                        if (data.stage && data.stage !== null && (data.steps?.vpn || data.steps?.supplier)) {
                            document.getElementById('modalStepPayment').classList.add('hidden');
                            showProgressCard(data);
                            clearInterval(pollInterval);
                            pollInterval = null;
                        } else {
                            const processingOrderId = activeOrderId;
                            document.getElementById('modalStepPayment').classList.add('hidden');
                            document.getElementById('modalStepSuccess').classList.remove('hidden');
                            populateSuccessDetails(data.vpn_config, data.success_instruction, processingOrderId, 'proses', data.sn);
                        }
                        watchPayment = false;
                    }
                } else if (data.group === 'failed') {
                    showFailedRefundState(
                        data.refunded === true,
                        activeOrderId,
                        data.refunded ? 'Pesanan gagal diproses. Dana Anda telah dikembalikan otomatis ke Saldo Akun.' : 'Pesanan gagal diproses. Silakan hubungi Admin untuk bantuan.',
                        data.reason || ''
                    );
                } else if (data.status === 'expired') {
                    showExpiredState();
                } else if (data.status === 'ditolak' || data.status === 'rejected') {
                    showExpiredState();
                } else if (data.status === 'dibatalkan') {
                    showCancelledState();
                }
            })
            .catch(err => console.error("Error polling order status:", err));
        }, 4000); // Poll every 4 seconds
    }

    function showFailedRefundState(refunded, orderId = '', message = '', reason = '') {
        const failedOrderId = orderId || activeOrderId || '';
        resetOrderTracker();
        document.getElementById('modalStepInput').classList.add('hidden');
        document.getElementById('modalStepPayment').classList.add('hidden');
        document.getElementById('modalStepSuccess').classList.add('hidden');
        document.getElementById('modalStepExpired').classList.add('hidden');
        document.getElementById('modalStepProgress').classList.add('hidden');
        document.getElementById('modalStepFailed').classList.remove('hidden');

        const titleEl = document.getElementById('failedModalTitle');
        const orderEl = document.getElementById('failedModalOrderId');
        const msgEl = document.getElementById('failedModalMessage');
        const reasonWrap = document.getElementById('failedModalReasonWrap');
        const reasonEl = document.getElementById('failedModalReason');
        const refundBadge = document.getElementById('failedModalRefundBadge');

        if (titleEl) {
            titleEl.innerText = refunded ? 'Transaksi Gagal & Dana Dikembalikan' : 'Transaksi Gagal Diproses';
        }
        if (orderEl) {
            if (failedOrderId) {
                orderEl.innerText = 'Order ID: ' + failedOrderId;
                orderEl.classList.remove('hidden');
            } else {
                orderEl.classList.add('hidden');
            }
        }
        if (msgEl) {
            msgEl.innerText = message || 'Pesanan tidak dapat diproses oleh supplier.';
        }
        if (reasonWrap && reasonEl) {
            if (reason && reason.trim() !== '') {
                reasonEl.innerText = reason;
                reasonWrap.classList.remove('hidden');
            } else {
                reasonWrap.classList.add('hidden');
            }
        }
        if (refundBadge) {
            if (refunded) {
                refundBadge.classList.remove('hidden');
            } else {
                refundBadge.classList.add('hidden');
            }
        }
    }

    function retryFromFailedModal() {
        document.getElementById('modalStepFailed').classList.add('hidden');
        document.getElementById('modalStepInput').classList.remove('hidden');
        // Refresh saldo user agar tampil terbaru pasca auto-refund
        const balanceText = document.getElementById('checkoutUserBalanceText');
        const balanceInput = document.querySelector('input[name="payment_method"][value="balance"]');
        const balanceLabel = document.getElementById('payMethodBalanceLabel');
        if (balanceText && currentBuyPricing) {
            fetch('/api/balance')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentUserBalance = data.balance;
                        balanceText.innerText = data.formatted_balance;
                        if (balanceInput && balanceLabel && data.balance >= currentBuyPricing.priceBalance) {
                            balanceInput.disabled = false;
                            balanceLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                })
                .catch(() => {});
        }
    }

    function showSuccessState(vpnConfig, successInstruction, orderStatus = 'sukses', sn = '') {
        const completedOrderId = activeOrderId;
        resetOrderTracker();
        document.getElementById('modalStepPayment').classList.add('hidden');
        document.getElementById('modalStepProgress').classList.add('hidden');
        document.getElementById('modalStepSuccess').classList.remove('hidden');
        
        populateSuccessDetails(vpnConfig, successInstruction, completedOrderId, orderStatus, sn);
    }

    function populateSuccessDetails(config, successInstruction, orderId, orderStatus = 'sukses', sn = '') {
        const container = document.getElementById('successDetailsContainer');
        if (!container) return;
        
        const headerDefault = document.getElementById('successHeaderDefault');
        const orderIdSubtitle = document.getElementById('successModalOrderId');
        if (orderIdSubtitle) {
            orderIdSubtitle.innerText = 'Order ID: ' + (orderId || '');
        }

        // Extract node link (vmess, vless, trojan, ss, shadowsocks)
        const trimmedConfig = config ? config.trim() : '';
        let nodeLink = '';
        const match = trimmedConfig.match(/(vmess|vless|trojan|ss|shadowsocks):\/\/[^\s"]+/i);
        if (match) {
            nodeLink = match[0];
        }

        // Case 0: Produk TIDAK memiliki otomatisasi/konfigurasi VPS (trimmedConfig kosong)
        if (!trimmedConfig) {
            if (headerDefault) {
                headerDefault.classList.add('hidden');
            }
            container.className = "text-center space-y-5 py-2 text-slate-800 dark:text-slate-200";

            const isProcessing = (orderStatus === 'proses');
            const iconHtml = isProcessing
                ? `
                    <div class="w-24 h-24 rounded-full bg-amber-100 dark:bg-amber-950/40 flex items-center justify-center mx-auto">
                        <div class="w-16 h-16 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <svg class="w-9 h-9 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3.5"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                `
                : `
                    <div class="pt-2">
                        <div class="w-20 h-20 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto shadow-xl shadow-emerald-500/30 ring-8 ring-emerald-100 dark:ring-emerald-950/50">
                            <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                `;

            let html = `
                <div class="pt-2">${iconHtml}</div>
                <div class="space-y-2">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold ${isProcessing ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50'}">
                            ${isProcessing ? '⏳ Sedang Diproses Supplier' : '✅ Transaksi Selesai'}
                        </span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">${isProcessing ? 'Pesanan Sedang Diproses' : 'Transaksi Berhasil!'}</h3>
                    <p class="text-xs font-mono text-slate-400 dark:text-slate-500">Order ID: ${escapeHtml(orderId || '')}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 pt-1 leading-relaxed">
                        ${isProcessing
                            ? 'Pembayaran Anda sudah diterima dan pesanan sedang diproses ke supplier. Status akan diperbarui otomatis (atau cek di Riwayat Pesanan).'
                            : 'Pembayaran terverifikasi dan pesanan Anda telah sukses diproses oleh sistem.'}
                    </p>
                    ${sn ? `<div class="mt-2 inline-block px-3.5 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs font-mono font-bold text-emerald-700 dark:text-emerald-400 select-all">SN: ${escapeHtml(sn)}</div>` : ''}
                </div>
            `;

            if (successInstruction && successInstruction.trim() !== '') {
                html += `
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl text-left">
                        <span class="text-xs text-emerald-700 dark:text-emerald-400 font-bold block uppercase tracking-wider mb-1">Informasi Pesanan:</span>
                        <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">${escapeHtml(successInstruction)}</div>
                    </div>
                `;
            }

            if (isProcessing) {
                html += `
                    <div class="pt-1">
                        <a href="{{ route('orders.history') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold transition">
                            📋 Pantau di Riwayat Pesanan
                        </a>
                    </div>
                `;
            }

            container.innerHTML = html;
            return;
        }

        if (headerDefault) {
            headerDefault.classList.remove('hidden');
        }
        container.className = "bg-blue-50 dark:bg-blue-950/20 p-5 border border-blue-100 dark:border-blue-900/50 rounded-3xl max-w-lg mx-auto space-y-3 text-slate-800 dark:text-slate-200";

        // Case 1: Config is a node link (vmess, vless, trojan, etc.)
        if (nodeLink) {
            let html = `
                <div class="space-y-4">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block uppercase tracking-wider text-left">DETAIL AKUN VPN:</span>
                    <div class="flex flex-col md:flex-row gap-5 items-stretch justify-between text-left">
                        <div class="flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-3xl flex flex-col justify-between w-full">
                            <textarea readonly class="w-full bg-transparent text-xs font-mono text-slate-800 dark:text-slate-200 border-none outline-none focus:ring-0 resize-none h-28 pr-1 custom-scrollbar" id="successConfigText">${escapeHtml(nodeLink)}</textarea>
                            <div class="flex justify-center mt-4">
                                <button onclick="copySuccessConfig()" class="inline-flex items-center space-x-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-xs font-bold transition-all duration-200 shadow-md shadow-blue-500/10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                    <span id="copySuccessBtnText">Salin Akun</span>
                                </button>
                            </div>
                        </div>
                        <div class="w-full md:w-44 bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-sm aspect-square md:aspect-auto">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(nodeLink)}" alt="QR Code" class="w-full h-full object-contain rounded-xl">
                        </div>
                    </div>
                </div>
            `;

            // If there's also a success instruction, append it below the node link details
            if (successInstruction && successInstruction.trim() !== '') {
                html += `
                    <div class="mt-4 p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl text-left">
                        <span class="text-xs text-blue-600 dark:text-blue-400 font-bold block uppercase tracking-wider mb-2">Petunjuk Tambahan:</span>
                        <div class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap">${escapeHtml(successInstruction)}</div>
                    </div>
                `;
            }

            container.innerHTML = html;
            return;
        }

        // Case 2: No node link, but success instruction exists
        if (successInstruction && successInstruction.trim() !== '') {
            container.innerHTML = `
                <div class="text-left space-y-2 p-1">
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-bold block uppercase tracking-wider">Petunjuk Pembayaran Sukses:</span>
                    <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">${escapeHtml(successInstruction)}</div>
                </div>
            `;
            return;
        }

        // Case 3: Plain single-line credentials (e.g. vip.rzkstores.my.id:80@admin123)
        const isSingleLine = trimmedConfig && !trimmedConfig.includes('\n') && !trimmedConfig.includes('\r');
        if (isSingleLine) {
            container.innerHTML = `
                <div class="text-left space-y-3 p-1">
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-bold block uppercase tracking-wider">Konfigurasi VPN Anda:</span>
                    <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl flex items-center justify-between gap-3">
                        <span class="text-sm font-mono text-slate-800 dark:text-slate-200 select-all break-all" id="successConfigTextPlain">${escapeHtml(config)}</span>
                        <button onclick="copySuccessConfigPlain()" class="flex-shrink-0 p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all duration-200" title="Salin Akun">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        </button>
                    </div>
                </div>
            `;
            return;
        }

        // Case 4: Default download button (.ovpn configuration file)
        container.innerHTML = `
            <div class="space-y-3 p-1">
                <span class="text-xs text-blue-600 dark:text-blue-400 font-bold block uppercase tracking-wider">Konfigurasi VPN Anda:</span>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tekan tombol di bawah untuk mengunduh konfigurasi siap pakai Anda (.ovpn):</p>
                <a href="/orders/${orderId}/download" class="mt-4 inline-flex items-center space-x-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 transition-all duration-200">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh Konfigurasi (.ovpn)</span>
                </a>
            </div>
        `;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function copySuccessConfig() {
        const copyText = document.getElementById("successConfigText");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        const btnText = document.getElementById("copySuccessBtnText");
        btnText.innerText = "Tersalin!";
        setTimeout(() => {
            btnText.innerText = "Salin Akun";
        }, 2000);
    }

    function copySuccessConfigPlain() {
        const copyText = document.getElementById("successConfigTextPlain").innerText;
        navigator.clipboard.writeText(copyText);
        alert("Konfigurasi VPN berhasil disalin ke clipboard!");
    }

    function showExpiredState() {
        resetOrderTracker();
        document.getElementById('modalStepPayment').classList.add('hidden');
        document.getElementById('modalStepProgress').classList.add('hidden');
        document.getElementById('modalStepExpired').classList.remove('hidden');
    }

    function showCancelledState() {
        resetOrderTracker();
        document.getElementById('modalStepPayment').classList.add('hidden');
        document.getElementById('modalStepProgress').classList.add('hidden');
        document.getElementById('modalStepExpired').classList.remove('hidden');
        document.querySelector('#modalStepExpired h3').innerText = 'Pesanan Dibatalkan';
        document.querySelector('#modalStepExpired p').innerText = 'Pesanan Anda telah berhasil dibatalkan.';
    }

    function copyAmount() {
        if (!currentRawAmount) return;
        navigator.clipboard.writeText(currentRawAmount);
        
        // Minimal visual indicator
        const btn = document.querySelector('[onclick="copyAmount()"]');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="text-xs text-emerald-500 font-bold">Tersalin!</span>';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 1500);
    }

    // =====================================================
    // PROGRESS CARD FUNCTIONS
    // =====================================================

    const SVG_CHECK = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>';
    const SVG_SPINNER = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    const SVG_CROSS = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>';

    function showProgressCard(progressData) {
        // Hide all other steps
        document.getElementById('modalStepInput').classList.add('hidden');
        document.getElementById('modalStepPayment').classList.add('hidden');
        document.getElementById('modalStepSuccess').classList.add('hidden');
        document.getElementById('modalStepExpired').classList.add('hidden');
        document.getElementById('modalStepFailed').classList.add('hidden');
        document.getElementById('modalStepProgress').classList.remove('hidden');

        // Populate transaction info
        document.getElementById('progressOrderId').innerText = 'ID Transaksi: ' + (progressData.id || '-');
        document.getElementById('progressDate').innerText = progressData.date || '-';
        document.getElementById('progressCategory').innerText = progressData.category || '-';
        document.getElementById('progressProductName').innerText = progressData.product_name || '-';
        document.getElementById('progressEmail').innerText = progressData.email || '-';
        document.getElementById('progressPhone').innerText = progressData.phone || '-';
        document.getElementById('progressTotal').innerText = progressData.formatted_total || 'Rp 0';

        // Show/hide conditional steps
        const stepVpn = document.getElementById('stepVpn');
        const stepSupplier = document.getElementById('stepSupplier');

        if (progressData.steps && progressData.steps.vpn) {
            stepVpn.classList.remove('hidden');
            stepVpn.classList.add('flex');
        } else {
            stepVpn.classList.add('hidden');
            stepVpn.classList.remove('flex');
        }

        if (progressData.steps && progressData.steps.supplier) {
            stepSupplier.classList.remove('hidden');
            stepSupplier.classList.add('flex');
            const supplierTitle = document.getElementById('stepSupplierTitle');
            if (supplierTitle) {
                supplierTitle.innerText = progressData.phone ? 'Sedang Memeriksa Nomor HP' : 'Sedang Memproses Pesanan';
            }
        } else {
            stepSupplier.classList.add('hidden');
            stepSupplier.classList.remove('flex');
        }

        // Hide result and footer initially
        document.getElementById('progressResult').classList.add('hidden');
        document.getElementById('progressResultSuccess').classList.add('hidden');
        document.getElementById('progressResultFailed').classList.add('hidden');
        document.getElementById('progressFooter').classList.add('hidden');

        // Update the card with current stage
        updateProgressCard(progressData);

        // Start progress polling
        activeOrderId = progressData.id;
        startProgressPolling();
    }

    function updateProgressCard(data) {
        const stage = data.stage || 'queued';
        const group = data.group || 'processing';

        // Update status badge
        const badge = document.getElementById('progressStatusBadge');
        const title = document.getElementById('progressTitle');

        if (group === 'success') {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50';
            badge.innerHTML = '✅ Berhasil';
            title.innerText = 'Transaksi Berhasil!';
        } else if (group === 'failed') {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50';
            badge.innerHTML = '❌ Gagal';
            title.innerText = 'Pesanan Gagal Diproses';
        } else {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 animate-pulse';
            badge.innerHTML = '⏳ Sedang Diproses';
            title.innerText = 'Memproses Pesanan...';
        }

        // Update VPN step
        if (data.steps && data.steps.vpn) {
            updateStepIcon('stepVpn', stage, group, 'vpn', 'failed_vpn');
        }

        // Update Supplier step
        if (data.steps && data.steps.supplier) {
            updateStepIcon('stepSupplier', stage, group, 'supplier', 'failed_supplier');
        }

        // Show result section when done or failed
        if (group === 'success' || group === 'failed') {
            document.getElementById('progressResult').classList.remove('hidden');
            document.getElementById('progressFooter').classList.remove('hidden');

            if (group === 'success') {
                document.getElementById('progressResultSuccess').classList.remove('hidden');
                document.getElementById('progressResultFailed').classList.add('hidden');

                // SN
                if (data.sn) {
                    document.getElementById('progressSnContainer').classList.remove('hidden');
                    document.getElementById('progressSn').innerText = data.sn;
                }

                // VPN config
                if (data.vpn_config) {
                    const vpnDiv = document.getElementById('progressVpnConfig');
                    vpnDiv.classList.remove('hidden');
                    const trimmed = data.vpn_config.trim();
                    const nodeMatch = trimmed.match(/(vmess|vless|trojan|ss|shadowsocks):\/\/[^\s"]+/i);
                    if (nodeMatch) {
                        vpnDiv.innerHTML = `
                            <div class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/50 rounded-2xl space-y-3">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block uppercase tracking-wider">Akun VPN:</span>
                                <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-3 rounded-xl">
                                    <textarea readonly class="w-full bg-transparent text-xs font-mono text-slate-800 dark:text-slate-200 border-none outline-none focus:ring-0 resize-none h-16 custom-scrollbar">${escapeHtml(nodeMatch[0])}</textarea>
                                </div>
                                <button onclick="navigator.clipboard.writeText('${escapeHtml(nodeMatch[0]).replace(/'/g, "\\'")}'); this.innerText='Tersalin!'; setTimeout(()=>this.innerText='Salin Akun',2000)" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">Salin Akun</button>
                            </div>
                        `;
                    } else {
                        vpnDiv.innerHTML = `
                            <div class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/50 rounded-2xl">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block uppercase tracking-wider mb-2">Konfigurasi VPN:</span>
                                <a href="/orders/${data.id}/download" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh Konfigurasi</span>
                                </a>
                            </div>
                        `;
                    }
                }

                // Success instruction
                if (data.success_instruction && data.success_instruction.trim()) {
                    document.getElementById('progressSuccessInstruction').classList.remove('hidden');
                    document.getElementById('progressSuccessInstructionText').innerText = data.success_instruction;
                }

                // Update close button
                document.getElementById('progressBtnClose').innerText = 'Lanjut Belanja';
                document.getElementById('progressBtnClose').classList.remove('col-span-1');
                document.getElementById('progressBtnClose').classList.add('col-span-2');
                document.getElementById('progressBtnRetry').classList.add('hidden');

            } else if (group === 'failed') {
                document.getElementById('progressResultFailed').classList.remove('hidden');
                document.getElementById('progressResultSuccess').classList.add('hidden');
                document.getElementById('progressFailReason').innerText = data.reason || 'Pesanan tidak dapat diproses saat ini.';

                if (data.refunded) {
                    document.getElementById('progressRefundBadge').classList.remove('hidden');
                }

                // Show retry + close
                document.getElementById('progressBtnRetry').classList.remove('hidden');
                document.getElementById('progressBtnClose').classList.remove('col-span-2');
                document.getElementById('progressBtnClose').classList.add('col-span-1');
                document.getElementById('progressBtnClose').innerText = 'Tutup';
            }
        }
    }

    function updateStepIcon(stepId, currentStage, group, activeStage, failedStage) {
        const iconEl = document.getElementById(stepId + 'Icon');
        const statusEl = document.getElementById(stepId + 'Status');
        if (!iconEl || !statusEl) return;

        // Determine order: queued < vpn < supplier < done
        const stageOrder = { 'queued': 0, 'vpn': 1, 'supplier': 2, 'done': 3 };
        const currentIdx = stageOrder[currentStage] ?? -1;
        const activeIdx = stageOrder[activeStage] ?? -1;

        if (currentStage === failedStage) {
            // This step failed
            iconEl.className = 'w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm';
            iconEl.innerHTML = SVG_CROSS;
            statusEl.innerText = 'Gagal';
            statusEl.className = 'block text-[10px] text-rose-600 dark:text-rose-400 font-medium';
        } else if (currentIdx > activeIdx || group === 'success') {
            // This step completed
            iconEl.className = 'w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm';
            iconEl.innerHTML = SVG_CHECK;
            statusEl.innerText = 'Selesai';
            statusEl.className = 'block text-[10px] text-emerald-600 dark:text-emerald-400 font-medium';
        } else if (currentStage === activeStage) {
            // This step is running
            iconEl.className = 'w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm';
            iconEl.innerHTML = SVG_SPINNER;
            statusEl.innerText = 'Sedang diproses...';
            statusEl.className = 'block text-[10px] text-blue-600 dark:text-blue-400 font-medium';
        } else {
            // This step is waiting
            iconEl.className = 'w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-400 flex items-center justify-center flex-shrink-0';
            iconEl.innerHTML = SVG_SPINNER;
            statusEl.innerText = 'Menunggu...';
            statusEl.className = 'block text-[10px] text-slate-400 dark:text-slate-500 font-medium';
        }
    }

    function startProgressPolling() {
        if (progressPollInterval) clearInterval(progressPollInterval);

        progressPollInterval = setInterval(() => {
            if (!activeOrderId) return;

            fetch(`/orders/${activeOrderId}/status`)
            .then(res => res.json())
            .then(data => {
                updateProgressCard(data);

                if (data.group === 'success' || data.group === 'failed') {
                    clearInterval(progressPollInterval);
                    progressPollInterval = null;
                }
            })
            .catch(err => console.error("Error polling progress:", err));
        }, 2000); // Poll every 2 seconds
    }

    function retryFromProgressModal() {
        if (progressPollInterval) clearInterval(progressPollInterval);
        progressPollInterval = null;
        document.getElementById('modalStepProgress').classList.add('hidden');
        document.getElementById('modalStepInput').classList.remove('hidden');

        // Reset progress card state
        document.getElementById('progressResult').classList.add('hidden');
        document.getElementById('progressFooter').classList.add('hidden');
        document.getElementById('progressResultSuccess').classList.add('hidden');
        document.getElementById('progressResultFailed').classList.add('hidden');
        document.getElementById('progressRefundBadge').classList.add('hidden');
        document.getElementById('progressSnContainer').classList.add('hidden');
        document.getElementById('progressVpnConfig').classList.add('hidden');
        document.getElementById('progressVpnConfig').innerHTML = '';
        document.getElementById('progressSuccessInstruction').classList.add('hidden');

        // Refresh balance
        const balanceText = document.getElementById('checkoutUserBalanceText');
        const balanceInput = document.querySelector('input[name="payment_method"][value="balance"]');
        const balanceLabel = document.getElementById('payMethodBalanceLabel');
        if (balanceText && currentBuyPricing) {
            fetch('/api/balance')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentUserBalance = data.balance;
                        balanceText.innerText = data.formatted_balance;
                        if (balanceInput && balanceLabel && data.balance >= currentBuyPricing.priceBalance) {
                            balanceInput.disabled = false;
                            balanceLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                })
                .catch(() => {});
        }
    }

    // =====================================================
    // QRIS ACTION FUNCTIONS
    // =====================================================

    function checkPaymentStatus() {
        if (!activeOrderId || checkPaymentCooldown) return;

        const btn = document.getElementById('btnCheckPayment');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Sedang mengecek...</span>';
        checkPaymentCooldown = true;

        fetch(`/orders/${activeOrderId}/check-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success === false) {
                // Rate limited
                btn.innerHTML = '<span>⏳ Tunggu 30 detik...</span>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    checkPaymentCooldown = false;
                }, 30000);
                return;
            }

            if (data.group === 'processing' || data.group === 'success') {
                // Payment confirmed! Transition to progress card
                if (data.stage && (data.steps?.vpn || data.steps?.supplier)) {
                    document.getElementById('modalStepPayment').classList.add('hidden');
                    if (pollInterval) clearInterval(pollInterval);
                    if (countdownInterval) clearInterval(countdownInterval);
                    showProgressCard(data);
                } else if (data.group === 'success') {
                    showSuccessState(data.vpn_config, data.success_instruction, data.status, data.sn);
                } else {
                    document.getElementById('modalStepPayment').classList.add('hidden');
                    document.getElementById('modalStepSuccess').classList.remove('hidden');
                    populateSuccessDetails(data.vpn_config, data.success_instruction, data.id, 'proses', data.sn);
                }
                return;
            }

            // Not confirmed yet
            btn.innerHTML = '<span>Pembayaran belum dikonfirmasi</span>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                checkPaymentCooldown = false;
            }, 30000);
        })
        .catch(err => {
            console.error('Check payment error:', err);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            checkPaymentCooldown = false;
        });
    }

    function cancelOrder() {
        if (!activeOrderId) return;

        const btn = document.getElementById('btnCancelOrder');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span>Membatalkan...</span>';

        fetch(`/orders/${activeOrderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showCancelledState();
            } else {
                btn.innerHTML = `<span class="text-xs">${escapeHtml(data.message || 'Gagal membatalkan')}</span>`;
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 5000);
            }
        })
        .catch(err => {
            console.error('Cancel order error:', err);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>
@endsection
