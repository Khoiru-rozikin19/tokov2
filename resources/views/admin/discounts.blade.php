@extends('layouts.app', ['title' => 'Atur Diskon Produk'])

@section('content')
<div class="space-y-6 sm:space-y-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-slate-100">Kelola Diskon Role</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Atur potongan harga khusus per-produk untuk role user tertentu (Buyer, Seller, Admin). Potongan langsung diterapkan saat checkout.</p>
        </div>
        <button onclick="toggleCreateModal(true)" class="mt-4 sm:mt-0 flex items-center space-x-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Tambah Diskon</span>
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Total Aturan</span>
            <span class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1 block">{{ $discounts->count() }}</span>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Diskon Aktif</span>
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">{{ $discounts->where('is_active', true)->count() }}</span>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Diskon Nonaktif</span>
            <span class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 block">{{ $discounts->where('is_active', false)->count() }}</span>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
            <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase block">Total Produk</span>
            <span class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1 block">{{ $products->count() }}</span>
        </div>
    </div>

    <!-- Discounts Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-4.5 px-6">Produk</th>
                        <th class="py-4.5 px-6">Harga Normal</th>
                        <th class="py-4.5 px-6">Potongan Diskon</th>
                        <th class="py-4.5 px-6">Harga Final</th>
                        <th class="py-4.5 px-6">Berlaku Untuk Role</th>
                        <th class="py-4.5 px-6">Status</th>
                        <th class="py-4.5 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm font-medium text-slate-700 dark:text-slate-350">
                    @forelse($discounts as $discount)
                        @php
                            $prodPrice = $discount->product ? (int)$discount->product->price : 0;
                            $discAmt = (int)round($discount->discount_amount);
                            $finalPrice = max(0, $prodPrice - $discAmt);
                            $roles = is_array($discount->roles) ? $discount->roles : json_decode($discount->roles, true) ?? [];
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all duration-150 {{ !$discount->is_active ? 'opacity-50' : '' }}">
                            <td class="py-4.5 px-6">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $discount->product->name ?? 'Produk Dihapus' }}</div>
                                <div class="text-xs text-slate-400 dark:text-slate-500">ID: {{ $discount->product_id }}</div>
                            </td>
                            <td class="py-4.5 px-6 text-slate-600 dark:text-slate-400">
                                Rp {{ number_format($prodPrice, 0, ',', '.') }}
                            </td>
                            <td class="py-4.5 px-6">
                                <span class="text-rose-600 dark:text-rose-400 font-bold">
                                    - Rp {{ number_format($discAmt, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-4.5 px-6">
                                <span class="text-emerald-600 dark:text-emerald-400 font-black">
                                    Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-4.5 px-6">
                                <div class="flex flex-wrap gap-1.5">
                                    @if(in_array('buyer', $roles))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                                            Buyer
                                        </span>
                                    @endif
                                    @if(in_array('seller', $roles))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-900">
                                            Seller
                                        </span>
                                    @endif
                                    @if(in_array('admin', $roles))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                            Admin
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4.5 px-6">
                                @if($discount->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Button -->
                                    <button onclick="openEditModal({{ json_encode($discount) }})" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/20 text-slate-600 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 rounded-xl transition-all duration-200" title="Edit Diskon">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Toggle Form -->
                                    <form action="{{ route('admin.discounts.toggle', $discount->id) }}" method="POST" class="inline">
                                        @csrf
                                        @if($discount->is_active)
                                            <button type="submit" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-amber-50 dark:hover:bg-amber-950/20 text-slate-600 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 rounded-xl transition-all duration-200" title="Nonaktifkan">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @else
                                            <button type="submit" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 text-slate-600 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 rounded-xl transition-all duration-200" title="Aktifkan">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.discounts.delete', $discount->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan diskon ini?')" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/20 text-slate-600 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 rounded-xl transition-all duration-200" title="Hapus">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada aturan diskon yang dibuat. Klik tombol "Tambah Diskon" untuk membuat aturan baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CREATE DISCOUNT MODAL -->
<div id="createDiscountModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative">
        <button onclick="toggleCreateModal(false)" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight mb-2">Tambah Aturan Diskon</h3>
        <p class="text-xs text-slate-400 dark:text-slate-500 mb-6">Pilih produk dan tentukan role user mana yang berhak mendapatkan potongan harga.</p>

        <form action="{{ route('admin.discounts.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Pilih Produk -->
            <div>
                <label for="create_product_id" class="block text-xs font-bold text-slate-500 uppercase mb-2">Pilih Produk</label>
                <select id="create_product_id" name="product_id" required onchange="updateCreateCalculation()" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
                    <option value="" data-price="0">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Role Checkbox -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2.5">Pilih Role Penerima Diskon</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center space-x-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/20 cursor-pointer transition-all">
                        <input type="checkbox" name="roles[]" value="buyer" class="w-4.5 h-4.5 text-blue-600 rounded border-slate-300 dark:border-slate-700 focus:ring-blue-500">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Buyer</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/20 cursor-pointer transition-all">
                        <input type="checkbox" name="roles[]" value="seller" checked class="w-4.5 h-4.5 text-purple-600 rounded border-slate-300 dark:border-slate-700 focus:ring-purple-500">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Seller</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/20 cursor-pointer transition-all">
                        <input type="checkbox" name="roles[]" value="admin" checked class="w-4.5 h-4.5 text-amber-600 rounded border-slate-300 dark:border-slate-700 focus:ring-amber-500">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Admin</span>
                    </label>
                </div>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1.5">Centang role yang boleh membeli dengan harga diskon.</p>
            </div>

            <!-- Potongan Harga -->
            <div>
                <label for="create_discount_amount" class="block text-xs font-bold text-slate-500 uppercase mb-2">Potongan Diskon (Rp)</label>
                <input type="number" id="create_discount_amount" name="discount_amount" required min="1" placeholder="Contoh: 3000" oninput="updateCreateCalculation()" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Preview Kalkulasi -->
            <div id="createPreviewBox" class="p-4 bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl space-y-1.5">
                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Harga Normal:</span>
                    <span id="create_normal_price" class="font-bold text-slate-700 dark:text-slate-300">Rp 0</span>
                </div>
                <div class="flex justify-between text-xs text-rose-500 dark:text-rose-400">
                    <span>Potongan Diskon:</span>
                    <span id="create_preview_discount" class="font-bold">- Rp 0</span>
                </div>
                <div class="flex justify-between text-sm pt-1.5 border-t border-blue-200 dark:border-blue-900 font-black">
                    <span class="text-slate-800 dark:text-slate-200">Harga Setelah Diskon:</span>
                    <span id="create_final_price" class="text-emerald-600 dark:text-emerald-400">Rp 0</span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="button" onclick="toggleCreateModal(false)" class="px-5 py-3 rounded-2xl font-bold text-sm text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                    Simpan Aturan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT DISCOUNT MODAL -->
<div id="editDiscountModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 p-5 sm:p-8 shadow-2xl relative">
        <button onclick="toggleEditModal(false)" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight mb-2">Edit Aturan Diskon</h3>
        <p class="text-xs text-slate-400 dark:text-slate-500 mb-6">Ubah target role atau nominal potongan harga untuk produk ini.</p>

        <form id="editDiscountForm" action="" method="POST" class="space-y-5">
            @csrf
            
            <!-- Detail Produk (Readonly) -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl">
                <span class="block text-xs font-bold text-slate-400 uppercase">Produk</span>
                <span id="edit_product_name" class="font-extrabold text-base text-slate-800 dark:text-slate-100 block mt-0.5">Nama Produk</span>
                <span class="text-xs text-slate-500 mt-1 block">Harga Normal: <strong id="edit_product_price" class="text-slate-700 dark:text-slate-300">Rp 0</strong></span>
            </div>

            <!-- Role Checkbox -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2.5">Pilih Role Penerima Diskon</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center space-x-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/20 cursor-pointer transition-all">
                        <input type="checkbox" id="edit_role_buyer" name="roles[]" value="buyer" class="w-4.5 h-4.5 text-blue-600 rounded border-slate-300 dark:border-slate-700 focus:ring-blue-500">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Buyer</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/20 cursor-pointer transition-all">
                        <input type="checkbox" id="edit_role_seller" name="roles[]" value="seller" class="w-4.5 h-4.5 text-purple-600 rounded border-slate-300 dark:border-slate-700 focus:ring-purple-500">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Seller</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 hover:bg-blue-50 dark:hover:bg-blue-950/20 cursor-pointer transition-all">
                        <input type="checkbox" id="edit_role_admin" name="roles[]" value="admin" class="w-4.5 h-4.5 text-amber-600 rounded border-slate-300 dark:border-slate-700 focus:ring-amber-500">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Admin</span>
                    </label>
                </div>
            </div>

            <!-- Potongan Harga -->
            <div>
                <label for="edit_discount_amount" class="block text-xs font-bold text-slate-500 uppercase mb-2">Potongan Diskon Baru (Rp)</label>
                <input type="number" id="edit_discount_amount" name="discount_amount" required min="1" oninput="updateEditCalculation()" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white focus:outline-none rounded-2xl text-sm font-medium text-slate-800 dark:text-slate-100 transition-all duration-200">
            </div>

            <!-- Preview Kalkulasi -->
            <div id="editPreviewBox" class="p-4 bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl space-y-1.5">
                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Harga Normal:</span>
                    <span id="edit_preview_normal_price" class="font-bold text-slate-700 dark:text-slate-300">Rp 0</span>
                </div>
                <div class="flex justify-between text-xs text-rose-500 dark:text-rose-400">
                    <span>Potongan Diskon:</span>
                    <span id="edit_preview_discount" class="font-bold">- Rp 0</span>
                </div>
                <div class="flex justify-between text-sm pt-1.5 border-t border-blue-200 dark:border-blue-900 font-black">
                    <span class="text-slate-800 dark:text-slate-200">Harga Setelah Diskon:</span>
                    <span id="edit_final_price" class="text-emerald-600 dark:text-emerald-400">Rp 0</span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="button" onclick="toggleEditModal(false)" class="px-5 py-3 rounded-2xl font-bold text-sm text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                    Perbarui Aturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentEditProductPrice = 0;

    function toggleCreateModal(show) {
        const modal = document.getElementById('createDiscountModal');
        if (show) {
            modal.classList.remove('hidden');
            updateCreateCalculation();
        } else {
            modal.classList.add('hidden');
        }
    }

    function toggleEditModal(show) {
        const modal = document.getElementById('editDiscountModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function updateCreateCalculation() {
        const productSelect = document.getElementById('create_product_id');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const normalPrice = selectedOption ? parseInt(selectedOption.getAttribute('data-price') || 0) : 0;
        const discountInput = document.getElementById('create_discount_amount');
        const discountAmount = parseInt(discountInput.value || 0);

        const finalPrice = Math.max(0, normalPrice - discountAmount);

        document.getElementById('create_normal_price').innerText = 'Rp ' + normalPrice.toLocaleString('id-ID');
        document.getElementById('create_preview_discount').innerText = '- Rp ' + discountAmount.toLocaleString('id-ID');
        document.getElementById('create_final_price').innerText = 'Rp ' + finalPrice.toLocaleString('id-ID');
    }

    function updateEditCalculation() {
        const discountInput = document.getElementById('edit_discount_amount');
        const discountAmount = parseInt(discountInput.value || 0);
        const finalPrice = Math.max(0, currentEditProductPrice - discountAmount);

        document.getElementById('edit_preview_normal_price').innerText = 'Rp ' + currentEditProductPrice.toLocaleString('id-ID');
        document.getElementById('edit_preview_discount').innerText = '- Rp ' + discountAmount.toLocaleString('id-ID');
        document.getElementById('edit_final_price').innerText = 'Rp ' + finalPrice.toLocaleString('id-ID');
    }

    function openEditModal(discount) {
        const product = discount.product || {};
        currentEditProductPrice = product.price ? parseInt(product.price) : 0;

        document.getElementById('edit_product_name').innerText = product.name || 'Produk ID #' + discount.product_id;
        document.getElementById('edit_product_price').innerText = 'Rp ' + currentEditProductPrice.toLocaleString('id-ID');

        const roles = Array.isArray(discount.roles) ? discount.roles : (typeof discount.roles === 'string' ? JSON.parse(discount.roles || '[]') : []);
        
        document.getElementById('edit_role_buyer').checked = roles.includes('buyer');
        document.getElementById('edit_role_seller').checked = roles.includes('seller');
        document.getElementById('edit_role_admin').checked = roles.includes('admin');

        document.getElementById('edit_discount_amount').value = Math.round(discount.discount_amount);
        document.getElementById('editDiscountForm').action = `/admin/discounts/${discount.id}/update`;

        updateEditCalculation();
        toggleEditModal(true);
    }
</script>
@endsection
