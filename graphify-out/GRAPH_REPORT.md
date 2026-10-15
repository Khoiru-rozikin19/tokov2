# Graph Report - toko  (2026-10-15)

## Corpus Check
- 155 files · ~108,328 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 37 file(s) not represented in the graph (top: (none) 16, .bak 10, .bak2 6)

## Summary
- 993 nodes · 1906 edges · 115 communities (34 shown, 81 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.85)
- Token cost: 1,200 input · 400 output

## Community Hubs (Navigation)
- CLI Admin Tool (tokoctl)
- Admin Backoffice Management
- Telegram Bot & Webhooks
- Testing Suite (User)
- Testing Suite (BalanceTransaction)
- MyXL API & Quota Service
- Telegram Bot & Webhooks
- Testing Suite (QrisVpnStoreTest)
- WhatsApp Gateway Integration
- Admin Backoffice Management
- Testing Suite (TelegramVerificationTest)
- User Wallet & Balance System
- Testing Suite (ReleaseEscrowCommand)
- OKEConnect H2H Gateway
- Admin Backoffice Management
- Frontend Assets & Build Pipeline
- Testing Suite (ReleaseEscrowCommand)
- Database Migrations (2026_06_17_153557_create_)
- Dispute & Complaint Handling
- Tournament & Match System
- MyXL Crypto & Decryption
- MyXL API & Quota Service
- Admin Backoffice Management
- WhatsApp Gateway Integration
- Dispute & Complaint Handling
- Testing Suite (XlToolTest)
- Authcontroller Module
- Telegram Bot & Webhooks
- Buyer Portal & Registration
- Testing Suite (VpsServerIntegrationTest)
- Composer Module
- Composer Module
- Composer Module
- Index Module
- Testing Suite (Category)
- Tournament & Match System
- Vpssshservice Module
- Composer Module
- Tournament & Match System
- Appserviceprovider Module
- Database Migrations (0001_01_01_000000_create_)
- Database Migrations (0001_01_01_000002_create_)
- Testing Suite (TestCase)
- Sellercommission Module
- Composer Module
- Composer Module
- Database Migrations (0001_01_01_000001_create_)
- Database Migrations (2026_06_15_000000_add_rba)
- OKEConnect H2H Gateway
- Database Migrations (2026_06_15_041621_add_sn_)
- Database Migrations (2026_06_15_052546_add_tar)
- Database Migrations (2026_06_15_145357_add_des)
- Database Migrations (2026_06_15_154509_add_suc)
- Database Migrations (2026_06_16_000000_add_use)
- Database Migrations (2026_06_16_000001_add_har)
- Database Migrations (2026_06_16_065008_add_cat)
- Database Migrations (2026_06_16_145005_add_vps)
- Database Migrations (2026_06_17_000003_add_pay)
- Database Migrations (2026_06_17_000004_make_pr)
- Database Migrations (2026_06_18_000001_add_com)
- Database Migrations (2026_06_18_000002_add_sor)
- Database Migrations (2026_06_19_161529_add_ima)
- Database Migrations (2026_07_10_194417_add_sta)
- Database Migrations (2026_07_10_221500_add_is_)
- Database Migrations (2026_07_10_231000_add_cus)
- Tournament & Match System
- WhatsApp Gateway Integration
- Database Migrations (2026_07_17_150000_split_m)
- Tournament & Match System
- Database Migrations (2026_06_16_065000_create_)
- Database Migrations (2026_06_17_000001_create_)
- Database Migrations (2026_06_17_000002_create_)
- Database Migrations (2026_06_18_000003_create_)
- OKEConnect H2H Gateway
- Composer Module
- Composer Module
- Deploy Module
- Account Stocks.Blade Module
- Commissions.Blade Module
- Dispute & Complaint Handling
- Dashboard.Blade Module
- Products.Blade Module
- System Configuration & Settings
- System Configuration & Settings
- Xl.Blade Module
- Tournament & Match System
- Transactions.Blade Module
- Users.Blade Module
- Vpn Panel.Blade Module
- WhatsApp Gateway Integration
- Balance.Blade Module
- Catalog.Blade Module
- History.Blade Module
- Profile.Blade Module
- Index.Blade Module
- Show.Blade Module
- VPN & Xray Provisioning
- Robots Module
- Readme Module

## God Nodes (most connected - your core abstractions)
1. `User` - 97 edges
2. `Order` - 87 edges
3. `Product` - 76 edges
4. `AdminController` - 56 edges
5. `Setting` - 52 edges
6. `MyXlService` - 34 edges
7. `TelegramService` - 22 edges
8. `XlSession` - 20 edges
9. `AccountStock` - 17 edges
10. `tokoctl.sh script` - 16 edges

## Surprising Connections (you probably didn't know these)
- `{closure#1}()` --calls--> `Setting`  [EXTRACTED]
  tests/Feature/BalanceTopupTest.php → app/Models/Setting.php
- `{closure#1}()` --calls--> `Setting`  [EXTRACTED]
  tests/Feature/XlToolTest.php → app/Models/Setting.php
- `{closure#18}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/TelegramVerificationTest.php → app/Models/User.php
- `{closure#20}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/TelegramVerificationTest.php → app/Models/User.php
- `{closure#22}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/TelegramVerificationTest.php → app/Models/User.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **OKEConnect Automated Order Fulfillment Flow** — penjelasan_integrasi_okeconnect_orderkuota_service, penjelasan_integrasi_okeconnect_okeconnect_h2h, penjelasan_integrasi_okeconnect_webhook_callback [EXTRACTED 0.95]

## Communities (115 total, 81 thin omitted)

### Community 0 - "CLI Admin Tool (tokoctl)"
Cohesion: 0.16
Nodes (48): akun_edit_user(), akun_hapus_user(), akun_header(), akun_list_users(), akun_tambah_user(), backup_website(), change_domain(), check_and_propose_alias() (+40 more)

### Community 2 - "Telegram Bot & Webhooks"
Cohesion: 0.09
Nodes (6): {closure#2}(), TelegramWebhookController, SendOrderToOrderkuota, SendTelegramNotificationJob, OrderkuotaService, TelegramService

### Community 3 - "Testing Suite (User)"
Cohesion: 0.09
Nodes (19): Complaint, User, {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#4}() (+11 more)

### Community 4 - "Testing Suite (BalanceTransaction)"
Cohesion: 0.07
Nodes (6): BalanceController, {closure#1}(), BalanceTransaction, QrisService, UserFactory, {closure#2}()

### Community 6 - "Telegram Bot & Webhooks"
Cohesion: 0.10
Nodes (15): {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}() (+7 more)

### Community 7 - "Testing Suite (QrisVpnStoreTest)"
Cohesion: 0.09
Nodes (18): Order, {closure#1}(), {closure#10}(), {closure#12}(), {closure#14}(), {closure#15}(), {closure#16}(), {closure#17}() (+10 more)

### Community 8 - "WhatsApp Gateway Integration"
Cohesion: 0.08
Nodes (26): cors, express, pino, qrcode-terminal, @whiskeysockets/baileys, app, authPath, connectToWhatsApp() (+18 more)

### Community 9 - "Admin Backoffice Management"
Cohesion: 0.07
Nodes (7): {closure#1}(), {closure#16}(), {closure#3}(), {closure#4}(), {closure#6}(), {closure#7}(), AccountStock

### Community 10 - "Testing Suite (TelegramVerificationTest)"
Cohesion: 0.09
Nodes (11): {closure#10}(), {closure#12}(), {closure#14}(), {closure#16}(), {closure#18}(), {closure#2}(), {closure#20}(), {closure#22}() (+3 more)

### Community 12 - "Testing Suite (ReleaseEscrowCommand)"
Cohesion: 0.11
Nodes (9): {closure#1}(), UserBalance, {closure#1}(), {closure#11}(), {closure#2}(), {closure#5}(), {closure#6}(), {closure#7}() (+1 more)

### Community 13 - "OKEConnect H2H Gateway"
Cohesion: 0.10
Nodes (5): BuyerController, Controller, OkeconnectCallbackController, PaymentCallbackController, ProfileController

### Community 14 - "Admin Backoffice Management"
Cohesion: 0.14
Nodes (7): Product, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}()

### Community 15 - "Frontend Assets & Build Pipeline"
Cohesion: 0.12
Nodes (17): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private, $schema (+9 more)

### Community 16 - "Testing Suite (ReleaseEscrowCommand)"
Cohesion: 0.14
Nodes (4): ReleaseEscrowCommand, SendTournamentMatchReminders, TestTelegramCommand, TournamentMatch

### Community 17 - "Database Migrations (2026_06_17_153557_create_)"
Cohesion: 0.12
Nodes (4): {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}()

### Community 18 - "Dispute & Complaint Handling"
Cohesion: 0.12
Nodes (4): {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}()

### Community 22 - "Admin Backoffice Management"
Cohesion: 0.18
Nodes (3): Setting, DatabaseSeeder, {closure#12}()

### Community 25 - "Testing Suite (XlToolTest)"
Cohesion: 0.17
Nodes (11): {closure#1}(), {closure#10}(), {closure#11}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+3 more)

### Community 27 - "Telegram Bot & Webhooks"
Cohesion: 0.24
Nodes (3): {closure#1}(), {closure#2}(), {closure#1}()

### Community 29 - "Testing Suite (VpsServerIntegrationTest)"
Cohesion: 0.27
Nodes (5): VpsServer, {closure#1}(), {closure#2}(), {closure#5}(), {closure#6}()

### Community 30 - "Composer Module"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 31 - "Composer Module"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 32 - "Composer Module"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 33 - "Index Module"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 34 - "Testing Suite (Category)"
Cohesion: 0.38
Nodes (3): Category, {closure#1}(), {closure#2}()

### Community 37 - "Composer Module"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 38 - "Tournament & Match System"
Cohesion: 0.29
Nodes (4): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}()

### Community 40 - "Database Migrations (0001_01_01_000000_create_)"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 41 - "Database Migrations (0001_01_01_000002_create_)"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 45 - "Composer Module"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 46 - "Composer Module"
Cohesion: 0.40
Nodes (5): require, laravel/framework, laravel/tinker, php, phpseclib/phpseclib

### Community 75 - "OKEConnect H2H Gateway"
Cohesion: 0.50
Nodes (4): OKEConnect H2H Gateway, Orderkuota H2H Service, Shotgun Query Parameter Strategy, OKEConnect Webhook Handler

### Community 77 - "Composer Module"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 78 - "Composer Module"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **95 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+90 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 404 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **81 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Setting` connect `Admin Backoffice Management` to `Telegram Bot & Webhooks`, `Testing Suite (User)`, `Testing Suite (BalanceTransaction)`, `MyXL API & Quota Service`, `Testing Suite (QrisVpnStoreTest)`, `Admin Backoffice Management`, `Testing Suite (TelegramVerificationTest)`, `User Wallet & Balance System`, `Testing Suite (ReleaseEscrowCommand)`, `OKEConnect H2H Gateway`, `Admin Backoffice Management`, `MyXL API & Quota Service`, `WhatsApp Gateway Integration`, `Dispute & Complaint Handling`, `Testing Suite (XlToolTest)`, `Telegram Bot & Webhooks`, `Testing Suite (VpsServerIntegrationTest)`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `User` connect `Testing Suite (User)` to `Admin Backoffice Management`, `Testing Suite (Category)`, `Testing Suite (BalanceTransaction)`, `Testing Suite (QrisVpnStoreTest)`, `Admin Backoffice Management`, `Testing Suite (TelegramVerificationTest)`, `User Wallet & Balance System`, `Testing Suite (ReleaseEscrowCommand)`, `Admin Backoffice Management`, `Admin Backoffice Management`, `Testing Suite (XlToolTest)`, `Authcontroller Module`, `Testing Suite (VpsServerIntegrationTest)`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Why does `Order` connect `Testing Suite (QrisVpnStoreTest)` to `Admin Backoffice Management`, `Telegram Bot & Webhooks`, `Testing Suite (User)`, `Testing Suite (BalanceTransaction)`, `Vpssshservice Module`, `Admin Backoffice Management`, `Testing Suite (TelegramVerificationTest)`, `User Wallet & Balance System`, `Testing Suite (ReleaseEscrowCommand)`, `OKEConnect H2H Gateway`, `Sellercommission Module`, `Admin Backoffice Management`, `Testing Suite (ReleaseEscrowCommand)`, `Dispute & Complaint Handling`, `Telegram Bot & Webhooks`, `Testing Suite (VpsServerIntegrationTest)`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _95 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Admin Backoffice Management` be split into smaller, more focused modules?**
  _Cohesion score 0.06666666666666667 - nodes in this community are weakly interconnected._
- **Should `Telegram Bot & Webhooks` be split into smaller, more focused modules?**
  _Cohesion score 0.08846153846153847 - nodes in this community are weakly interconnected._
- **Should `Testing Suite (User)` be split into smaller, more focused modules?**
  _Cohesion score 0.09206349206349207 - nodes in this community are weakly interconnected._