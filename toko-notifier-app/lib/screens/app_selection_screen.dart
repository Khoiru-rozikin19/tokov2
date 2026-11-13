import 'package:flutter/material.dart';

class AppSelectionScreen extends StatefulWidget {
  final List<Map<String, String>> installedApps;
  final Set<String> initialSelected;

  const AppSelectionScreen({
    super.key,
    required this.installedApps,
    required this.initialSelected,
  });

  @override
  State<AppSelectionScreen> createState() => _AppSelectionScreenState();
}

class _AppSelectionScreenState extends State<AppSelectionScreen> {
  late Set<String> _selected;
  String _searchQuery = '';
  final TextEditingController _searchCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    _selected = Set.from(widget.initialSelected);
  }

  @override
  Widget build(BuildContext context) {
    final filtered = widget.installedApps.where((app) {
      final name = (app['name'] ?? '').toLowerCase();
      final pkg = (app['package_name'] ?? '').toLowerCase();
      final query = _searchQuery.toLowerCase();
      return name.contains(query) || pkg.contains(query);
    }).toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Pilih Aplikasi Pembayaran',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context, _selected);
            },
            child: const Text(
              'Simpan',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
            ),
          )
        ],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchCtrl,
              decoration: InputDecoration(
                hintText: 'Cari aplikasi (BCA, DANA, GoPay, OVO...)...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchQuery.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchCtrl.clear();
                          setState(() => _searchQuery = '');
                        },
                      )
                    : null,
                filled: true,
                fillColor: Colors.white,
                contentPadding: const EdgeInsets.symmetric(horizontal: 16),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: Color(0xFFCBD5E1)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: Color(0xFFCBD5E1)),
                ),
              ),
              onChanged: (val) {
                setState(() => _searchQuery = val);
              },
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            color: const Color(0xFFEFF6FF),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${_selected.length} aplikasi dipilih',
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF1D4ED8),
                    fontSize: 13,
                  ),
                ),
                Row(
                  children: [
                    TextButton(
                      onPressed: () {
                        setState(() {
                          _selected.addAll(filtered.map((a) => a['package_name']!));
                        });
                      },
                      child: const Text('Pilih Semua'),
                    ),
                    TextButton(
                      onPressed: () {
                        setState(() {
                          _selected.clear();
                        });
                      },
                      child: const Text('Batal Semua'),
                    ),
                  ],
                )
              ],
            ),
          ),
          Expanded(
            child: filtered.isEmpty
                ? const Center(
                    child: Text('Tidak ada aplikasi yang cocok.'),
                  )
                : ListView.separated(
                    itemCount: filtered.length,
                    separatorBuilder: (_, __) => const Divider(height: 1, indent: 64),
                    itemBuilder: (context, index) {
                      final app = filtered[index];
                      final pkg = app['package_name'] ?? '';
                      final name = app['name'] ?? '';
                      final isChecked = _selected.contains(pkg);

                      return CheckboxListTile(
                        value: isChecked,
                        activeColor: const Color(0xFF2563EB),
                        title: Text(
                          name,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                        subtitle: Text(
                          pkg,
                          style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
                        ),
                        secondary: Container(
                          width: 40,
                          height: 40,
                          decoration: BoxDecoration(
                            color: const Color(0xFFF1F5F9),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(Icons.account_balance_wallet, color: Color(0xFF2563EB), size: 22),
                        ),
                        onChanged: (val) {
                          setState(() {
                            if (val == true) {
                              _selected.add(pkg);
                            } else {
                              _selected.remove(pkg);
                            }
                          });
                        },
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}

