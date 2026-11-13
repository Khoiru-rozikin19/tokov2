import 'dart:async';
import 'dart:convert';
import 'package:flutter/services.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class NativeBridgeService {
  static const MethodChannel _methodChannel = MethodChannel('com.tokovpn.toko_notifier/methods');
  static const EventChannel _eventChannel = EventChannel('com.tokovpn.toko_notifier/events');

  // Keys SharedPreferences
  static const String keyServiceEnabled = 'service_enabled';
  static const String keyServerUrl = 'server_url';
  static const String keySecretKey = 'secret_key';
  static const String keySelectedApps = 'selected_apps';
  static const String defaultUrl = 'https://arizan.my.id/api/v1/payment/callback-notification';

  Stream<dynamic>? _eventStream;

  Stream<dynamic> get eventStream {
    _eventStream ??= _eventChannel.receiveBroadcastStream();
    return _eventStream!;
  }

  Future<bool> isNotificationPermissionGranted() async {
    try {
      final bool result = await _methodChannel.invokeMethod('isNotificationPermissionGranted');
      return result;
    } catch (e) {
      return false;
    }
  }

  Future<void> openNotificationSettings() async {
    try {
      await _methodChannel.invokeMethod('openNotificationSettings');
    } catch (_) {}
  }

  Future<bool> isBatteryOptimizationIgnored() async {
    try {
      final bool result = await _methodChannel.invokeMethod('isBatteryOptimizationIgnored');
      return result;
    } catch (e) {
      return false;
    }
  }

  Future<void> requestIgnoreBatteryOptimizations() async {
    try {
      await _methodChannel.invokeMethod('requestIgnoreBatteryOptimizations');
    } catch (_) {}
  }

  Future<void> startForegroundService() async {
    try {
      await _methodChannel.invokeMethod('startForegroundService');
    } catch (_) {}
  }

  Future<void> stopForegroundService() async {
    try {
      await _methodChannel.invokeMethod('stopForegroundService');
    } catch (_) {}
  }

  Future<List<Map<String, String>>> getInstalledApps() async {
    try {
      final List<dynamic> apps = await _methodChannel.invokeMethod('getInstalledApps');
      return apps.map((app) {
        return {
          'name': (app['name'] ?? '').toString(),
          'package_name': (app['package_name'] ?? '').toString(),
        };
      }).toList();
    } catch (e) {
      return [];
    }
  }

  // Pengaturan Lokal via SharedPreferences
  Future<Map<String, dynamic>> loadSettings() async {
    final prefs = await SharedPreferences.getInstance();
    final isEnabled = prefs.getBool(keyServiceEnabled) ?? true;
    final serverUrl = prefs.getString(keyServerUrl) ?? defaultUrl;
    final secretKey = prefs.getString(keySecretKey) ?? '';
    final selectedAppsString = prefs.getString(keySelectedApps) ?? '';
    final selectedApps = selectedAppsString.split(',').where((s) => s.trim().isNotEmpty).toSet();

    return {
      'isEnabled': isEnabled,
      'serverUrl': serverUrl,
      'secretKey': secretKey,
      'selectedApps': selectedApps,
    };
  }

  Future<void> saveSettings({
    required bool isEnabled,
    required String serverUrl,
    required String secretKey,
    required Set<String> selectedApps,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(keyServiceEnabled, isEnabled);
    await prefs.setString(keyServerUrl, serverUrl.trim());
    await prefs.setString(keySecretKey, secretKey.trim());
    await prefs.setString(keySelectedApps, selectedApps.join(','));

    if (isEnabled) {
      await startForegroundService();
    } else {
      await stopForegroundService();
    }
  }

  // Kirim Ping Test ke website untuk memastikan secret key & url valid
  Future<Map<String, dynamic>> sendPingTest({
    required String serverUrl,
    required String secretKey,
  }) async {
    try {
      final response = await http.post(
        Uri.parse(serverUrl),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'raw_text': 'PING_TEST',
          'amount': '0',
          'secret_key': secretKey,
        }),
      ).timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode >= 200 && response.statusCode < 300,
        'message': data['message'] ?? 'Status ${response.statusCode}',
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal koneksi: $e',
      };
    }
  }
}

