class NotificationLogItem {
  final String packageName;
  final String rawText;
  final int amount;
  final DateTime timestamp;
  final bool? isSuccess;
  final String? responseInfo;

  NotificationLogItem({
    required this.packageName,
    required this.rawText,
    required this.amount,
    required this.timestamp,
    this.isSuccess,
    this.responseInfo,
  });

  NotificationLogItem copyWith({
    bool? isSuccess,
    String? responseInfo,
  }) {
    return NotificationLogItem(
      packageName: packageName,
      rawText: rawText,
      amount: amount,
      timestamp: timestamp,
      isSuccess: isSuccess ?? this.isSuccess,
      responseInfo: responseInfo ?? this.responseInfo,
    );
  }
}

