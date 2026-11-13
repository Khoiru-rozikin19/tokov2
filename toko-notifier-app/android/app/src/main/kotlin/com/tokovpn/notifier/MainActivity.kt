package com.tokovpn.notifier

import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.content.pm.ApplicationInfo
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.PowerManager
import android.provider.Settings
import androidx.annotation.NonNull
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.EventChannel
import io.flutter.plugin.common.MethodChannel

class MainActivity : FlutterActivity() {

    private val METHOD_CHANNEL = "com.tokovpn.notifier/methods"
    private val EVENT_CHANNEL = "com.tokovpn.notifier/events"

    override fun configureFlutterEngine(@NonNull flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        // Event Channel untuk streaming status dan log notifikasi ke UI Flutter
        EventChannel(flutterEngine.dartExecutor.binaryMessenger, EVENT_CHANNEL).setStreamHandler(
            object : EventChannel.StreamHandler {
                override fun onListen(arguments: Any?, events: EventChannel.EventSink?) {
                    PaymentNotificationListenerService.eventSink = events
                }

                override fun onCancel(arguments: Any?) {
                    PaymentNotificationListenerService.eventSink = null
                }
            }
        )

        // Method Channel untuk kontrol sistem native
        MethodChannel(flutterEngine.dartExecutor.binaryMessenger, METHOD_CHANNEL).setMethodCallHandler { call, result ->
            when (call.method) {
                "isNotificationPermissionGranted" -> {
                    result.success(isNotificationPermissionGranted())
                }
                "openNotificationSettings" -> {
                    openNotificationAccessSettings()
                    result.success(true)
                }
                "isBatteryOptimizationIgnored" -> {
                    result.success(isBatteryOptimizationIgnored())
                }
                "requestIgnoreBatteryOptimizations" -> {
                    requestIgnoreBatteryOptimizations()
                    result.success(true)
                }
                "startForegroundService" -> {
                    startKeepAliveService()
                    result.success(true)
                }
                "stopForegroundService" -> {
                    stopKeepAliveService()
                    result.success(true)
                }
                "getInstalledApps" -> {
                    result.success(getInstalledApplicationsList())
                }
                else -> result.notImplemented()
            }
        }
    }

    private fun isNotificationPermissionGranted(): Boolean {
        val packageName = packageName
        val flat = Settings.Secure.getString(contentResolver, "enabled_notification_listeners")
        return flat != null && flat.contains(packageName)
    }

    private fun openNotificationAccessSettings() {
        val intent = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP_MR1) {
            Intent(Settings.ACTION_NOTIFICATION_LISTENER_SETTINGS)
        } else {
            Intent("android.settings.ACTION_NOTIFICATION_LISTENER_SETTINGS")
        }
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
        startActivity(intent)
    }

    private fun isBatteryOptimizationIgnored(): Boolean {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            val pm = getSystemService(Context.POWER_SERVICE) as PowerManager
            return pm.isIgnoringBatteryOptimizations(packageName)
        }
        return true
    }

    private fun requestIgnoreBatteryOptimizations() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            try {
                val intent = Intent(Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS).apply {
                    data = Uri.parse("package:$packageName")
                    addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                }
                startActivity(intent)
            } catch (e: Exception) {
                val intent = Intent(Settings.ACTION_IGNORE_BATTERY_OPTIMIZATION_SETTINGS).apply {
                    addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                }
                startActivity(intent)
            }
        }
    }

    private fun startKeepAliveService() {
        val intent = Intent(this, ForegroundKeepAliveService::class.java)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            startForegroundService(intent)
        } else {
            startService(intent)
        }
    }

    private fun stopKeepAliveService() {
        val intent = Intent(this, ForegroundKeepAliveService::class.java)
        stopService(intent)
    }

    /**
     * Mengambil daftar aplikasi yang terpasang di HP pengguna beserta nama dan package name-nya
     */
    private fun getInstalledApplicationsList(): List<Map<String, Any>> {
        val pm = packageManager
        val apps = pm.getInstalledApplications(PackageManager.GET_META_DATA)
        val list = mutableListOf<Map<String, Any>>()

        for (app in apps) {
            // Filter: utamakan aplikasi non-sistem atau aplikasi pembayaran umum
            val isSystemApp = (app.flags and ApplicationInfo.FLAG_SYSTEM) != 0
            val appName = pm.getApplicationLabel(app).toString()
            val packageName = app.packageName

            if (!isSystemApp || isPaymentRelevant(packageName)) {
                val map = HashMap<String, Any>()
                map["name"] = appName
                map["package_name"] = packageName
                list.add(map)
            }
        }

        // Urutkan alfabetis berdasarkan nama aplikasi
        return list.sortedBy { (it["name"] as String).lowercase() }
    }

    private fun isPaymentRelevant(pkg: String): Boolean {
        val lower = pkg.lowercase()
        return lower.contains("bca") || lower.contains("bri") || lower.contains("mandiri") ||
                lower.contains("bni") || lower.contains("dana") || lower.contains("ovo") ||
                lower.contains("gopay") || lower.contains("shopee") || lower.contains("linkaja") ||
                lower.contains("qris") || lower.contains("bank") || lower.contains("wallet")
    }
}

