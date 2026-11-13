import xml.etree.ElementTree as ET
import sys

manifest_path = sys.argv[1]

# Register android namespace
ET.register_namespace('android', 'http://schemas.android.com/apk/res/android')
tree = ET.parse(manifest_path)
root = tree.getroot()

android_ns = '{http://schemas.android.com/apk/res/android}'

# Permissions to add
permissions = [
    'android.permission.INTERNET',
    'android.permission.ACCESS_NETWORK_STATE',
    'android.permission.RECEIVE_BOOT_COMPLETED',
    'android.permission.FOREGROUND_SERVICE',
    'android.permission.FOREGROUND_SERVICE_DATA_SYNC',
    'android.permission.WAKE_LOCK',
    'android.permission.REQUEST_IGNORE_BATTERY_OPTIMIZATIONS',
    'android.permission.POST_NOTIFICATIONS',
    'android.permission.QUERY_ALL_PACKAGES',
]

existing_perms = {
    elem.attrib.get(f'{android_ns}name')
    for elem in root.findall('uses-permission')
}

for perm in permissions:
    if perm not in existing_perms:
        p_elem = ET.Element('uses-permission')
        p_elem.set(f'{android_ns}name', perm)
        root.insert(0, p_elem)

app_elem = root.find('application')
if app_elem is not None:
    app_elem.set(f'{android_ns}usesCleartextTraffic', 'true')
    
    # Tambahkan Service Penyadapan Notifikasi
    service_listener = ET.SubElement(app_elem, 'service')
    service_listener.set(f'{android_ns}name', '.PaymentNotificationListenerService')
    service_listener.set(f'{android_ns}label', 'Toko Payment Notification Listener')
    service_listener.set(f'{android_ns}permission', 'android.permission.BIND_NOTIFICATION_LISTENER_SERVICE')
    service_listener.set(f'{android_ns}exported', 'true')
    intent_filter = ET.SubElement(service_listener, 'intent-filter')
    action = ET.SubElement(intent_filter, 'action')
    action.set(f'{android_ns}name', 'android.service.notification.NotificationListenerService')

    # Tambahkan Foreground Keep Alive Service
    service_keepalive = ET.SubElement(app_elem, 'service')
    service_keepalive.set(f'{android_ns}name', '.ForegroundKeepAliveService')
    service_keepalive.set(f'{android_ns}enabled', 'true')
    service_keepalive.set(f'{android_ns}exported', 'false')
    service_keepalive.set(f'{android_ns}foregroundServiceType', 'dataSync')

    # Tambahkan Receiver Boot Completed (Auto-Start saat HP Nyala)
    receiver_boot = ET.SubElement(app_elem, 'receiver')
    receiver_boot.set(f'{android_ns}name', '.BootCompletedReceiver')
    receiver_boot.set(f'{android_ns}enabled', 'true')
    receiver_boot.set(f'{android_ns}exported', 'true')
    receiver_boot.set(f'{android_ns}permission', 'android.permission.RECEIVE_BOOT_COMPLETED')
    receiver_intent = ET.SubElement(receiver_boot, 'intent-filter')
    for act_name in ['android.intent.action.BOOT_COMPLETED', 'android.intent.action.QUICKBOOT_POWERON', 'com.htc.intent.action.QUICKBOOT_POWERON', 'android.intent.action.MY_PACKAGE_REPLACED']:
        act_elem = ET.SubElement(receiver_intent, 'action')
        act_elem.set(f'{android_ns}name', act_name)

tree.write(manifest_path, encoding='utf-8', xml_declaration=True)
print(f"Successfully injected permissions and services into {manifest_path}")
