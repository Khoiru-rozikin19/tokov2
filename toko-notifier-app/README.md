# Toko Notifier - Android Background Payment Forwarder (Flutter)

Aplikasi Android berbasis Flutter untuk membaca notifikasi pembayaran (Bank, QRIS, e-Wallet, Pulsa) secara realtime di latar belakang dan meneruskannya ke website Toko VPN & Pulsa Laravel Anda (`https://arizan.my.id/api/v1/payment/callback-notification`).

---

## 🚀 Fitur Unggulan

1. **Anti-Kill 24 Jam Nonstop**:
   - Berjalan sebagai **Android Foreground Service** dengan tipe `dataSync`.
   - Menggunakan `START_STICKY` agar sistem Android otomatis menghidupkan kembali service jika memori HP sempat penuh.
   - Pengecekan dan shortcut langsung untuk menonaktifkan **Battery Optimization** (Doze Mode).
2. **Auto-Start Saat HP Dinyalakan (Boot Completed)**:
   - Terintegrasi dengan `BootCompletedReceiver` (`RECEIVE_BOOT_COMPLETED` & `QUICKBOOT_POWERON`).
   - Aplikasi otomatis aktif kembali setiap kali HP di-reboot tanpa perlu Anda buka manual.
3. **Pilih Aplikasi Pembayaran (Filter Bebas)**:
   - Anda bisa memilih secara spesifik aplikasi apa saja yang notifikasinya ingin dibaca (misalnya: hanya BCA, DANA, GoPay, OVO, ShopeePay, Livin by Mandiri, BRImo, dll.).
   - Notifikasi chat pribadi (WhatsApp, SMS, Telegram) tidak akan mengganggu atau terkirim ke server.
4. **Ekstraksi Nominal Cerdas**:
   - Mendeteksi format rupiah secara dinamis (`Rp 50.009`, `Rp. 10,000`, `IDR 25.000`, `10009`).
5. **Koneksi Stabil & Ringan**:
   - Pengiriman HTTP POST dieksekusi langsung pada native thread Kotlin (`HttpURLConnection`), sehingga notifikasi tetap terkirim seketika meskipun antarmuka Flutter sedang tidak dibuka.
6. **Tombol Test Ping**:
   - Tombol uji coba koneksi langsung ke website untuk memastikan Secret Key Anda sudah cocok. Telegram admin akan otomatis menerima konfirmasi test ping.
7. **Riwayat Log Real-Time**:
   - Menampilkan daftar notifikasi yang berhasil dibaca dan status HTTP pengiriman ke server.

---

## 📁 Struktur Direktori Project

```text
D:\laravel\toko-notifier-app\
├── android\
│   ├── app\
│   │   ├── src\main\
│   │   │   ├── AndroidManifest.xml
│   │   │   └── kotlin\com\tokovpn\notifier\
│   │   │       ├── MainActivity.kt
│   │   │       ├── PaymentNotificationListenerService.kt
│   │   │       ├── ForegroundKeepAliveService.kt
│   │   │       └── BootCompletedReceiver.kt
│   │   └── build.gradle
│   ├── build.gradle
│   └── settings.gradle
├── lib\
│   ├── main.dart
│   ├── models\
│   │   └── notification_log_item.dart
│   ├── screens\
│   │   ├── home_screen.dart
│   │   └── app_selection_screen.dart
│   └── services\
│       └── native_bridge_service.dart
├── pubspec.yaml
└── README.md
```

---

## 🛠️ Cara Build Menjadi File APK

Jika Anda ingin mengompilasi project ini menjadi file `.apk` siap install di HP:

1. Pastikan **Flutter SDK** sudah terpasang di komputer/laptop Anda ([Download Flutter](https://docs.flutter.dev/get-started/install/windows)).
2. Buka terminal (CMD / PowerShell) di folder project:
   ```powershell
   cd D:\laravel\toko-notifier-app
   ```
3. Unduh seluruh package dependensi:
   ```powershell
   flutter pub get
   ```
4. Build APK rilis:
   ```powershell
   flutter build apk --release
   ```
5. File APK siap install akan berada di:
   `D:\laravel\toko-notifier-app\build\app\outputs\flutter-apk\app-release.apk`

---

## 📱 Panduan Pertama Kali Install di HP

Untuk memastikan aplikasi benar-benar **tidak pernah dimatikan** oleh OS Android (terutama merk Xiaomi, Vivo, Oppo, Realme, Samsung):

1. **Buka Aplikasi Toko Notifier**.
2. Klik tombol **Izin Penyadapan Notifikasi** ➔ Aktifkan toggle izin untuk *Toko Notifier*.
3. Klik tombol **Abaikan Optimasi Baterai** ➔ Pilih *Izinkan / Jangan batasi baterai (Unrestricted)*.
4. Di pengaturan sistem HP (Info Aplikasi Toko Notifier):
   - **Auto-start / Mulai Otomatis**: Nyalakan.
   - **Kunci Aplikasi (Lock in Recent Apps)**: Di menu recent apps, tahan aplikasi lalu klik ikon gembok agar tidak terhapus saat Anda membersihkan RAM.
5. Masukkan **API Secret Key** dari web admin Anda, lalu klik **Simpan Konfigurasi**.
6. Klik **Pilih Aplikasi Pembayaran** untuk mencentang bank atau e-wallet yang Anda pakai.

