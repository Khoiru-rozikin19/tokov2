<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerController extends Controller
{
    /**
     * Handle requesting Seller upgrade.
     */
    public function requestUpgradeToSeller()
    {
        $user = Auth::user();

        if ($user->role !== 'buyer') {
            return back()->with('error', 'Akun Anda sudah memiliki role ' . ucfirst($user->role) . '.');
        }

        if ($user->seller_request === 'pending') {
            return back()->with('error', 'Pengajuan upgrade Anda sudah dikirim dan berstatus pending.');
        }

        $minBalance = 100000;
        if ($user->getBalance() < $minBalance) {
            return back()->with('error', 'Saldo akun Anda belum mencukupi. Syarat upgrade menjadi Seller adalah memiliki saldo minimal Rp ' . number_format($minBalance, 0, ',', '.') . '. Silakan lakukan Top Up saldo terlebih dahulu.');
        }

        $user->update([
            'seller_request' => 'pending',
        ]);

        return back()->with('success', 'Permohonan upgrade menjadi Seller berhasil dikirim! Menunggu persetujuan Admin.');
    }
}
