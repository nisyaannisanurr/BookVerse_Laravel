<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MitraVerification;
use App\Models\LogAktivitas;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MitraAuthController extends Controller
{
    protected FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/');
        }
        return view('auth.login-mitra');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            // Pastikan yang login memang akun Mitra (role 4)
            if ($user->role_id !== 4) {
                auth()->logout();
                $request->session()->invalidate();
                return back()->with('error', 'Akun ini bukan akun Mitra. Silakan gunakan halaman login yang sesuai.')->withInput();
            }

            // Cek status akun (jika ada suspend)
            if (isset($user->status_akun) && $user->status_akun === 'suspended') {
                auth()->logout();
                $request->session()->invalidate();
                return back()->with('error', 'Akun Mitra Anda telah ditangguhkan. Hubungi admin untuk informasi lebih lanjut.')->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended('/donasi/dashboard')->with('success', 'Selamat datang kembali, Mitra!');
        }

        return back()->with('error', 'Email atau password salah.')->withInput();
    }

    public function showRegister()
    {
        $daerahs = \App\Models\InstansiDaerah::where('is_active', true)->orderBy('nama_daerah')->get();
        return view('auth.register-mitra', compact('daerahs'));
    }

    public function register(Request $request)
    {
        // Validate account data
        $username = trim($request->username ?? '');
        $email    = trim($request->email ?? '');
        $password = $request->password ?? '';
        $confirmPassword = $request->password_confirmation ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            return back()->with('error', 'Username, email, dan password harus diisi.')->withInput();
        }

        if (strlen($password) < 8) {
            return back()->with('error', 'Password minimal 8 karakter.')->withInput();
        } elseif (!preg_match('/[A-Z]/', $password)) {
            return back()->with('error', 'Password harus mengandung minimal 1 huruf kapital (A-Z).')->withInput();
        } elseif (!preg_match('/[a-z]/', $password)) {
            return back()->with('error', 'Password harus mengandung minimal 1 huruf kecil (a-z).')->withInput();
        } elseif (!preg_match('/[0-9]/', $password)) {
            return back()->with('error', 'Password harus mengandung minimal 1 angka (0-9).')->withInput();
        }
        
        if ($password !== $confirmPassword) {
            return back()->with('error', 'Konfirmasi password tidak cocok.')->withInput();
        }

        if (User::where('email', $email)->exists()) {
            return back()->with('error', 'Email sudah terdaftar.')->withInput();
        }

        if (User::where('username', $username)->exists()) {
            return back()->with('error', 'Username sudah digunakan.')->withInput();
        }

        // Validate mitra data
        $instansiDaerahId = $request->instansi_daerah_id;
        $namaInstansi = trim($request->nama_instansi ?? '');
        $kategori     = $request->kategori ?? '';
        $alamat       = trim($request->alamat_lengkap ?? '');
        $namaPJ       = trim($request->nama_penanggung_jawab ?? '');
        $noTelepon    = trim($request->no_telepon ?? '');

        if (empty($instansiDaerahId) || empty($namaInstansi) || empty($kategori) || empty($alamat) || empty($namaPJ) || empty($noTelepon)) {
            return back()->with('error', 'Semua data instansi harus diisi.')->withInput();
        }

        if (!\App\Models\InstansiDaerah::where('id', $instansiDaerahId)->where('is_active', true)->exists()) {
            return back()->with('error', 'Instansi Daerah / Desa tidak valid.')->withInput();
        }

        $validKategori = ['Sekolah', 'Panti Asuhan', 'Taman Bacaan'];
        if (!in_array($kategori, $validKategori)) {
            return back()->with('error', 'Kategori tidak valid.')->withInput();
        }

        // Validate required files
        $requiredFiles = ['file_ktp', 'file_legalitas', 'foto_bangunan', 'foto_kegiatan'];
        foreach ($requiredFiles as $field) {
            if (!$request->hasFile($field)) {
                $labels = [
                    'file_ktp'        => 'Foto KTP',
                    'file_legalitas'  => 'Dokumen Legalitas',
                    'foto_bangunan'   => 'Foto Bangunan',
                    'foto_kegiatan'   => 'Foto Kegiatan',
                ];
                return back()->with('error', $labels[$field] . ' harus diunggah.')->withInput();
            }
        }

        // Upload files
        $uploaded = [];
        foreach ($requiredFiles as $field) {
            $result = $this->uploadService->uploadImage($request->file($field), 'mitra');
            if (!$result) {
                // Clean up already uploaded files
                foreach ($uploaded as $file) {
                    $this->uploadService->deleteUpload('mitra', $file);
                }
                return back()->with('error', 'Gagal mengunggah file. Pastikan semua file adalah gambar (JPG/PNG/WEBP) dan ukuran maksimal 5MB.')->withInput();
            }
            $uploaded[$field] = $result;
        }

        // Create user with role Mitra
        $user = User::create([
            'username'  => $username,
            'email'     => $email,
            'password'  => Hash::make($password),
            'role_id'   => 4,
        ]);

        // Create verification record
        MitraVerification::create([
            'user_id'                => $user->id,
            'instansi_daerah_id'     => $instansiDaerahId,
            'nama_instansi'          => $namaInstansi,
            'kategori'               => $kategori,
            'alamat_lengkap'         => $alamat,
            'link_maps'              => trim($request->link_maps ?? ''),
            'nama_penanggung_jawab'  => $namaPJ,
            'no_telepon'             => $noTelepon,
            'file_ktp'               => $uploaded['file_ktp'],
            'file_legalitas'         => $uploaded['file_legalitas'],
            'foto_bangunan'          => $uploaded['foto_bangunan'],
            'foto_kegiatan'          => $uploaded['foto_kegiatan'],
        ]);

        LogAktivitas::record($user->id, 'Daftar Mitra', "Pendaftaran mitra baru: {$namaInstansi} ({$kategori})");

        auth()->login($user);

        return redirect('/donasi/dashboard')->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi oleh admin.');
    }
}
