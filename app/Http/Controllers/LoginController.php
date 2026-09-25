<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('landing'))
                ->with('success', 'Selamat datang kembali!');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Jangan membedakan email terdaftar vs tidak terdaftar (anti enumerasi akun).
        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($token), 'created_at' => now()],
            );

            $link = route('password.reset', ['token' => $token, 'email' => $user->email]);

            Mail::raw(
                "Halo {$user->name},\n\n" .
                "Anda menerima email ini karena kami menerima permintaan reset sandi untuk akun Gudang Gadget.\n\n" .
                "Klik tautan berikut untuk membuat sandi baru:\n{$link}\n\n" .
                "Tautan berlaku selama 60 menit. Jika Anda tidak meminta reset sandi, abaikan email ini.",
                fn ($m) => $m->to($user->email)->subject('Reset Sandi Akun Gudang Gadget')
            );
        }

        return back()->with('success', 'Tautan reset sandi telah dikirim ke email Anda (bila email terdaftar).');
    }

    public function showReset(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    public function storeReset(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (! $record || ! Hash::check($data['token'], $record->token) || \Carbon\Carbon::parse($record->created_at)->lt(now()->subMinutes(60))) {
            return back()->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->password = $data['password'];
        $user->save();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('landing')->with('success', 'Sandi berhasil diubah. Selamat datang kembali!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah keluar.');
    }
}