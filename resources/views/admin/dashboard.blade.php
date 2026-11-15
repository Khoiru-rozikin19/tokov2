@extends('layouts.app', ['title' => 'Dashboard Seller'])

@section('content')
<div class="space-y-6 sm:space-y-10">
    
    <!-- Alert Success/Error -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl text-emerald-800 dark:text-emerald-400 text-sm flex items-center space-x-2">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 rounded-2xl text-rose-800 dark:text-rose-400 text-sm flex items-center space-x-2">
            <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Welcome Banner -->
    <div class="border-b border-slate-200 dark:border-slate-800 pb-5">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
            Selamat Datang di Portal Seller, {{ Auth::user()->name ?? 'Admin Utama' }}!
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Kelola produk Anda, kelola stok akun, dan pantau penghasilan penjualan Anda secara langsung.
        </p>
    </div>

    <!-- Row 1: Hero Cards (Saldo Orderkuota H2H & Total Keuntungan Bersih) -->
    <div class="grid grid-cols-1 {{ auth()->user()->role === 'admin' ? 'md:grid-cols-2' : 'md:grid-cols-1' }} gap-6">
        
        @if(auth()->user()->role === 'admin')
            <!-- Hero Card 1: Saldo Orderkuota (Deep Blue) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-3xl p-6 sm:p-7 flex flex-col justify-between shadow-xl shadow-blue-600/15 min-h-44">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-blue-100 uppercase tracking-wider">Saldo Orderkuota (H2H)</span>
                            <button id="refreshOrderkuotaBtn" class="p-1 rounded-lg bg-white/10 text-blue-100 hover:bg-white/20 hover:text-white transition-colors duration-150 focus:outline-none" title="Refresh Saldo Real-time">
                                <svg id="refreshIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                                </svg>
                            </button>
                        </div>
                        <span id="orderkuotaBalanceText" class="text-3xl font-black mt-2.5 block tracking-tight">
                            @if(is_numeric($orderkuotaBalance))
                                Rp {{ number_format($orderkuotaBalance, 0, ',', '.') }}
                            @else
                                {{ $orderkuotaBalance }}
                            @endif
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-5 pt-3 border-t border-white/15 text-xs text-blue-100">
                    <span>Sisa Amunisi Modal di Supplier OKEConnect</span>
                    <span class="font-semibold text-white/90">Real-time H2H</span>
                </div>
            </div>
        @endif

        <!-- Hero Card 2: Total Keuntungan Bersih (Emerald Green) -->
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 to-teal-600 text-white rounded-3xl p-6 sm:p-7 flex flex-col justify-between shadow-xl shadow-emerald-600/15 min-h-44">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center space-x-2.5">
                        <span class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Total Keuntungan Bersih</span>
                        <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full bg-white/20 text-white">
                            {{ $totalRevenue > 0 ? round(($totalNetProfit / $totalRevenue) * 100, 1) : 0 }}% Margin
                        </span>
                    </div>
                    <span class="text-3xl font-black mt-2.5 block tracking-tight">Rp {{ number_format($totalNetProfit ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center justify-between mt-5 pt-3 border-t border-white/15 text-xs text-emerald-100">
                <span>Omzet dikurangi Modal H2H ({{ $totalSalesCount }} transaksi)</span>
                <span class="font-bold text-white">Hari ini: +Rp {{ number_format($todayProfit ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Row 2: Secondary Metrics (Omzet Kotor, Modal H2H Terpakai, & Status Stok) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 3: Omzet / Pendapatan Kotor (White with Amber/Orange Accent) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 flex flex-col justify-between shadow-sm min-h-44">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Omzet (Pendapatan Kotor)</span>
                    <span class="text-2xl font-black mt-2 block tracking-tight text-blue-600 dark:text-blue-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-4 leading-normal">
                <span class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Total pembayaran masuk dari pembeli.</span>
                </span>
            </div>
        </div>

        <!-- Card 4: Modal H2H Terpakai / COGS (White with Rose Accent) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 flex flex-col justify-between shadow-sm min-h-44">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Total Modal H2H Terpakai</span>
                    <span class="text-2xl font-black mt-2 block tracking-tight text-rose-600 dark:text-rose-400">Rp {{ number_format($totalCost ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-4 leading-normal">
                <span class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                    <span>Akumulasi saldo H2H terpotong untuk produk terjual.</span>
                </span>
            </div>
        </div>

        <!-- Card 5: Status Stok & Garansi (White) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 flex flex-col justify-between shadow-sm min-h-44">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Status Stok & Garansi</span>
                    <span class="text-[11px] font-bold text-slate-500">Tertahan: Rp {{ number_format($heldBalance, 0, ',', '.') }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                    <div class="bg-blue-50 dark:bg-blue-950/20 py-2.5 rounded-xl border border-blue-100/50 dark:border-blue-950/50">
                        <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400 block">{{ $readyStockCount }}</span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 uppercase font-bold">Ready</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-950/20 py-2.5 rounded-xl border border-amber-100/50 dark:border-amber-950/50">
                        <span class="text-sm font-extrabold text-amber-600 dark:text-amber-400 block">0</span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 uppercase font-bold">Karantina</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-950/20 py-2.5 rounded-xl border border-emerald-100/50 dark:border-emerald-950/50">
                        <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 block">{{ $totalSalesCount }}</span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 uppercase font-bold">Terjual</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Period & Payment Breakdown (Hari Ini, Bulan Ini, & Metode Pembayaran) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ringkasan Hari Ini -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">Performa Hari Ini</span>
                    <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">{{ now()->translatedFormat('d F Y') }}</h4>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                    {{ $todaySalesCount ?? 0 }} Trx
                </span>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Omzet Kotor:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Modal H2H Terpakai:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400">- Rp {{ number_format($todayCost ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-sm">
                    <span class="font-bold text-slate-700 dark:text-slate-200">Laba Bersih Hari Ini:</span>
                    <span class="font-black text-emerald-600 dark:text-emerald-400">+Rp {{ number_format($todayProfit ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Bulan Ini -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 block">Performa Bulan Ini</span>
                    <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">{{ now()->translatedFormat('F Y') }}</h4>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">
                    {{ $monthSalesCount ?? 0 }} Trx
                </span>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Omzet Kotor:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($monthRevenue ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Modal H2H Terpakai:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400">- Rp {{ number_format($monthCost ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-sm">
                    <span class="font-bold text-slate-700 dark:text-slate-200">Laba Bersih Bulan Ini:</span>
                    <span class="font-black text-emerald-600 dark:text-emerald-400">+Rp {{ number_format($monthProfit ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Rincian Sumber Pembayaran -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Sumber Pembayaran</span>
                    <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">QRIS vs Saldo Akun</h4>
                </div>
            </div>
            <div class="space-y-3 text-xs">
                <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-700 dark:text-slate-200 block">📱 QRIS Langsung ({{ $qrisCount ?? 0 }}x)</span>
                        <span class="text-[11px] text-slate-400">Omzet: Rp {{ number_format($qrisRevenue ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400">+Rp {{ number_format($qrisProfit ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-700 dark:text-slate-200 block">💳 Saldo Akun ({{ $balanceCount ?? 0 }}x)</span>
                        <span class="text-[11px] text-slate-400">Omzet: Rp {{ number_format($balanceRevenue ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400">+Rp {{ number_format($balanceProfit ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section (2 Columns: 70% and 30%) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Daily Earnings Line Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Tren Omzet & Keuntungan Bersih Harian</span>
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Perbandingan omzet kotor vs laba bersih dalam 7 hari terakhir</p>
                </div>
                
                <span class="text-xs font-semibold px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full">7 Hari Terakhir</span>
            </div>
            
            <div class="relative h-72">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        <!-- Order Ratios Donut Chart -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    <span>Rasio Status Order</span>
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Persentase sukses vs batal/expired</p>
            </div>

            <div class="relative h-56 flex items-center justify-center">
                <canvas id="ratioChart" class="max-h-full"></canvas>
                <!-- Central Total Overlay -->
                <div class="absolute flex flex-col items-center justify-center">
                    <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalOrdersCount }}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Order</span>
                </div>
            </div>

            <div class="mt-6 flex justify-around text-xs border-t border-slate-100 dark:border-slate-800 pt-4">
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 block"></span>
                    <span class="text-slate-600 dark:text-slate-400">Sukses ({{ $donutData[0] }})</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 block"></span>
                    <span class="text-slate-600 dark:text-slate-400">Pending ({{ $donutData[1] }})</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 block"></span>
                    <span class="text-slate-600 dark:text-slate-400">Expired ({{ $donutData[2] }})</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Line Chart Settings
        const earningsCtx = document.getElementById('earningsChart').getContext('2d');
        const isDark = document.documentElement.classList.contains('dark');
        
        const gridColor = isDark ? '#1e293b' : '#f1f5f9';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        // Prepare line chart
        const earningsChart = new Chart(earningsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Omzet Kotor (Rp)',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#2563eb',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Laba Bersih (Rp)',
                        data: {!! json_encode($chartProfitData ?? []) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: textColor,
                            usePointStyle: true,
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            callback: function(value) {
                                return 'Rp ' + (value >= 1000 ? (value/1000) + 'k' : value);
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            font: { family: 'Plus Jakarta Sans', size: 10 }
                        }
                    }
                }
            }
        });

        // Donut Chart Settings
        const ratioCtx = document.getElementById('ratioChart').getContext('2d');
        const successVal = {{ $donutData[0] }};
        const pendingVal = {{ $donutData[1] }};
        const expiredVal = {{ $donutData[2] }};

        const ratioChart = new Chart(ratioCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sukses', 'Pending', 'Expired'],
                datasets: [{
                    data: [successVal, pendingVal, expiredVal],
                    backgroundColor: [
                        '#10b981', // green
                        '#3b82f6', // blue
                        '#f43f5e'  // rose
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Dynamic theme updates for charts
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === "class") {
                    const isDarkNow = document.documentElement.classList.contains('dark');
                    const newGridColor = isDarkNow ? '#1e293b' : '#f1f5f9';
                    const newTextColor = isDarkNow ? '#94a3b8' : '#64748b';

                    // Update config
                    earningsChart.options.scales.y.grid.color = newGridColor;
                    earningsChart.options.scales.y.ticks.color = newTextColor;
                    earningsChart.options.scales.x.ticks.color = newTextColor;
                    earningsChart.update();
                }
            });
        });

        // Real-time Orderkuota Balance Fetcher (Admin Only)
        const refreshBtn = document.getElementById('refreshOrderkuotaBtn');
        const refreshIcon = document.getElementById('refreshIcon');
        const balanceText = document.getElementById('orderkuotaBalanceText');

        function fetchRealtimeBalance() {
            if (!refreshIcon || !refreshBtn || !balanceText) return;
            
            refreshIcon.classList.add('animate-spin');
            refreshBtn.disabled = true;

            fetch("{{ route('admin.orderkuota_balance') }}")
                .then(res => res.json())
                .then(data => {
                    refreshIcon.classList.remove('animate-spin');
                    refreshBtn.disabled = false;
                    if (data.success) {
                        balanceText.innerText = data.formatted_balance;
                    } else {
                        balanceText.innerText = data.balance;
                    }
                })
                .catch(err => {
                    refreshIcon.classList.remove('animate-spin');
                    refreshBtn.disabled = false;
                    console.error('Failed to fetch balance:', err);
                });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', fetchRealtimeBalance);
            // Auto-fetch on page load for real-time update
            fetchRealtimeBalance();
        }

        observer.observe(document.documentElement, { attributes: true });
    });
</script>
@endsection
