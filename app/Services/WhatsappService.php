<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $apiUrl;
    protected $enabled;
    protected $otpEnabled;
    protected $groupId;
    protected $adminPhone;

    public function __construct()
    {
        $this->apiUrl = rtrim(Setting::get('whatsapp_bot_api_url', 'http://127.0.0.1:3000'), '/');
        $this->enabled = Setting::get('whatsapp_bot_enabled', '0') === '1';
        $this->otpEnabled = Setting::get('whatsapp_bot_otp_enabled', '0') === '1';
        $this->groupId = Setting::get('whatsapp_bot_group_id', '');
        $this->adminPhone = Setting::get('whatsapp_bot_admin_phone', '');
    }

    public function getAdminPhone(): ?string
    {
        return $this->adminPhone;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isOtpEnabled(): bool
    {
        return $this->enabled && $this->otpEnabled;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Get list of all WhatsApp groups the bot is in
     */
    public function getGroups(): array
    {
        if (!$this->enabled) {
            return [];
        }

        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/groups");
            if ($response->successful()) {
                return $response->json('groups') ?? [];
            }
        } catch (\Exception $e) {
            Log::error("WhatsappService failed to fetch groups: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Send message to personal JID
     */
    public function sendGenericMessage($phone, $message): bool
    {
        if (!$this->enabled) {
            Log::info("WhatsappService: Bot is disabled. Skipping message to {$phone}.");
            return false;
        }

        try {
            $response = Http::timeout(8)
                ->withoutVerifying()
                ->post("{$this->apiUrl}/send-message", [
                    'phone' => $phone,
                    'message' => $message
                ]);

            if ($response->failed()) {
                Log::error("WhatsappService sendGenericMessage Failed: " . $response->body());
                if ($response->status() === 400 && str_contains($response->body(), 'tidak terdaftar')) {
                    throw new \Exception("Nomor WhatsApp tidak terdaftar atau tidak aktif.");
                }
                return false;
            }

            return $response->json('success', false);
        } catch (\Exception $e) {
            Log::error("WhatsappService sendGenericMessage Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send message to WhatsApp Group
     */
    public function sendGroupMessage($message): bool
    {
        if (!$this->enabled || empty($this->groupId)) {
            Log::info("WhatsappService: Bot or Group ID is not configured. Skipping group message.");
            return false;
        }

        try {
            $response = Http::timeout(8)
                ->withoutVerifying()
                ->post("{$this->apiUrl}/send-group-message", [
                    'groupId' => $this->groupId,
                    'message' => $message
                ]);

            if ($response->failed()) {
                Log::error("WhatsappService sendGroupMessage Failed: " . $response->body());
                return false;
            }

            return $response->json('success', false);
        } catch (\Exception $e) {
            Log::error("WhatsappService sendGroupMessage Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send message to a specific WhatsApp Group
     */
    public function sendGroupMessageTo($groupId, $message): bool
    {
        if (!$this->enabled || empty($groupId)) {
            Log::info("WhatsappService: Bot is disabled or target Group JID is empty.");
            return false;
        }

        try {
            $response = Http::timeout(8)
                ->withoutVerifying()
                ->post("{$this->apiUrl}/send-group-message", [
                    'groupId' => $groupId,
                    'message' => $message
                ]);

            if ($response->failed()) {
                Log::error("WhatsappService sendGroupMessageTo Failed: " . $response->body());
                return false;
            }

            return $response->json('success', false);
        } catch (\Exception $e) {
            Log::error("WhatsappService sendGroupMessageTo Exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendOtp($phone, $otpCode): bool
    {
        $templates = [
            "🔐 *KODE OTP RZK STORE*\n\nKode OTP verifikasi akun Anda adalah: *{$otpCode}*\nBerlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun demi keamanan akun Anda.",
            "🛡️ *VERIFIKASI RZK STORE*\n\nMasukkan kode keamanan *{$otpCode}* pada halaman verifikasi website untuk mengaktifkan akun Anda. Kode ini kedaluwarsa dalam 5 menit.",
            "🔑 *KODE KEAMANAN RZK STORE*\n\nBerikut adalah kode OTP Anda: *{$otpCode}*.\nSegera masukkan kode ini untuk melanjutkan proses registrasi. Tetap jaga kerahasiaan kode Anda.",
            "🌟 *AKTIVASI AKUN RZK STORE*\n\nTerima kasih telah mendaftar! Kode verifikasi Anda adalah: *{$otpCode}*.\nJangan berikan kode ini kepada petugas atau pihak lain."
        ];

        // Pilih template secara acak
        $template = $templates[array_rand($templates)];

        // Tambahkan pengenal unik (reference code & timestamp) di bagian bawah agar setiap pesan unik bagi server WhatsApp
        $refCode = strtoupper(substr(md5(uniqid()), 0, 4));
        $timestamp = date('H:i:s');
        $message = "{$template}\n\n_Ref: [{$refCode}] • Waktu: {$timestamp}_";

        $sent = $this->sendGenericMessage($phone, $message);

        if ($sent) {
            $adminAlert = "🔐 *AKTIVITAS OTP WHATSAPP*\n"
                        . "📱 *Nomor Tujuan:* +{$phone}\n"
                        . "🔑 *Kode OTP:* `{$otpCode}`\n"
                        . "⏰ *Waktu:* {$timestamp}\n\n"
                        . "ℹ️ _Pesan otomatis sistem RZK Store._";
            $groupAlert = "🔐 *AKTIVITAS OTP WHATSAPP*\n"
                        . "📱 *Nomor Tujuan:* +{$phone}\n"
                        . "🔑 *Kode OTP:* `******` _(disensor untuk keamanan grup)_\n"
                        . "⏰ *Waktu:* {$timestamp}\n\n"
                        . "ℹ️ _Pesan otomatis sistem RZK Store._";
            $this->sendAutomatedNotification('user_otp_alert', $adminAlert, $groupAlert);
        }

        return $sent;
    }

    /**
     * Catalog of all automated/condition-triggered messages in the system.
     */
    public static function getAutomatedMessageCatalog(): array
    {
        return [
            [
                'key' => 'okeconnect_status_change',
                'title' => 'Update Status Produk OKEConnect (Open / Close)',
                'badge' => 'Otomatis Sinkron',
                'trigger' => 'Dikirim otomatis saat sinkronisasi OKEConnect (tiap 5 menit / tombol sinkron) mendeteksi status produk berubah Open ↔ Close (tanpa info modal).',
                'default_admin' => '0',
                'default_group' => '1',
                'preview' => "📢 *UPDATE STATUS PRODUK (OPEN / CLOSE)*\n"
                           . "📅 Waktu: 16-11-2026 06:30:00\n\n"
                           . "Terdeteksi perubahan status ketersediaan produk berikut:\n\n"
                           . "• *XL 15GB V1* (XLA15)\n"
                           . "  Status: Open ➔ 🔴 *CLOSE*\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
            [
                'key' => 'okeconnect_price_change',
                'title' => 'Update Harga Modal Supplier OKEConnect',
                'badge' => 'Otomatis Sinkron',
                'trigger' => 'Dikirim otomatis saat sinkronisasi OKEConnect mendeteksi perubahan harga modal H2H dari supplier (beserta peringatan jika modal >= harga jual).',
                'default_admin' => '1',
                'default_group' => '0',
                'preview' => "💰 *UPDATE HARGA MODAL OKECONNECT*\n"
                           . "📅 Waktu: 16-11-2026 06:30:00\n\n"
                           . "Terdeteksi perubahan harga modal dari supplier pada produk berikut:\n\n"
                           . "• *XL 15GB V1* (XLA15)\n"
                           . "  Modal H2H: Rp 14.500 ➔ *Rp 15.000*\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
            [
                'key' => 'okeconnect_manual_stock',
                'title' => 'Laporan Status Stok Produk OKEConnect (Cek Manual)',
                'badge' => 'Tombol Cek Stok',
                'trigger' => 'Dikirim saat Admin menekan tombol "Kirim Cek Stok ke WA Saya" pada panel Bot WhatsApp.',
                'default_admin' => '1',
                'default_group' => '0',
                'preview' => "📊 *STATUS STOK PRODUK OKECONNECT (MANUAL CHECK)*\n"
                           . "📅 Waktu: 16-11-2026 06:30:00\n\n"
                           . "Berikut status terkini produk supplier Anda:\n\n"
                           . "• *XL 15GB V1* (XLA15)\n"
                           . "  Modal: Rp 14.500 | Status: 🟢 *OPEN*\n\n"
                           . "ℹ️ _Pesan permintaan cek stok manual sistem RZK Store._",
            ],
            [
                'key' => 'order_success',
                'title' => 'Notifikasi Transaksi Produk Baru / Pembayaran Sukses',
                'badge' => 'Transaksi Produk',
                'trigger' => 'Dikirim otomatis saat pembelian produk berhasil dibayar via QRIS otomatis, Saldo Akun, atau disetujui Admin.',
                'default_admin' => '0',
                'default_group' => '0',
                'preview' => "✅ *TRANSAKSI PRODUK SUKSES*\n"
                           . "📦 *ID Order:* ORD-XXXXXXXX\n"
                           . "🛍️ *Produk:* XL 15GB V1\n"
                           . "💳 *Metode:* Saldo Akun / QRIS\n"
                           . "💰 *Nominal:* Rp 18.000\n"
                           . "👤 *Pelanggan:* buyer@email.com\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
            [
                'key' => 'preorder_received',
                'title' => 'Notifikasi Pre-Order Masuk & Terproses Otomatis',
                'badge' => 'Pre-Order',
                'trigger' => 'Dikirim otomatis saat Pre-Order diterima (PROSES) atau saat produk supplier kembali OPEN dan Pre-Order otomatis diteruskan (SUKSES).',
                'default_admin' => '0',
                'default_group' => '0',
                'preview' => "⏳ *PRE-ORDER DITERIMA / DIPROSES OTOMATIS*\n"
                           . "📦 *ID Order:* ORD-XXXXXXXX\n"
                           . "🛍️ *Produk:* XL 15GB V1\n"
                           . "📱 *No Tujuan:* 0878xxxxxxxx\n"
                           . "💰 *Nominal:* Rp 18.000\n"
                           . "👤 *Pelanggan:* buyer@email.com\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
            [
                'key' => 'topup_success',
                'title' => 'Notifikasi Top Up Saldo Akun Sukses',
                'badge' => 'Top Up Saldo',
                'trigger' => 'Dikirim otomatis saat top up saldo pengguna via QRIS berhasil diverifikasi dan saldo masuk ke akun.',
                'default_admin' => '0',
                'default_group' => '0',
                'preview' => "💵 *TOP UP SALDO SUKSES*\n"
                           . "📦 *ID Top Up:* TOP-XXXXXXXX\n"
                           . "💰 *Nominal Saldo:* Rp 100.000\n"
                           . "👤 *Pelanggan:* buyer@email.com\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
            [
                'key' => 'user_registration',
                'title' => 'Notifikasi Pendaftaran Akun Baru',
                'badge' => 'Registrasi User',
                'trigger' => 'Dikirim otomatis saat ada pengguna baru mendaftar akun di website.',
                'default_admin' => '0',
                'default_group' => '0',
                'preview' => "🔔 *PENDAFTARAN AKUN BARU*\n"
                           . "👤 *Nama:* Budi Santoso\n"
                           . "📧 *Email:* budi@email.com\n"
                           . "📱 *WhatsApp:* 628123456789\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
            [
                'key' => 'user_otp_alert',
                'title' => 'Salinan Aktivitas Pengiriman Kode OTP User',
                'badge' => 'Keamanan / OTP',
                'trigger' => 'Dikirim otomatis setiap sistem mengirim kode OTP ke WhatsApp user (Registrasi, Lupa Password, Ubah No. WA). OTP utama selalu ke WA user; kode OTP disensor ****** jika dikirim ke Grup.',
                'default_admin' => '0',
                'default_group' => '0',
                'preview' => "🔐 *AKTIVITAS OTP WHATSAPP*\n"
                           . "📱 *Nomor Tujuan:* +628123456789\n"
                           . "🔑 *Kode OTP:* 123456\n"
                           . "⏰ *Waktu:* 06:30:00\n\n"
                           . "ℹ️ _Pesan otomatis sistem RZK Store._",
            ],
        ];
    }

    /**
     * Check whether a target ('admin' or 'group') is enabled for a specific automated message event.
     */
    public function isAutoTargetEnabled(string $eventKey, string $target): bool
    {
        $catalog = collect(self::getAutomatedMessageCatalog())->keyBy('key');
        $item = $catalog->get($eventKey);
        $default = $target === 'admin'
            ? ($item['default_admin'] ?? '0')
            : ($item['default_group'] ?? '0');

        return Setting::get("whatsapp_auto_{$eventKey}_{$target}", $default) === '1';
    }

    /**
     * Get all automated notification target checkboxes keyed by event key.
     */
    public function getAutoNotificationSettings(): array
    {
        $settings = [];
        foreach (self::getAutomatedMessageCatalog() as $item) {
            $key = $item['key'];
            $settings[$key] = [
                'admin' => $this->isAutoTargetEnabled($key, 'admin'),
                'group' => $this->isAutoTargetEnabled($key, 'group'),
            ];
        }
        return $settings;
    }

    /**
     * Dispatch an automated WhatsApp message to checked destinations (WhatsApp Admin and/or Selected Group).
     */
    public function sendAutomatedNotification(string $eventKey, string $adminMessage, ?string $groupMessage = null): array
    {
        $result = ['admin' => false, 'group' => false];

        if (!$this->enabled) {
            Log::info("WhatsappService: Bot is disabled. Skipping automated notification '{$eventKey}'.");
            return $result;
        }

        if ($this->isAutoTargetEnabled($eventKey, 'admin')) {
            $result['admin'] = $this->sendToAdmin($adminMessage);
        }

        if ($this->isAutoTargetEnabled($eventKey, 'group')) {
            $result['group'] = $this->sendGroupMessage($groupMessage ?? $adminMessage);
        }

        return $result;
    }

    /**
     * Format automated WhatsApp message for a completed product order.
     */
    public static function formatOrderSuccessMessage(\App\Models\Order $order, string $sourceLabel = 'Otomatis'): string
    {
        $productName = $order->product ? $order->product->name : 'Produk';
        $method = strtoupper((string) ($order->payment_method ?: 'QRIS'));
        $amountFmt = 'Rp ' . number_format((int) $order->total_amount, 0, ',', '.');
        $targetLine = !empty($order->target_phone) ? "📱 *No Tujuan:* {$order->target_phone}\n" : '';

        return "✅ *TRANSAKSI PRODUK SUKSES ({$sourceLabel})*\n"
             . "📦 *ID Order:* {$order->id}\n"
             . "🛍️ *Produk:* {$productName}\n"
             . $targetLine
             . "💳 *Metode:* {$method}\n"
             . "💰 *Nominal:* {$amountFmt}\n"
             . "👤 *Pelanggan:* {$order->email_or_whatsapp}\n\n"
             . "ℹ️ _Pesan otomatis sistem RZK Store._";
    }

    /**
     * Format automated WhatsApp message for pre-order received or auto-completed.
     */
    public static function formatPreorderMessage(\App\Models\Order $order, bool $completed = false): string
    {
        $productName = $order->product ? $order->product->name : 'Produk';
        $amountFmt = 'Rp ' . number_format((int) $order->total_amount, 0, ',', '.');
        $targetPhone = $order->target_phone ?: '-';
        $header = $completed
            ? "✅ *PRE-ORDER BERHASIL DIPROSES (OTOMATIS)*"
            : "⏳ *PRE-ORDER BARU DITERIMA*";
        $footer = $completed
            ? "Supplier sudah OPEN dan pre-order berhasil diteruskan."
            : "Status *PROSES* — akan diteruskan otomatis saat supplier OPEN.";

        return "{$header}\n"
             . "📦 *ID Order:* {$order->id}\n"
             . "🛍️ *Produk:* {$productName}\n"
             . "📱 *No Tujuan:* {$targetPhone}\n"
             . "💰 *Nominal:* {$amountFmt}\n"
             . "👤 *Pelanggan:* {$order->email_or_whatsapp}\n\n"
             . "{$footer}\n"
             . "ℹ️ _Pesan otomatis sistem RZK Store._";
    }

    /**
     * Format automated WhatsApp message for a verified top-up balance order.
     */
    public static function formatTopupSuccessMessage(\App\Models\Order $order): string
    {
        $amountFmt = 'Rp ' . number_format((int) $order->base_amount, 0, ',', '.');
        $totalFmt = 'Rp ' . number_format((int) $order->total_amount, 0, ',', '.');

        return "💵 *TOP UP SALDO SUKSES*\n"
             . "📦 *ID Top Up:* {$order->id}\n"
             . "💰 *Saldo Masuk:* {$amountFmt} (Bayar: {$totalFmt})\n"
             . "👤 *Pelanggan:* {$order->email_or_whatsapp}\n\n"
             . "ℹ️ _Pesan otomatis sistem RZK Store._";
    }

    /**
     * Format automated WhatsApp message for a newly registered user.
     */
    public static function formatUserRegistrationMessage(\App\Models\User $user): string
    {
        return "🔔 *PENDAFTARAN AKUN BARU*\n"
             . "👤 *Nama:* {$user->name}\n"
             . "📧 *Email:* {$user->email}\n"
             . "📱 *WhatsApp:* {$user->phone}\n\n"
             . "ℹ️ _Pesan otomatis sistem RZK Store._";
    }

    /**
     * Send direct WhatsApp notification to Admin private phone.
     */
    public function sendToAdmin(string $message): bool
    {
        $adminPhone = preg_replace('/[^0-9]/', '', (string) Setting::get('whatsapp_bot_admin_phone', ''));
        if (empty($adminPhone)) {
            return false;
        }

        if (str_starts_with($adminPhone, '0')) {
            $adminPhone = '62' . substr($adminPhone, 1);
        }

        try {
            $response = Http::timeout(8)
                ->withoutVerifying()
                ->post("{$this->apiUrl}/send-message", [
                    'phone' => $adminPhone,
                    'message' => $message
                ]);

            if ($response->failed()) {
                Log::error("WhatsappService sendToAdmin Failed: " . $response->body());
                return false;
            }

            return $response->json('success', false);
        } catch (\Exception $e) {
            Log::error("WhatsappService sendToAdmin Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Baileys connection status and QR code from gateway
     */
    public function getConnectionStatus(): array
    {
        try {
            $response = Http::timeout(5)
                ->withoutVerifying()
                ->get("{$this->apiUrl}/status");

            if ($response->successful()) {
                return [
                    'status' => $response->json('status', 'connecting'),
                    'qr' => $response->json('qr', null),
                    'online' => true
                ];
            }
        } catch (\Exception $e) {
            Log::warning("WhatsappService getConnectionStatus: Gateway is offline/unreachable.");
        }

        return [
            'status' => 'offline',
            'qr' => null,
            'online' => false
        ];
    }

    /**
     * Disconnect/logout the session on the gateway
     */
    public function disconnect(): bool
    {
        try {
            $response = Http::timeout(5)
                ->withoutVerifying()
                ->post("{$this->apiUrl}/disconnect");

            return $response->successful() && $response->json('success', false);
        } catch (\Exception $e) {
            Log::error("WhatsappService disconnect Exception: " . $e->getMessage());
            return false;
        }
    }
}
