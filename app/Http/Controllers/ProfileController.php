<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Order;
use App\Services\WhatsappService;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();

        // Build email & phone variations to count all product orders made by this user
        $variations = array_filter([
            $user->email,
            strtolower((string) $user->email),
        ]);

        if (!empty($user->phone)) {
            $rawPhone = preg_replace('/[^0-9]/', '', $user->phone);
            if (!empty($rawPhone)) {
                $basePhone = $rawPhone;
                if (str_starts_with($rawPhone, '62')) {
                    $basePhone = substr($rawPhone, 2);
                } elseif (str_starts_with($rawPhone, '0')) {
                    $basePhone = substr($rawPhone, 1);
                }
                $variations[] = $user->phone;
                $variations[] = $rawPhone;
                $variations[] = '0' . $basePhone;
                $variations[] = '62' . $basePhone;
                $variations[] = '+62' . $basePhone;
                $variations[] = $basePhone;
            }
        }
        $variations = array_unique(array_filter($variations));

        $totalTransactionsCount = Order::where(function ($query) use ($user, $variations) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('email_or_whatsapp', $variations);
            })
            ->where(function ($query) {
                $query->whereNull('payment_method')
                      ->orWhere('payment_method', '!=', 'topup_balance');
            })
            ->where('id', 'not like', 'TOP-%')
            ->count();

        return view('profile', [
            'title' => 'Profil Saya',
            'user' => $user,
            'currentBalance' => $user->getBalance(),
            'totalTransactionsCount' => $totalTransactionsCount,
        ]);
    }

    /**
     * Normalize phone number for comparison and WhatsApp sending (e.g., 628123456789).
     */
    private function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }
        return $digits;
    }

    /**
     * Update the user profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telegram_chat_id' => 'nullable|regex:/^-?[0-9]+$/|max:100',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak cocok.'])->withInput();
            }
        }

        $newNormalizedPhone = $this->normalizePhone($request->phone);
        $oldNormalizedPhone = $this->normalizePhone($user->phone);
        $isPhoneChanged = $newNormalizedPhone !== $oldNormalizedPhone;

        if ($isPhoneChanged) {
            $inputOtp = trim((string) $request->input('phone_otp', ''));

            if (empty($inputOtp)) {
                return back()
                    ->withErrors(['phone' => 'Perubahan nomor HP/WA memerlukan verifikasi kode OTP WhatsApp.'])
                    ->withInput()
                    ->with('open_section', 'menu')
                    ->with('open_phone_otp_modal', true);
            }

            if (
                empty($user->whatsapp_otp) ||
                $user->whatsapp_otp !== $inputOtp ||
                !$user->whatsapp_otp_expires_at ||
                now()->greaterThan($user->whatsapp_otp_expires_at)
            ) {
                return back()
                    ->withErrors(['phone' => 'Kode OTP WhatsApp tidak valid atau sudah kedaluwarsa.'])
                    ->withInput()
                    ->with('open_section', 'menu')
                    ->with('open_phone_otp_modal', true);
            }

            $pendingPhone = session('pending_phone_change');
            if (!empty($pendingPhone) && $pendingPhone !== $newNormalizedPhone) {
                return back()
                    ->withErrors(['phone' => 'Nomor WhatsApp yang dimasukkan berbeda dengan nomor yang dikirimkan OTP.'])
                    ->withInput()
                    ->with('open_section', 'menu')
                    ->with('open_phone_otp_modal', true);
            }
        }

        DB::transaction(function () use ($user, $request, $isPhoneChanged) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }
            if ($request->has('telegram_chat_id')) {
                $user->telegram_chat_id = $request->telegram_chat_id;
            }
            
            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            if ($isPhoneChanged) {
                $user->whatsapp_otp = null;
                $user->whatsapp_otp_expires_at = null;
            }
            
            $user->save();
        });

        if ($isPhoneChanged) {
            session()->forget('pending_phone_change');
        }

        return redirect()->route('profile.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Send WhatsApp OTP when user wants to change phone/WA number.
     */
    public function sendPhoneChangeOtp(Request $request)
    {
        $user = Auth::user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ], [
            'phone.required' => 'Nomor HP/WhatsApp baru wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $normalizedPhone = $this->normalizePhone($request->phone);
        if (strlen($normalizedPhone) < 9) {
            return response()->json([
                'success' => false,
                'message' => 'Format nomor HP/WhatsApp tidak valid.',
            ], 422);
        }

        $cooldownKey = 'change-phone-otp-cooldown:' . $user->id;
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return response()->json([
                'success' => false,
                'message' => 'Mohon tunggu ' . $seconds . ' detik sebelum meminta kode OTP baru.',
            ], 429);
        }

        $whatsappService = app(WhatsappService::class);
        if (!$whatsappService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan Bot WhatsApp sedang tidak aktif. Silakan hubungi Admin.',
            ], 503);
        }

        $otp = (string) rand(100000, 999999);
        $user->update([
            'whatsapp_otp' => $otp,
            'whatsapp_otp_expires_at' => now()->addMinutes(5),
        ]);
        session(['pending_phone_change' => $normalizedPhone]);

        try {
            $sent = $whatsappService->sendOtp($normalizedPhone, $otp);
            if (!$sent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP ke WhatsApp (' . $request->phone . '). Pastikan nomor WhatsApp aktif dan bot terhubung.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
            ], 500);
        }

        RateLimiter::hit($cooldownKey, 60);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6 digit telah dikirim oleh Bot WhatsApp ke nomor ' . $request->phone . '.',
        ]);
    }

    /**
     * Update only the user password from the Ubah Password menu.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok.'])->with('open_section', 'menu');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.edit', ['section' => 'menu'])->with('success', 'Password Anda berhasil diperbarui.');
    }

    /**
     * Send WhatsApp OTP for resetting forgotten password.
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $user = Auth::user();

        if (empty($user->phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP/WhatsApp belum diatur pada akun Anda. Silakan isi nomor HP di menu Edit Profil terlebih dahulu.',
            ], 422);
        }

        $phone = preg_replace('/[^0-9]/', '', $user->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $cooldownKey = 'forgot-pwd-otp-cooldown:' . $user->id;
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return response()->json([
                'success' => false,
                'message' => 'Mohon tunggu ' . $seconds . ' detik sebelum meminta kode OTP baru.',
            ], 429);
        }

        $whatsappService = app(WhatsappService::class);
        if (!$whatsappService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan Bot WhatsApp sedang tidak aktif. Silakan hubungi Admin.',
            ], 503);
        }

        $otp = (string) rand(100000, 999999);
        $user->update([
            'whatsapp_otp' => $otp,
            'whatsapp_otp_expires_at' => now()->addMinutes(5),
        ]);

        try {
            $sent = $whatsappService->sendOtp($phone, $otp);
            if (!$sent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP ke WhatsApp (' . $user->phone . '). Pastikan nomor WhatsApp aktif dan bot terhubung.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
            ], 500);
        }

        RateLimiter::hit($cooldownKey, 60);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6 digit telah dikirim ke nomor WhatsApp ' . $user->phone . '.',
        ]);
    }

    /**
     * Verify WhatsApp OTP and reset user password.
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = Auth::user();

        if (empty($user->whatsapp_otp) || $user->whatsapp_otp !== $request->otp || !$user->whatsapp_otp_expires_at || now()->greaterThan($user->whatsapp_otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'whatsapp_otp' => null,
            'whatsapp_otp_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password Anda berhasil direset menggunakan OTP WhatsApp!',
        ]);
    }
}
