package com.tokovpn.toko_notifier

import android.content.Context
import android.content.Intent
import android.content.pm.ApplicationInfo
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.PowerManager
import android.provider.Settings
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.EventChannel
import io.flutter.plugin.common.MethodChannel

class MainActivity : FlutterActivity() {

    private val METHOD_CHANNEL = "com.tokovpn.toko_notifier/methods"
    private val EVENT_CHANNEL = "com.tokovpn.toko_notifier/events"

    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
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
                e.printStackTrace()
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

    private fun getInstalledApplicationsList(): List<Map<String, String>> {
        val pm = packageManager
        val apps = pm.getInstalledApplications(PackageManager.GET_META_DATA)
        val result = mutableListOf<Map<String, String>>()

        for (app in apps) {
            val isSystem = (app.flags and ApplicationInfo.FLAG_SYSTEM) != 0
            val appName = pm.getApplicationLabel(app).toString()
            val packageName = app.packageName

            if (!isSystem || packageName.contains("bank") || packageName.contains("pay") || packageName.contains("dana") || packageName.contains("ovo")) {
                val item = HashMap<String, String>()
                item["packageName"] = packageName
                item["appName"] = appName
                result.add(item)
            }
        }

        result.sortBy { it["appName"]?.lowercase() }
        return result
    }
}
