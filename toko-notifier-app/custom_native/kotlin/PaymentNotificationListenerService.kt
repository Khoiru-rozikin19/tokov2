package com.tokovpn.notifier

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.Context
import android.os.Build
import android.service.notification.NotificationListenerService
import android.service.notification.StatusBarNotification
import android.util.Log
import io.flutter.plugin.common.EventChannel
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL
import kotlin.concurrent.thread

class PaymentNotificationListenerService : NotificationListenerService() {

    companion object {
        private const val TAG = "PaymentNotifService"
        var eventSink: EventChannel.EventSink? = null
        var isConnected: Boolean = false
    }

    override fun onListenerConnected() {
        super.onListenerConnected()
        isConnected = true
        Log.i(TAG, "Notification Listener Service Connected successfully!")
        notifyFlutter("SERVICE_STATUS", "CONNECTED")
    }

    override fun onListenerDisconnected() {
        super.onListenerDisconnected()
        isConnected = false
        Log.w(TAG, "Notification Listener Service Disconnected!")
        notifyFlutter("SERVICE_STATUS", "DISCONNECTED")
    }

    override fun onNotificationPosted(sbn: StatusBarNotification?) {
        super.onNotificationPosted(sbn)
        if (sbn == null) return

        val packageName = sbn.packageName ?: return
        val extras = sbn.notification?.extras ?: return

        val title = extras.getCharSequence(Notification.EXTRA_TITLE)?.toString() ?: ""
        val text = extras.getCharSequence(Notification.EXTRA_TEXT)?.toString() ?: ""
        val bigText = extras.getCharSequence(Notification.EXTRA_BIG_TEXT)?.toString() ?: ""

        val fullText = if (bigText.isNotEmpty()) "$title - $bigText" else "$title - $text"
        if (fullText.trim().isEmpty() || fullText == " - ") return

        val prefs = getSharedPreferences("FlutterSharedPreferences", Context.MODE_PRIVATE)

        // Periksa apakah fitur listener diaktifkan di aplikasi
        val isEnabled = prefs.getBoolean("flutter.service_enabled", true)
        if (!isEnabled) {
            return
        }

        // Ambil daftar package name yang dicentang oleh user
        val selectedApps = prefs.getString("flutter.selected_apps", "") ?: ""
        val appList = selectedApps.split(",").map { it.trim() }.filter { it.isNotEmpty() }

        // Jika user memilih aplikasi tertentu, cek apakah package cocok
        if (appList.isNotEmpty() && !appList.contains(packageName)) {
            return
        }

        val targetUrl = prefs.getString("flutter.server_url", "https://arizan.my.id/api/v1/payment/callback-notification") ?: "https://arizan.my.id/api/v1/payment/callback-notification"
        val secretKey = prefs.getString("flutter.secret_key", "") ?: ""

        // Ekstrak nominal uang dari notifikasi
        val detectedAmount = extractAmount(fullText)

        val logData = HashMap<String, Any>()
        logData["package_name"] = packageName
        logData["raw_text"] = fullText
        logData["amount"] = detectedAmount
        logData["timestamp"] = System.currentTimeMillis()

        // Kirim event ke UI Flutter jika aplikasi sedang terbuka
        notifyFlutter("NOTIFICATION_RECEIVED", logData)

        // Langsung kirim ke backend Laravel di background thread (meskipun aplikasi Flutter ditutup)
        if (detectedAmount > 0 && secretKey.isNotEmpty() && targetUrl.isNotEmpty()) {
            thread {
                sendCallbackToServer(targetUrl, secretKey, fullText, detectedAmount)
            }
        }
    }

    /**
     * Regex cerdas untuk mengekstrak angka nominal uang dari notifikasi pembayaran
     * Contoh: "Rp 50.009", "Rp. 10,000", "50009", "IDR 25.000"
     */
    private fun extractAmount(text: String): Long {
        try {
            // Regex mencari pola Rp/IDR yang diikuti nominal angka
            val regex = Regex("""(?:Rp\.?|IDR)\s*([0-9.,]+)""", RegexOption.IGNORE_CASE)
            val match = regex.find(text)
            if (match != null) {
                val rawDigits = match.groupValues[1].replace(".", "").replace(",", "")
                val parsed = rawDigits.toLongOrNull()
                if (parsed != null && parsed > 0) return parsed
            }

            // Fallback: Cari angka terisolasi dengan panjang 4-9 digit (Rp 1.000 - Rp 999.000.000)
            val digitRegex = Regex("""\b([1-9][0-9]{3,8})\b""")
            val digitMatch = digitRegex.find(text)
            if (digitMatch != null) {
                return digitMatch.groupValues[1].toLongOrNull() ?: 0L
            }
        } catch (e: Exception) {
            Log.e(TAG, "Error extracting amount: ${e.message}")
        }
        return 0L
    }

    /**
     * Eksekusi HTTP POST langsung via native Android HttpURLConnection
     * Sangat stabil & tidak bergantung pada lifecycle Flutter Engine
     */
    private fun sendCallbackToServer(targetUrl: String, secretKey: String, rawText: String, amount: Long) {
        var conn: HttpURLConnection? = null
        try {
            val url = URL(targetUrl)
            conn = url.openConnection() as HttpURLConnection
            conn.requestMethod = "POST"
            conn.setRequestProperty("Content-Type", "application/json; utf-8")
            conn.setRequestProperty("Accept", "application/json")
            conn.connectTimeout = 15000
            conn.readTimeout = 15000
            conn.doOutput = true

            val jsonBody = JSONObject().apply {
                put("raw_text", rawText)
                put("amount", amount.toString())
                put("secret_key", secretKey)
            }

            OutputStreamWriter(conn.outputStream).use { writer ->
                writer.write(jsonBody.toString())
                writer.flush()
            }

            val responseCode = conn.responseCode
            Log.i(TAG, "Callback HTTP Status: $responseCode for amount: $amount")
            
            val statusMap = HashMap<String, Any>()
            statusMap["amount"] = amount
            statusMap["http_status"] = responseCode
            statusMap["success"] = (responseCode in 200..299)
            notifyFlutter("CALLBACK_SENT", statusMap)

        } catch (e: Exception) {
            Log.e(TAG, "Callback failed: ${e.message}")
            val errorMap = HashMap<String, Any>()
            errorMap["amount"] = amount
            errorMap["error"] = e.message ?: "Network error"
            notifyFlutter("CALLBACK_ERROR", errorMap)
        } finally {
            conn?.disconnect()
        }
    }

    private fun notifyFlutter(type: String, data: Any) {
        val payload = HashMap<String, Any>()
        payload["type"] = type
        payload["data"] = data
        try {
            eventSink?.success(payload)
        } catch (e: Exception) {
            // Flutter UI sedang tidak mendengarkan / background
        }
    }
}

