# Graph Report - toko  (2026-10-15)

## Corpus Check
- 142 files · ~92,346 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 37 file(s) not represented in the graph (top: (none) 16, .bak 10, .bak2 6)

## Summary
- 898 nodes · 1753 edges · 105 communities (32 shown, 73 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- CLI Admin Tool (tokoctl)
- Telegram Bot & Webhooks
- User Wallet & Balance System
- Admin Backoffice Management
- Admin Backoffice Management
- Admin Backoffice Management
- MyXL API & Quota Service
- WhatsApp Gateway Integration
- Admin Backoffice Management
- Admin Backoffice Management
- Database Migrations (2026_06_19_165111_add_tel)
- OKEConnect H2H Gateway
- Testing Suite (TelegramVerificationTest)
- Dispute & Complaint Handling
- Testing Suite (VpsServerIntegrationTest)
- Frontend Assets & Build Pipeline
- Admin Backoffice Management
- Dispute & Complaint Handling
- MyXL Crypto & Decryption
- Testing Suite (PreOrderTest)
- MyXL API & Quota Service
- WhatsApp Gateway Integration
- Database Migrations (2026_06_14_044603_create_)
- User Module
- Testing Suite (AuthController)
- Authcontroller Module
- Composer Module
- Composer Module
- Composer Module
- App Module
- Testing Suite (ProfileTest)
- Buyer Portal & Registration
- Composer Module
- Testing Suite (Category)
- Providers Module
- Database Migrations (0001_01_01_000000_create_)
- Composer Module
- Composer Module
- Database Migrations (0001_01_01_000001_create_)
- OKEConnect H2H Gateway
- Database Migrations (2026_06_15_041621_add_sn_)
- Database Migrations (2026_06_15_052546_add_tar)
- Database Migrations (2026_06_15_145357_add_des)
- Database Migrations (2026_06_15_154509_add_suc)
- Database Migrations (2026_06_16_000000_add_use)
- Database Migrations (2026_06_16_000001_add_har)
- Database Migrations (2026_06_16_065008_add_cat)
- Database Migrations (2026_06_17_000003_add_pay)
- Database Migrations (2026_06_17_000004_make_pr)
- Database Migrations (2026_06_17_122335_add_vis)
- Database Migrations (2026_06_18_000001_add_com)
- Database Migrations (2026_06_18_000002_add_sor)
- Database Migrations (2026_06_19_043845_add_tel)
- Database Migrations (2026_06_19_161529_add_ima)
- Escrow Fund Release Management
- Database Migrations (2026_07_10_194417_add_sta)
- Database Migrations (2026_07_10_221500_add_is_)
- Database Migrations (2026_07_10_231000_add_cus)
- WhatsApp Gateway Integration
- Database Migrations (2026_06_14_044604_create_)
- Database Migrations (2026_06_14_044606_create_)
- Database Migrations (2026_06_15_135913_create_)
- Database Migrations (2026_06_16_065000_create_)
- Database Migrations (2026_06_17_000001_create_)
- Database Migrations (2026_06_17_153557_create_)
- OKEConnect H2H Gateway
- Composer Module
- Composer Module
- Testing Suite (TestCase)
- Deploy Module
- Account Stocks.Blade Module
- Commissions.Blade Module
- Dispute & Complaint Handling
- Dashboard.Blade Module
- Products.Blade Module
- Settings.Blade Module
- Supplier Settings.Blade Module
- Xl.Blade Module
- Transactions.Blade Module
- Users.Blade Module
- Vpn Panel.Blade Module
- WhatsApp Gateway Integration
- Balance.Blade Module
- Catalog.Blade Module
- History.Blade Module
- Profile.Blade Module
- Which Add Module
- Robots Module
- Readme Module

## God Nodes (most connected - your core abstractions)
1. `User` - 93 edges
2. `Order` - 87 edges
3. `Product` - 76 edges
4. `Setting` - 52 edges
5. `AdminController` - 47 edges
6. `MyXlService` - 34 edges
7. `TelegramService` - 21 edges
8. `XlSession` - 20 edges
9. `AccountStock` - 17 edges
10. `tokoctl.sh script` - 16 edges

## Surprising Connections (you probably didn't know these)
- `{closure#1}()` --calls--> `Setting`  [EXTRACTED]
  tests/Feature/BalanceTopupTest.php → app/Models/Setting.php
- `{closure#2}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/BalanceTopupTest.php → app/Models/User.php
- `{closure#3}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/BalanceTopupTest.php → app/Models/User.php
- `{closure#2}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/ProfileTest.php → app/Models/User.php
- `{closure#3}()` --calls--> `User`  [EXTRACTED]
  tests/Feature/ProfileTest.php → app/Models/User.php

## Import Cycles
- None detected.

## Communities (105 total, 73 thin omitted)

### Community 0 - "CLI Admin Tool (tokoctl)"
Cohesion: 0.16
Nodes (48): akun_edit_user(), akun_hapus_user(), akun_header(), akun_list_users(), akun_tambah_user(), backup_website(), change_domain(), check_and_propose_alias() (+40 more)

### Community 1 - "Telegram Bot & Webhooks"
Cohesion: 0.07
Nodes (9): ReleaseEscrowCommand, TestTelegramCommand, {closure#1}(), TelegramWebhookController, SendOrderToOrderkuota, SendTelegramNotificationJob, OrderkuotaService, TelegramService (+1 more)

### Community 2 - "User Wallet & Balance System"
Cohesion: 0.07
Nodes (6): BalanceController, {closure#1}(), BalanceTransaction, PaymentLog, SellerCommission, {closure#9}()

### Community 4 - "Admin Backoffice Management"
Cohesion: 0.09
Nodes (14): User, {closure#18}(), {closure#22}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#2}(), {closure#3}() (+6 more)

### Community 5 - "Admin Backoffice Management"
Cohesion: 0.10
Nodes (15): Product, {closure#1}(), {closure#10}(), {closure#12}(), {closure#14}(), {closure#15}(), {closure#16}(), {closure#18}() (+7 more)

### Community 7 - "WhatsApp Gateway Integration"
Cohesion: 0.08
Nodes (26): cors, express, pino, qrcode-terminal, @whiskeysockets/baileys, app, authPath, connectToWhatsApp() (+18 more)

### Community 8 - "Admin Backoffice Management"
Cohesion: 0.12
Nodes (15): {closure#1}(), {closure#1}(), {closure#2}(), Complaint, Order, UserBalance, {closure#1}(), {closure#2}() (+7 more)

### Community 9 - "Admin Backoffice Management"
Cohesion: 0.10
Nodes (12): {closure#1}(), {closure#3}(), {closure#4}(), {closure#6}(), {closure#7}(), AccountStock, {closure#1}(), {closure#2}() (+4 more)

### Community 10 - "Database Migrations (2026_06_19_165111_add_tel)"
Cohesion: 0.12
Nodes (11): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}() (+3 more)

### Community 11 - "OKEConnect H2H Gateway"
Cohesion: 0.10
Nodes (5): BuyerController, Controller, OkeconnectCallbackController, PaymentCallbackController, ProfileController

### Community 12 - "Testing Suite (TelegramVerificationTest)"
Cohesion: 0.10
Nodes (9): {closure#10}(), {closure#12}(), {closure#14}(), {closure#16}(), {closure#2}(), {closure#20}(), {closure#4}(), {closure#5}() (+1 more)

### Community 13 - "Dispute & Complaint Handling"
Cohesion: 0.11
Nodes (4): CatalogController, {closure#2}(), QrisService, {closure#2}()

### Community 14 - "Testing Suite (VpsServerIntegrationTest)"
Cohesion: 0.13
Nodes (6): VpsServer, VpsSshService, {closure#1}(), {closure#2}(), {closure#5}(), {closure#6}()

### Community 15 - "Frontend Assets & Build Pipeline"
Cohesion: 0.12
Nodes (17): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private, $schema (+9 more)

### Community 16 - "Admin Backoffice Management"
Cohesion: 0.16
Nodes (5): Setting, {closure#1}(), {closure#8}(), {closure#1}(), {closure#1}()

### Community 17 - "Dispute & Complaint Handling"
Cohesion: 0.12
Nodes (3): {closure#1}(), {closure#1}(), {closure#1}()

### Community 19 - "Testing Suite (PreOrderTest)"
Cohesion: 0.13
Nodes (5): {closure#11}(), {closure#2}(), {closure#5}(), {closure#6}(), {closure#7}()

### Community 22 - "Database Migrations (2026_06_14_044603_create_)"
Cohesion: 0.15
Nodes (3): {closure#1}(), {closure#1}(), {closure#1}()

### Community 24 - "Testing Suite (AuthController)"
Cohesion: 0.20
Nodes (4): DatabaseSeeder, {closure#1}(), {closure#2}(), {closure#3}()

### Community 26 - "Composer Module"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 27 - "Composer Module"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 28 - "Composer Module"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 29 - "App Module"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 30 - "Testing Suite (ProfileTest)"
Cohesion: 0.25
Nodes (6): {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}()

### Community 32 - "Composer Module"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 33 - "Testing Suite (Category)"
Cohesion: 0.47
Nodes (3): Category, {closure#1}(), {closure#2}()

### Community 35 - "Database Migrations (0001_01_01_000000_create_)"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 37 - "Composer Module"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 38 - "Composer Module"
Cohesion: 0.40
Nodes (5): require, laravel/framework, laravel/tinker, php, phpseclib/phpseclib

### Community 67 - "OKEConnect H2H Gateway"
Cohesion: 0.50
Nodes (4): OKEConnect H2H Gateway, Orderkuota H2H Service, Shotgun Query Parameter Strategy, OKEConnect Webhook Handler

### Community 69 - "Composer Module"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 70 - "Composer Module"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **92 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+87 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 356 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **73 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Setting` connect `Admin Backoffice Management` to `Telegram Bot & Webhooks`, `User Wallet & Balance System`, `Admin Backoffice Management`, `Admin Backoffice Management`, `Admin Backoffice Management`, `MyXL API & Quota Service`, `Admin Backoffice Management`, `Admin Backoffice Management`, `OKEConnect H2H Gateway`, `Testing Suite (TelegramVerificationTest)`, `Dispute & Complaint Handling`, `Testing Suite (VpsServerIntegrationTest)`, `Testing Suite (PreOrderTest)`, `MyXL API & Quota Service`, `WhatsApp Gateway Integration`, `Testing Suite (AuthController)`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._
- **Why does `User` connect `Admin Backoffice Management` to `Testing Suite (Category)`, `User Wallet & Balance System`, `Admin Backoffice Management`, `Telegram Bot & Webhooks`, `Admin Backoffice Management`, `Admin Backoffice Management`, `Admin Backoffice Management`, `Testing Suite (TelegramVerificationTest)`, `Testing Suite (VpsServerIntegrationTest)`, `Admin Backoffice Management`, `Testing Suite (PreOrderTest)`, `User Module`, `Testing Suite (AuthController)`, `Authcontroller Module`, `Testing Suite (ProfileTest)`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `Order` connect `Admin Backoffice Management` to `Telegram Bot & Webhooks`, `User Wallet & Balance System`, `Admin Backoffice Management`, `Admin Backoffice Management`, `Admin Backoffice Management`, `OKEConnect H2H Gateway`, `Testing Suite (TelegramVerificationTest)`, `Dispute & Complaint Handling`, `Testing Suite (VpsServerIntegrationTest)`, `Admin Backoffice Management`, `Testing Suite (PreOrderTest)`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _92 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Telegram Bot & Webhooks` be split into smaller, more focused modules?**
  _Cohesion score 0.07373737373737374 - nodes in this community are weakly interconnected._
- **Should `User Wallet & Balance System` be split into smaller, more focused modules?**
  _Cohesion score 0.07439024390243902 - nodes in this community are weakly interconnected._
- **Should `Admin Backoffice Management` be split into smaller, more focused modules?**
  _Cohesion score 0.1028225806451613 - nodes in this community are weakly interconnected._