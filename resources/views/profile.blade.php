@extends('layouts.app', ['title' => 'Profil & Detail Akun'])

@section('content')
@php
    $activeSection = request()->query('section', session('open_section', $errors->any() ? 'menu' : 'profil'));
@endphp
<div class="max-w-3xl mx-auto space-y-6 sm:space-y-8">
    
    <!-- Profile Header / Initial Avatar -->
    <div class="flex flex-col items-center justify-center space-y-4 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-blue-600 flex items-center justify-center text-white font-black text-4xl sm:text-5xl shadow-xl shadow-blue-500/20 select-none">
            {{ Auth::check() ? strtoupper(substr($user->name, 0, 1)) : 'G' }}
        </div>
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-850 dark:text-slate-100 tracking-tight">{{ $user->name }}</h2>
            <div class="mt-1.5 flex flex-wrap items-center justify-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400">
                    Role: {{ ucfirst($user->role) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-350 font-mono">
                    ID Akun: {{ $user->getWebsiteId() }}
                </span>
                @if($user->is_verified)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">
                        Terverifikasi
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Error/Validation Alerts -->
    @if($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-400 rounded-2xl text-sm space-y-1 shadow-sm">
            @foreach($errors->all() as $error)
                <p class="font-medium">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Toggle Buttons (Kiri: Profil | Kanan: Menu) -->
    <div class="grid grid-cols-2 gap-2 sm:gap-3 bg-slate-100 dark:bg-slate-800/80 p-1.5 rounded-2xl border border-slate-200/60 dark:border-slate-800">
        <button type="button" id="btnTabProfil" onclick="switchProfileMainTab('profil')" class="py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center justify-center {{ $activeSection === 'profil' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            <span>Profil</span>
        </button>
        <button type="button" id="btnTabMenu" onclick="switchProfileMainTab('menu')" class="py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center justify-center {{ $activeSection === 'menu' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
            <span>Menu</span>
        </button>
    </div>

    <!-- ========================================================= -->
    <!-- TAB KIRI: PROFIL (Read-Only / Hanya Bisa Dibaca)          -->
    <!-- ========================================================= -->
    <div id="sectionTabProfil" class="{{ $activeSection === 'profil' ? '' : 'hidden' }} bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-850 dark:text-slate-100">Informasi Akun</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Detail data profil akun Anda</p>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- 1. Nama -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80">
                <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nama</span>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100 break-words">{{ $user->name }}</p>
            </div>

            <!-- 2. Email -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80">
                <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Email</span>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100 break-all">{{ $user->email }}</p>
            </div>

            <!-- 3. Nomor Hp/Wa -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80">
                <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nomor HP / WA</span>
                <p class="mt-1 text-sm font-bold font-mono text-slate-800 dark:text-slate-100">{{ $user->phone ?: '-' }}</p>
            </div>

            <!-- 4. Jenis Kelamin -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80">
                <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Jenis Kelamin</span>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $user->gender ?: 'Belum diatur' }}</p>
            </div>

            <!-- 5. Role -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80">
                <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Role</span>
                <p class="mt-1 text-sm font-bold text-blue-600 dark:text-blue-400">{{ ucfirst($user->role) }}</p>
            </div>

            <!-- 6. Tanggal Mendaftar -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80">
                <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tanggal Mendaftar</span>
                <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">
                    {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                </p>
            </div>

            <!-- 7. Status Akun -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-150 dark:border-slate-800/80 sm:col-span-2 flex items-center justify-between">
                <div>
                    <span class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status Akun</span>
                    <p class="mt-1 text-sm font-bold {{ $user->is_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $user->is_verified ? 'Aktif (Terverifikasi)' : 'Belum Terverifikasi' }}
                    </p>
                </div>
                @if($user->is_verified)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400">
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400">
                        Pending
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- TAB KANAN: MENU (Transaksi, Edit Profil, Ubah Password)   -->
    <!-- ========================================================= -->
    <div id="sectionTabMenu" class="{{ $activeSection === 'menu' ? '' : 'hidden' }} space-y-6">
        
        <!-- 1. Transaksi -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-850 dark:text-slate-100">Transaksi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Total pesanan produk yang pernah Anda buat</p>
                </div>
            </div>
            <div class="flex items-center justify-between sm:justify-end space-x-4 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-slate-800">
                <div class="text-left sm:text-right">
                    <span class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($totalTransactionsCount ?? 0, 0, ',', '.') }}</span>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block">Total Transaksi</span>
                </div>
                <a href="{{ route('orders.history') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                    Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- 2. Edit Profil -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                @csrf
                <div class="pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-850 dark:text-slate-100">Edit Profil</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi nama, email, nomor HP/WA, dan jenis kelamin Anda</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama -->
                    <div class="space-y-2">
                        <label for="name" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all duration-200 font-medium">
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all duration-200 font-medium">
                    </div>

                    <!-- Nomor HP/WA -->
                    <div class="space-y-2">
                        <label for="phone" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nomor HP / WA</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all duration-200 font-medium font-mono">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-2">
                        <label for="gender" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all duration-200 font-medium">
                            <option value="" {{ old('gender', $user->gender) === null || old('gender', $user->gender) === '' ? 'selected' : '' }}>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('gender', $user->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $user->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Ubah Password -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
            <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                @csrf
                <div class="pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-850 dark:text-slate-100">Ubah Password</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gunakan kombinasi password yang kuat untuk menjaga keamanan akun Anda</p>
                </div>

                <!-- Kolom 1: Password Lama -->
                <div class="space-y-2">
                    <label for="current_password" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Password Lama</label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required placeholder="Masukkan password lama..." class="w-full pl-4 pr-12 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all">
                        <button type="button" onclick="togglePasswordVisibility('current_password', 'eye_current_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" tabindex="-1" aria-label="Tampilkan Password">
                            <svg id="eye_current_password" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Kolom 2: Password Baru -->
                    <div class="space-y-2">
                        <label for="new_password" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password" required placeholder="Minimal 6 karakter..." class="w-full pl-4 pr-12 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all">
                            <button type="button" onclick="togglePasswordVisibility('new_password', 'eye_new_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" tabindex="-1" aria-label="Tampilkan Password">
                                <svg id="eye_new_password" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Kolom 3: Konfirmasi Password Baru -->
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Ulangi password baru..." class="w-full pl-4 pr-12 py-3.5 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100 transition-all">
                            <button type="button" onclick="togglePasswordVisibility('new_password_confirmation', 'eye_new_password_confirmation')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" tabindex="-1" aria-label="Tampilkan Password">
                                <svg id="eye_new_password_confirmation" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Ubah Password: Lupa Password (Teks Biru) & Simpan Perubahan (Kanan) -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <button type="button" onclick="openForgotPasswordModal()" class="text-sm font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline focus:outline-none">
                        Lupa Password
                    </button>

                    <button type="submit" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Upgrade Account Portal (If role is buyer) -->
        @if($user->role === 'buyer')
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
                <h3 class="text-base font-bold text-slate-850 dark:text-slate-205 pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                    </svg>
                    <span>Upgrade Akun Seller</span>
                </h3>
                
                <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-5 bg-slate-50 dark:bg-slate-950/20 p-5 rounded-2xl border border-slate-100/50 dark:border-slate-850">
                    <div class="space-y-1">
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Ajukan Sebagai Seller</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                            Mulai berbisnis dengan meng-upgrade akun Anda untuk dapat menjual produk digital secara langsung di platform kami dan kelola stok Anda sendiri.
                        </p>
                    </div>
                    
                    <div>
                        @if($user->seller_request === 'none')
                            <form action="{{ route('buyer.request-seller') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full md:w-auto px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold shadow-md shadow-blue-500/10 active:scale-95 transition-all duration-200 text-center whitespace-nowrap">
                                    Ajukan Sekarang
                                </button>
                            </form>
                        @elseif($user->seller_request === 'pending')
                            <div class="inline-flex items-center space-x-2.5 px-4 py-3 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-250/20 rounded-2xl text-xs font-bold">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span>
                                <span>Menunggu Persetujuan Admin</span>
                            </div>
                        @elseif($user->seller_request === 'rejected')
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <div class="inline-flex items-center space-x-2.5 px-4 py-3 bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-250/20 rounded-2xl text-xs font-bold">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                                    <span>Pengajuan Terakhir Ditolak</span>
                                </div>
                                <form action="{{ route('buyer.request-seller') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold active:scale-95 transition-all duration-200 text-center whitespace-nowrap">
                                        Ajukan Lagi
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<!-- ========================================================= -->
<!-- MODAL LUPA PASSWORD (RESET VIA OTP WHATSAPP)              -->
<!-- ========================================================= -->
<div id="forgotPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-2xl relative space-y-5">
        <button type="button" onclick="closeForgotPasswordModal()" class="absolute top-5 right-5 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div>
            <h3 class="text-xl font-extrabold text-slate-850 dark:text-slate-100">Reset Password via WhatsApp</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kode OTP akan dikirimkan ke nomor WhatsApp terdaftar Anda: <strong class="font-mono text-slate-700 dark:text-slate-200">{{ $user->phone ?: 'Belum diatur' }}</strong>
            </p>
        </div>

        <div id="forgotPwdAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        <!-- Step 1: Send OTP Button -->
        <div class="flex items-center justify-between gap-3 p-3.5 bg-slate-50 dark:bg-slate-950/40 rounded-2xl border border-slate-150 dark:border-slate-800">
            <span class="text-xs text-slate-500 dark:text-slate-400">Minta kode verifikasi 6 digit:</span>
            <button type="button" id="btnSendForgotOtp" onclick="sendForgotPasswordOtp()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm whitespace-nowrap">
                Kirim OTP WA
            </button>
        </div>

        <!-- Step 2: Input OTP & New Password -->
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label for="forgot_otp" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Kode OTP WhatsApp</label>
                <input type="text" id="forgot_otp" maxlength="6" placeholder="Masukkan 6 digit kode OTP..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:outline-none rounded-2xl text-sm font-mono font-bold tracking-widest text-center text-slate-800 dark:text-slate-100">
            </div>

            <div class="space-y-1.5">
                <label for="forgot_new_password" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Password Baru</label>
                <div class="relative">
                    <input type="password" id="forgot_new_password" placeholder="Minimal 6 karakter..." class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100">
                    <button type="button" onclick="togglePasswordVisibility('forgot_new_password', 'eye_forgot_new_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" tabindex="-1">
                        <svg id="eye_forgot_new_password" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="forgot_new_password_confirmation" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" id="forgot_new_password_confirmation" placeholder="Ulangi password baru..." class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 focus:border-blue-500 focus:outline-none rounded-2xl text-sm text-slate-800 dark:text-slate-100">
                    <button type="button" onclick="togglePasswordVisibility('forgot_new_password_confirmation', 'eye_forgot_new_password_confirmation')" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" tabindex="-1">
                        <svg id="eye_forgot_new_password_confirmation" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2.5">
            <button type="button" onclick="closeForgotPasswordModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 rounded-xl transition">
                Batal
            </button>
            <button type="button" id="btnSubmitForgotReset" onclick="submitForgotPasswordReset()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-500/20 transition">
                Simpan Password Baru
            </button>
        </div>
    </div>
</div>

<script>
    function switchProfileMainTab(tab) {
        const sectionProfil = document.getElementById('sectionTabProfil');
        const sectionMenu = document.getElementById('sectionTabMenu');
        const btnProfil = document.getElementById('btnTabProfil');
        const btnMenu = document.getElementById('btnTabMenu');

        const activeClass = 'py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center justify-center bg-blue-600 text-white shadow-md shadow-blue-500/20';
        const inactiveClass = 'py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200';

        if (tab === 'menu') {
            sectionProfil.classList.add('hidden');
            sectionMenu.classList.remove('hidden');
            btnMenu.className = activeClass;
            btnProfil.className = inactiveClass;
        } else {
            sectionMenu.classList.add('hidden');
            sectionProfil.classList.remove('hidden');
            btnProfil.className = activeClass;
            btnMenu.className = inactiveClass;
        }
    }

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }

    function openForgotPasswordModal() {
        const alertBox = document.getElementById('forgotPwdAlert');
        alertBox.classList.add('hidden');
        document.getElementById('forgot_otp').value = '';
        document.getElementById('forgot_new_password').value = '';
        document.getElementById('forgot_new_password_confirmation').value = '';
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
    }

    function showForgotPwdAlert(message, isSuccess) {
        const alertBox = document.getElementById('forgotPwdAlert');
        alertBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'bg-rose-50', 'text-rose-700', 'border-rose-200');
        if (isSuccess) {
            alertBox.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');
        } else {
            alertBox.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
        }
        alertBox.innerText = message;
    }

    function sendForgotPasswordOtp() {
        const btn = document.getElementById('btnSendForgotOtp');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Mengirim...';

        fetch("{{ route('profile.forgot_password.send_otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = originalText;
            showForgotPwdAlert(data.message || 'Permintaan diproses.', !!data.success);
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerText = originalText;
            showForgotPwdAlert('Terjadi kesalahan jaringan saat mengirim OTP.', false);
        });
    }

    function submitForgotPasswordReset() {
        const otp = document.getElementById('forgot_otp').value.trim();
        const newPassword = document.getElementById('forgot_new_password').value;
        const newPasswordConfirm = document.getElementById('forgot_new_password_confirmation').value;

        const btn = document.getElementById('btnSubmitForgotReset');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Memproses...';

        fetch("{{ route('profile.forgot_password.reset') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                otp: otp,
                new_password: newPassword,
                new_password_confirmation: newPasswordConfirm
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = originalText;
            showForgotPwdAlert(data.message || 'Permintaan diproses.', !!data.success);
            if (data.success) {
                setTimeout(() => {
                    closeForgotPasswordModal();
                    window.location.reload();
                }, 1500);
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerText = originalText;
            showForgotPwdAlert('Terjadi kesalahan jaringan saat mereset password.', false);
        });
    }
</script>
@endsection
