import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/notification_log_item.dart';
import '../services/native_bridge_service.dart';
import 'app_selection_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final NativeBridgeService _bridge = NativeBridgeService();
  final TextEditingController _urlCtrl = TextEditingController();
  final TextEditingController _secretCtrl = TextEditingController();

  bool _isEnabled = true;
  bool _hasNotifPermission = false;
  bool _isBatteryIgnored = false;
  Set<String> _selectedApps = {};
  List<Map<String, String>> _installedApps = [];
  final List<NotificationLogItem> _logs = [];

  StreamSubscription? _eventSub;
  bool _isTestingPing = false;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
    _initEventListener();
  }

  @override
  void dispose() {
    _eventSub?.cancel();
    _urlCtrl.dispose();
    _secretCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadInitialData() async {
    final settings = await _bridge.loadSettings();
    final notifPerm = await _bridge.isNotificationPermissionGranted();
    final battPerm = await _bridge.isBatteryOptimizationIgnored();
    final apps = await _bridge.getInstalledApps();

    setState(() {
      _isEnabled = settings['isEnabled'];
      _urlCtrl.text = settings['serverUrl'];
      _secretCtrl.text = settings['secretKey'];
      _selectedApps = settings['selectedApps'];
      _hasNotifPermission = notifPerm;
      _isBatteryIgnored = battPerm;
      _installedApps = apps;
    });

    if (_isEnabled && _hasNotifPermission) {
      await _bridge.startForegroundService();
    }
  }

  void _initEventListener() {
    _eventSub = _bridge.eventStream.listen((event) {
      if (event is Map) {
        final type = event['type'];
        final data = event['data'];

        if (type == 'NOTIFICATION_RECEIVED' && data is Map) {
          setState(() {
            _logs.insert(
              0,
              NotificationLogItem(
                packageName: (data['package_name'] ?? '').toString(),
                rawText: (data['raw_text'] ?? '').toString(),
                amount: int.tryParse(data['amount'].toString()) ?? 0,
                timestamp: DateTime.fromMillisecondsSinceEpoch(
                  int.tryParse(data['timestamp'].toString()) ?? DateTime.now().millisecondsSinceEpoch,
                ),
              ),
            );
          });
        } else if (type == 'CALLBACK_SENT' && data is Map) {
          final amount = int.tryParse(data['amount'].toString()) ?? 0;
          final status = data['http_status'];
          setState(() {
            if (_logs.isNotEmpty && _logs.first.amount == amount) {
              _logs[0] = _logs.first.copyWith(
                isSuccess: true,
                responseInfo: 'HTTP $status Sukses',
              );
            }
          });
        } else if (type == 'CALLBACK_ERROR' && data is Map) {
          final error = data['error']?.toString() ?? 'Error';
          setState(() {
            if (_logs.isNotEmpty) {
              _logs[0] = _logs.first.copyWith(
                isSuccess: false,
                responseInfo: error,
              );
            }
          });
        }
      }
    });
  }

  Future<void> _saveAllSettings() async {
    await _bridge.saveSettings(
      isEnabled: _isEnabled,
      serverUrl: _urlCtrl.text,
      secretKey: _secretCtrl.text,
      selectedApps: _selectedApps,
    );
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Konfigurasi berhasil disimpan!'),
          backgroundColor: Color(0xFF16A34A),
        ),
      );
    }
  }

  Future<void> _testPing() async {
    if (_urlCtrl.text.isEmpty || _secretCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Mohon isi URL Website & API Secret Key terlebih dahulu.'),
          backgroundColor: Color(0xFFDC2626),
        ),
      );
      return;
    }

    setState(() => _isTestingPing = true);
    final result = await _bridge.sendPingTest(
      serverUrl: _urlCtrl.text,
      secretKey: _secretCtrl.text,
    );
    setState(() => _isTestingPing = false);

    if (mounted) {
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Row(
            children: [
              Icon(
                result['success'] ? Icons.check_circle : Icons.error,
                color: result['success'] ? Colors.green : Colors.red,
              ),
              const SizedBox(width: 8),
              Text(result['success'] ? 'Koneksi Berhasil!' : 'Koneksi Gagal'),
            ],
          ),
          content: Text(
            result['success']
                ? 'Server menerima ping uji coba dengan sukses. Telegram admin juga akan menerima konfirmasi.'
                : 'Pesan Error: ${result['message']}\nPastikan URL dan Secret Key sesuai dengan di web admin.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('OK'),
            )
          ],
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFmt = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: const Row(
          children: [
            Icon(Icons.sync_alt, color: Color(0xFF2563EB)),
            SizedBox(width: 10),
            Text(
              'Toko Notifier',
              style: TextStyle(fontWeight: FontWeight.w900, fontSize: 19, color: Color(0xFF0F172A)),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Color(0xFF64748B)),
            onPressed: _loadInitialData,
            tooltip: 'Segarkan Status',
          )
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // 1. Status Izin & Background Health
          _buildHealthStatusCard(),

          const SizedBox(height: 16),

          // 2. Konfigurasi Server & Secret Key
          _buildConfigurationCard(),

          const SizedBox(height: 16),

          // 3. Pemilihan Aplikasi Pembayaran
          _buildAppSelectionCard(),

          const SizedBox(height: 16),

          // 4. Log Pembayaran Masuk
          _buildLogsSection(currencyFmt),
        ],
      ),
    );
  }

  Widget _buildHealthStatusCard() {
    return Card(
      color: Colors.white,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Status Layanan Latar Belakang',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                ),
                Switch(
                  value: _isEnabled,
                  activeColor: const Color(0xFF2563EB),
                  onChanged: (val) {
                    setState(() => _isEnabled = val);
                    _saveAllSettings();
                  },
                )
              ],
            ),
            const Divider(height: 20),
            _buildCheckItem(
              title: 'Izin Penyadapan Notifikasi',
              subtitle: _hasNotifPermission ? 'Aktif (Diberikan)' : 'Belum Diberikan (Klik untuk membuka pengaturan)',
              isGranted: _hasNotifPermission,
              onTap: () async {
                await _bridge.openNotificationSettings();
                Future.delayed(const Duration(seconds: 1), _loadInitialData);
              },
            ),
            const SizedBox(height: 10),
            _buildCheckItem(
              title: 'Abaikan Optimasi Baterai (Anti-Kill)',
              subtitle: _isBatteryIgnored ? 'Aman (Tanpa Pembatasan Latar Belakang)' : 'Perlu Diaktifkan agar aplikasi tidak dibunuh Android',
              isGranted: _isBatteryIgnored,
              onTap: () async {
                await _bridge.requestIgnoreBatteryOptimizations();
                Future.delayed(const Duration(seconds: 1), _loadInitialData);
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCheckItem({
    required String title,
    required String subtitle,
    required bool isGranted,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: isGranted ? const Color(0xFFF0FDF4) : const Color(0xFFFEF2F2),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isGranted ? const Color(0xFFBBF7D0) : const Color(0xFFFECACA),
          ),
        ),
        child: Row(
          children: [
            Icon(
              isGranted ? Icons.check_circle : Icons.warning_rounded,
              color: isGranted ? const Color(0xFF16A34A) : const Color(0xFFDC2626),
              size: 24,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                      color: isGranted ? const Color(0xFF166534) : const Color(0xFF991B1B),
                    ),
                  ),
                  Text(
                    subtitle,
                    style: TextStyle(
                      fontSize: 11,
                      color: isGranted ? const Color(0xFF15803D) : const Color(0xFFB91C1C),
                    ),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, size: 18, color: Colors.grey),
          ],
        ),
      ),
    );
  }

  Widget _buildConfigurationCard() {
    return Card(
      color: Colors.white,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Tujuan Penerusan Callback Website',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _urlCtrl,
              decoration: InputDecoration(
                labelText: 'URL Endpoint Callback',
                hintText: 'https://arizan.my.id/api/v1/payment/callback-notification',
                filled: true,
                fillColor: const Color(0xFFF8FAFC),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _secretCtrl,
              decoration: InputDecoration(
                labelText: 'API Secret Key Website',
                hintText: 'Masukkan secret key dari web admin...',
                filled: true,
                fillColor: const Color(0xFFF8FAFC),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 14),
            Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2563EB),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                    onPressed: _saveAllSettings,
                    icon: const Icon(Icons.save, size: 18),
                    label: const Text('Simpan Konfigurasi', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ),
                const SizedBox(width: 8),
                OutlinedButton.icon(
                  style: OutlinedButton.styleFrom(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 12),
                  ),
                  onPressed: _isTestingPing ? null : _testPing,
                  icon: _isTestingPing
                      ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2))
                      : const Icon(Icons.send_rounded, size: 18),
                  label: const Text('Test Ping'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAppSelectionCard() {
    return Card(
      color: Colors.white,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Aplikasi yang Dipantau',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                ),
                TextButton(
                  onPressed: () async {
                    final selected = await Navigator.push<Set<String>>(
                      context,
                      MaterialPageRoute(
                        builder: (_) => AppSelectionScreen(
                          installedApps: _installedApps,
                          initialSelected: _selectedApps,
                        ),
                      ),
                    );
                    if (selected != null) {
                      setState(() => _selectedApps = selected);
                      _saveAllSettings();
                    }
                  },
                  child: const Text('Pilih / Ubah'),
                ),
              ],
            ),
            Text(
              _selectedApps.isEmpty
                  ? '⚠️ Semua notifikasi aplikasi akan diizinkan (belum difilter). Disarankan memilih hanya aplikasi perbankan/e-wallet Anda.'
                  : 'Memantau ${_selectedApps.length} aplikasi terpilih.',
              style: TextStyle(
                fontSize: 12,
                color: _selectedApps.isEmpty ? Colors.amber.shade900 : const Color(0xFF475569),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLogsSection(NumberFormat currencyFmt) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Riwayat Notifikasi Masuk',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Color(0xFF0F172A)),
            ),
            if (_logs.isNotEmpty)
              TextButton(
                onPressed: () => setState(() => _logs.clear()),
                child: const Text('Bersihkan'),
              ),
          ],
        ),
        const SizedBox(height: 8),
        if (_logs.isEmpty)
          Container(
            padding: const EdgeInsets.all(24),
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: const Column(
              children: [
                Icon(Icons.inbox_outlined, size: 40, color: Color(0xFF94A3B8)),
                SizedBox(height: 8),
                Text(
                  'Belum ada notifikasi pembayaran yang masuk.',
                  style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                ),
              ],
            ),
          )
        else
          ..._logs.map((item) {
            final hasAmount = item.amount > 0;
            return Container(
              margin: const EdgeInsets.only(bottom: 8),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        hasAmount ? currencyFmt.format(item.amount) : 'Nominal tidak terdeteksi',
                        style: TextStyle(
                          fontWeight: FontWeight.w900,
                          fontSize: 14,
                          color: hasAmount ? const Color(0xFF16A34A) : const Color(0xFF64748B),
                        ),
                      ),
                      Text(
                        DateFormat('HH:mm:ss').format(item.timestamp),
                        style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(
                    item.rawText,
                    style: const TextStyle(fontSize: 12, color: Color(0xFF334155)),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        item.packageName,
                        style: const TextStyle(fontSize: 10, color: Color(0xFF94A3B8)),
                      ),
                      if (item.responseInfo != null)
                        Text(
                          item.responseInfo!,
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: item.isSuccess == true ? Colors.green : Colors.red,
                          ),
                        ),
                    ],
                  ),
                ],
              ),
            );
          }).toList(),
      ],
    );
  }
}

