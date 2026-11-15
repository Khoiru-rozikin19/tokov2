const { 
    default: makeWASocket, 
    useMultiFileAuthState, 
    DisconnectReason, 
    fetchLatestBaileysVersion, 
    Browsers 
} = require('@whiskeysockets/baileys');
const express = require('express');
const cors = require('cors');
const pino = require('pino');
const fs = require('fs');
const path = require('path');

// ==========================================
// ANTI-CRASH PROTECTION
// Mencegah proses Node.js mati mendadak jika terjadi error jaringan/socket
// ==========================================
process.on('uncaughtException', (err) => {
    console.error('[Anti-Crash] Uncaught Exception:', err?.message || err);
});

process.on('unhandledRejection', (reason) => {
    console.error('[Anti-Crash] Unhandled Rejection:', reason?.message || reason);
});

const app = express();
app.use(cors());
app.use(express.json());

const PORT = 3000;
let sock = null;
let connectionStatus = 'connecting'; // connecting, qr, ready
let latestQr = null;
let isConnecting = false;
let reconnectTimer = null;

const authPath = path.join(__dirname, 'auth_info');

// Pastikan folder auth_info tersedia
if (!fs.existsSync(authPath)) {
    fs.mkdirSync(authPath, { recursive: true });
}

// Bersihkan socket lama untuk mencegah duplikasi koneksi dan memory leak
function cleanupOldSocket() {
    if (sock) {
        try {
            sock.ev?.removeAllListeners();
            sock.ws?.close();
            sock.end?.(new Error('Cleaning up socket'));
        } catch (_) {}
        sock = null;
    }
}

async function connectToWhatsApp() {
    if (isConnecting) {
        return;
    }
    isConnecting = true;

    try {
        cleanupOldSocket();

        console.log('Menginisialisasi koneksi Baileys WhatsApp...');
        const { state, saveCreds } = await useMultiFileAuthState(authPath);

        // Ambil versi WhatsApp Web terbaru
        let version = [2, 3000, 1017551065];
        try {
            const latest = await fetchLatestBaileysVersion();
            version = latest.version;
        } catch (_) {}

        sock = makeWASocket({
            version,
            auth: state,
            logger: pino({ level: 'silent' }),
            browser: Browsers.macOS('Desktop'),
            syncFullHistory: false, // Matikan sync riwayat agar hemat RAM VPS secara drastis
            markOnlineOnConnect: false,
            generateHighQualityLinkPreview: false,
            shouldIgnoreJid: (jid) => jid.endsWith('@broadcast') || jid.includes('status'), // Abaikan status WA kontak lain
            getMessage: async () => undefined,
            keepAliveIntervalMs: 25000, // Ping server WhatsApp setiap 25 detik agar koneksi tetap hidup 24 jam
            defaultQueryTimeoutMs: 60000,
            connectTimeoutMs: 60000,
            retryRequestDelayMs: 2000,
            printQRInTerminal: false,
        });

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                connectionStatus = 'qr';
                latestQr = qr;
                console.log('QR Code baru dihasilkan, silakan scan di menu admin web.');
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                connectionStatus = 'connecting';
                latestQr = null;
                console.log(`Koneksi WhatsApp terputus (Status Code: ${statusCode}). Reconnect: ${shouldReconnect}`);

                clearTimeout(reconnectTimer);

                if (shouldReconnect) {
                    // Jika restart diperlukan, reconnect lebih cepat
                    const delay = (statusCode === DisconnectReason.restartRequired) ? 1500 : 5000;
                    reconnectTimer = setTimeout(() => {
                        scheduleReconnect();
                    }, delay);
                } else {
                    console.log('Sesi logout / unlinked oleh WhatsApp. Membersihkan folder auth...');
                    try {
                        if (fs.existsSync(authPath)) {
                            fs.rmSync(authPath, { recursive: true, force: true });
                        }
                    } catch (e) {
                        console.error('Gagal menghapus authPath:', e.message);
                    }
                    reconnectTimer = setTimeout(() => {
                        scheduleReconnect();
                    }, 3000);
                }
            } else if (connection === 'open') {
                connectionStatus = 'ready';
                latestQr = null;
                console.log('✅ WhatsApp Bot terhubung dan siap digunakan!');
            }
        });

        sock.ev.on('creds.update', saveCreds);

    } catch (err) {
        console.error('Error saat inisialisasi koneksi WhatsApp:', err?.message || err);
        connectionStatus = 'connecting';
        clearTimeout(reconnectTimer);
        reconnectTimer = setTimeout(() => {
            scheduleReconnect();
        }, 5000);
    } finally {
        isConnecting = false;
    }
}

function scheduleReconnect() {
    connectToWhatsApp().catch(err => {
        console.error('Gagal menjalankan scheduleReconnect:', err?.message || err);
    });
}

// ==========================================
// API ENDPOINTS
// ==========================================

app.get('/status', (req, res) => {
    res.json({
        status: connectionStatus,
        qr: latestQr,
        timestamp: Date.now()
    });
});

app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ error: 'Missing phone or message' });
    }

    if (connectionStatus !== 'ready' || !sock) {
        return res.status(503).json({ error: 'WhatsApp is not ready' });
    }

    try {
        let formattedPhone = phone.replace(/[^0-9]/g, '');
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '62' + formattedPhone.slice(1);
        }
        const jid = `${formattedPhone}@s.whatsapp.net`;

        const sentMsg = await sock.sendMessage(jid, { text: message });
        res.json({ success: true, messageId: sentMsg?.key?.id });
    } catch (err) {
        console.error('Error sending message:', err?.message || err);
        res.status(500).json({ error: err?.message || 'Gagal mengirim pesan' });
    }
});

app.post('/send-group-message', async (req, res) => {
    const { groupId, message } = req.body;

    if (!groupId || !message) {
        return res.status(400).json({ error: 'Missing groupId or message' });
    }

    if (connectionStatus !== 'ready' || !sock) {
        return res.status(503).json({ error: 'WhatsApp is not ready' });
    }

    try {
        let jid = groupId.trim();
        if (!jid.includes('@')) {
            jid = `${jid}@g.us`;
        }

        const sentMsg = await sock.sendMessage(jid, { text: message });
        res.json({ success: true, messageId: sentMsg?.key?.id });
    } catch (err) {
        console.error('Error sending group message:', err?.message || err);
        res.status(500).json({ error: err?.message || 'Gagal mengirim pesan grup' });
    }
});

app.get('/groups', async (req, res) => {
    if (connectionStatus !== 'ready' || !sock) {
        return res.status(503).json({ error: 'WhatsApp is not ready' });
    }

    try {
        const groups = await sock.groupFetchAllParticipating();
        const list = Object.values(groups).map(g => ({
            id: g.id,
            subject: g.subject
        }));
        res.json({ success: true, groups: list });
    } catch (err) {
        console.error('Error fetching groups:', err?.message || err);
        res.status(500).json({ error: err?.message || 'Gagal mengambil daftar grup' });
    }
});

app.post('/disconnect', async (req, res) => {
    try {
        cleanupOldSocket();
        if (fs.existsSync(authPath)) {
            fs.rmSync(authPath, { recursive: true, force: true });
        }
        connectionStatus = 'connecting';
        latestQr = null;
        res.json({ success: true });
        setTimeout(scheduleReconnect, 2000);
    } catch (err) {
        res.status(500).json({ error: err?.message || 'Gagal disconnect' });
    }
});

// Jalankan Web Server Gateway
app.listen(PORT, '127.0.0.1', () => {
    console.log(`WhatsApp API Gateway server running internally at http://127.0.0.1:${PORT}`);
    scheduleReconnect();
});
