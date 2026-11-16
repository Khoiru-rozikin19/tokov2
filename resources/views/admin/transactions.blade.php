@extends('layouts.app', ['title' => 'Daftar Transaksi'])

@section('content')
<div class="space-y-6 sm:space-y-10">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Riwayat pesanan pelanggan dan log mutasi masuk</p>
        </div>
        
    <!-- Filters -->
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <a href="{{ route('admin.transactions') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ !$status ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.transactions', ['status' => 'proses']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $status === 'proses' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50' }}">
                Proses
            </a>
            <a href="{{ route('admin.transactions', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $status === 'pending' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50' }}">
                Pending
            </a>
            <a href="{{ route('admin.transactions', ['status' => 'success']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $status === 'success' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50' }}">
                Sukses
            </a>
            <a href="{{ route('admin.transactions', ['status' => 'gagal']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ in_array($status, ['gagal', 'failed']) ? 'bg-rose-600 text-white shadow-md shadow-rose-500/10' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50' }}">
                Gagal
            </a>
            <a href="{{ route('admin.transactions', ['status' => 'expired']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 {{ $status === 'expired' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50' }}">
                Expired
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl text-emerald-700 dark:text-emerald-400 text-xs font-bold">
            🎉 {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 rounded-2xl text-rose-700 dark:text-rose-400 text-xs font-bold">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Orders/Transactions Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-200">Riwayat Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-4.5 px-6">Order ID</th>
                        <th class="py-4.5 px-6">Produk</th>
                        @if(auth()->user()->role === 'admin')
                            <th class="py-4.5 px-6">Seller</th>
                        @endif
                        <th class="py-4.5 px-6">Email / WhatsApp</th>
                        <th class="py-4.5 px-6">Harga Dasar</th>
                        <th class="py-4.5 px-6 text-center">Kode Unik</th>
                        <th class="py-4.5 px-6">Total Tagihan</th>
                        <th class="py-4.5 px-6">Modal & Laba</th>
                        <th class="py-4.5 px-6">Status</th>
                        <th class="py-4.5 px-6">Dibuat Pada</th>
                        @if(auth()->user()->role === 'admin')
                            <th class="py-4.5 px-6 text-center">Aksi H2H</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm font-medium text-slate-700 dark:text-slate-350">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all duration-150">
                            <td class="py-4.5 px-6 font-mono text-xs text-blue-600 dark:text-blue-400 font-bold">
                                {{ $order->id }}
                            </td>
                            <td class="py-4.5 px-6">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $order->product->name ?? 'Produk Dihapus' }}</div>
                                @if($order->target_phone)
                                    <div class="text-[11px] text-slate-400">Tujuan: {{ $order->target_phone }}</div>
                                @endif
                                @if($order->sn)
                                    <div class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400">SN: {{ $order->sn }}</div>
                                @endif
                                @if($order->supplier_response)
                                    <div class="text-[10px] text-slate-400 max-w-xs truncate" title="{{ $order->supplier_response }}">{{ \Illuminate\Support\Str::limit($order->supplier_response, 55) }}</div>
                                @endif
                            </td>
                            @if(auth()->user()->role === 'admin')
                                <td class="py-4.5 px-6 text-slate-600 dark:text-slate-400 text-xs font-semibold">
                                    {{ $order->product && $order->product->seller ? $order->product->seller->name : 'Admin Utama' }}
                                </td>
                            @endif
                            <td class="py-4.5 px-6 text-xs">{{ $order->email_or_whatsapp }}</td>
                            <td class="py-4.5 px-6 text-slate-400">Rp {{ number_format($order->base_amount, 0, ',', '.') }}</td>
                            <td class="py-4.5 px-6 text-center text-xs font-mono text-amber-600 dark:text-amber-400 font-bold">
                                +{{ $order->unique_code }}
                            </td>
                            <td class="py-4.5 px-6 font-extrabold text-slate-800 dark:text-slate-100">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4.5 px-6">
                                @php
                                    $orderCost = $order->getEffectiveCostPrice();
                                    $orderProfit = $order->getEffectiveProfit();
                                @endphp
                                <div class="flex flex-col space-y-0.5 text-xs">
                                    <span class="text-slate-500 dark:text-slate-400">Modal: Rp {{ number_format($orderCost, 0, ',', '.') }}</span>
                                    @if(in_array($order->status, ['success', 'paid', 'sukses', 'proses']))
                                        <span class="font-extrabold {{ $orderProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            Laba: {{ $orderProfit >= 0 ? '+' : '' }}Rp {{ number_format($orderProfit, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Est. Laba: Rp {{ number_format($orderProfit, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4.5 px-6">
                                @if(in_array($order->status, ['success', 'paid', 'sukses']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">
                                        Sukses
                                    </span>
                                @elseif($order->status === 'proses')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400">
                                        Proses
                                    </span>
                                @elseif(in_array($order->status, ['pending', 'pending_manual']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 animate-pulse">
                                        Pending
                                    </span>
                                @elseif(in_array($order->status, ['gagal', 'failed']))
                                    <div class="flex flex-col space-y-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 w-fit">
                                            Gagal
                                        </span>
                                        @if($order->refunded_at)
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">↩️ Sudah Refund</span>
                                        @endif
                                    </div>
                                @elseif(in_array($order->status, ['rejected', 'ditolak']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400">
                                        Ditolak
                                    </span>
                                @elseif($order->status === 'expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 px-6 text-xs text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td class="py-4.5 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if(in_array($order->status, ['proses', 'pending', 'pending_manual']))
                                            <form action="{{ route('admin.transactions.mark_success', $order->id) }}" method="POST" onsubmit="var s = prompt('Masukkan SN / keterangan sukses (opsional):'); if (s === null) return false; this.querySelector('input[name=sn]').value = s; return true;">
                                                @csrf
                                                <input type="hidden" name="sn" value="">
                                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-[11px] font-bold transition whitespace-nowrap" title="Tandai transaksi sukses">
                                                    ✅ Sukses
                                                </button>
                                            </form>
                                        @endif

                                        @if(in_array($order->status, ['proses', 'sukses', 'success', 'paid']) && !$order->refunded_at)
                                            <form action="{{ route('admin.transactions.mark_failed_refund', $order->id) }}" method="POST" onsubmit="if (!confirm('Tandai pesanan {{ $order->id }} sebagai GAGAL dan refund otomatis saldo pembeli?')) return false; var r = prompt('Alasan gagal (opsional):', 'Transaksi gagal di OKEConnect'); if (r === null) return false; this.querySelector('input[name=reason]').value = r; return true;">
                                                @csrf
                                                <input type="hidden" name="reason" value="">
                                                <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-[11px] font-bold transition whitespace-nowrap" title="Tandai gagal dan refund saldo pembeli">
                                                    ↩️ Gagal & Refund
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 11 : 10 }}" class="py-10 text-center text-slate-400">
                                Tidak ada transaksi yang cocok dengan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination links -->
        <div class="p-6 border-t border-slate-100 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <!-- Top Up Saldo History Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-200">Riwayat Top Up Saldo</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi pengisian saldo pelanggan via QRIS</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-4.5 px-6">ID Top Up</th>
                        <th class="py-4.5 px-6">Email / WhatsApp</th>
                        <th class="py-4.5 px-6">Nominal Saldo</th>
                        <th class="py-4.5 px-6 text-center">Kode Unik</th>
                        <th class="py-4.5 px-6">Total Tagihan</th>
                        <th class="py-4.5 px-6">Status</th>
                        <th class="py-4.5 px-6">Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm font-medium text-slate-700 dark:text-slate-350">
                    @forelse($topupOrders as $topup)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all duration-150">
                            <td class="py-4.5 px-6 font-mono text-xs text-blue-600 dark:text-blue-400 font-bold">
                                {{ $topup->id }}
                            </td>
                            <td class="py-4.5 px-6 text-xs">{{ $topup->email_or_whatsapp }}</td>
                            <td class="py-4.5 px-6 text-emerald-600 dark:text-emerald-400 font-bold">+Rp {{ number_format($topup->base_amount, 0, ',', '.') }}</td>
                            <td class="py-4.5 px-6 text-center text-xs font-mono text-amber-600 dark:text-amber-400 font-bold">
                                +{{ $topup->unique_code }}
                            </td>
                            <td class="py-4.5 px-6 font-extrabold text-slate-800 dark:text-slate-100">
                                Rp {{ number_format($topup->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4.5 px-6">
                                @if(in_array($topup->status, ['success', 'paid', 'sukses']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">
                                        Sukses
                                    </span>
                                @elseif(in_array($topup->status, ['pending', 'pending_manual']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 animate-pulse">
                                        Pending
                                    </span>
                                @elseif(in_array($topup->status, ['rejected', 'ditolak']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400">
                                        Ditolak
                                    </span>
                                @elseif($topup->status === 'expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        {{ ucfirst($topup->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 px-6 text-xs text-slate-400">{{ $topup->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                Belum ada riwayat top up saldo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($topupOrders, 'hasPages') && $topupOrders->hasPages())
            <div class="p-6 border-t border-slate-100 dark:border-slate-800">
                {{ $topupOrders->links() }}
            </div>
        @endif
    </div>
    @endif

    <!-- Payment Logs (Callbacks from Android App) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 dark:text-slate-200">Log Callback Notifikasi Android</h3>
                <p class="text-xs text-slate-400 mt-0.5">Callback POST raw JSON dari pembaca notifikasi eksternal</p>
            </div>
            <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl text-slate-500 uppercase tracking-wider">Maks 20 Log Terakhir</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-4.5 px-6">ID Log</th>
                        @if(auth()->user()->role === 'admin')
                            <th class="py-4.5 px-6">Seller</th>
                        @endif
                        <th class="py-4.5 px-6">Nominal Diterima</th>
                        <th class="py-4.5 px-6">Teks Raw Notifikasi</th>
                        <th class="py-4.5 px-6">Kecocokan Pesanan</th>
                        <th class="py-4.5 px-6">Diterima Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm font-medium text-slate-700 dark:text-slate-350">
                    @forelse($paymentLogs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all duration-150">
                            <td class="py-4.5 px-6 font-mono text-xs text-slate-400">#{{ $log->id }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td class="py-4.5 px-6 text-slate-600 dark:text-slate-400 text-xs font-semibold">
                                    {{ $log->order && $log->order->product && $log->order->product->seller ? $log->order->product->seller->name : 'N/A' }}
                                </td>
                            @endif
                            <td class="py-4.5 px-6 text-emerald-600 font-extrabold">Rp {{ number_format($log->amount, 0, ',', '.') }}</td>
                            <td class="py-4.5 px-6">
                                <span class="text-xs font-mono font-medium max-w-sm block break-all text-slate-500 dark:text-slate-400">
                                    {{ $log->raw_text }}
                                </span>
                            </td>
                            <td class="py-4.5 px-6">
                                @if($log->matched_order_id)
                                    <span class="inline-flex items-center space-x-1 text-xs text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/20 px-2 py-0.5 rounded-lg border border-emerald-100 dark:border-emerald-900/30">
                                        <span>Cocok:</span>
                                        <span class="font-mono">{{ $log->matched_order_id }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-xs text-slate-400 bg-slate-50 dark:bg-slate-800/30 px-2 py-0.5 rounded-lg border border-slate-100 dark:border-slate-800">
                                        Tidak Cocok
                                    </span>
                                @endif
                            </td>
                            <td class="py-4.5 px-6 text-xs text-slate-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                Belum ada callback notifikasi masuk dari Android.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
