<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\PrelovedBook;
use App\Models\RakBukuUser;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Request $request)
    {
        $user = User::with('role')->find(auth()->id());
        $tab = $request->get('tab', 'shelf');
        $shelfTab = $request->get('shelf', 'sedang_dibaca');

        $shelfBooks = RakBukuUser::where('user_id', auth()->id())
            ->where('status', $shelfTab)
            ->join('buku', 'buku.id', '=', 'rak_buku_user.buku_id')
            ->select('rak_buku_user.*', 'buku.judul', 'buku.penulis', 'buku.cover_buku', 'buku.genre_buku')
            ->orderByDesc('rak_buku_user.updated_at')
            ->get();

        $shelfCounts = [
            'sedang_dibaca' => RakBukuUser::where('user_id', auth()->id())->where('status', 'sedang_dibaca')->count(),
            'selesai'       => RakBukuUser::where('user_id', auth()->id())->where('status', 'selesai')->count(),
            'wishlist'      => RakBukuUser::where('user_id', auth()->id())->where('status', 'wishlist')->count(),
        ];

        $sales = PrelovedBook::where('user_id', auth()->id())->orderByDesc('created_at')->get();

        return view('profile.index', compact('user', 'tab', 'shelfTab', 'shelfBooks', 'shelfCounts', 'sales'));
    }

    public function edit()
    {
        $user = User::find(auth()->id());
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        if ($request->has('role_id') && (int)$request->role_id === 1) {
            abort(403, 'Forbidden: Role escalation detected.');
        }

        $user = User::find(auth()->id());
        $username = trim($request->username ?? '');
        $email = trim($request->email ?? '');
        $bio = trim($request->bio ?? '');

        $errors = [];

        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        $existingUser = User::where('username', $username)->where('id', '!=', auth()->id())->first();
        if ($existingUser) $errors[] = 'Username sudah digunakan.';

        $existingEmail = User::where('email', $email)->where('id', '!=', auth()->id())->first();
        if ($existingEmail) $errors[] = 'Email sudah digunakan.';

        if (!empty($errors)) {
            return redirect('/profile/edit')->with('error', implode('<br>', $errors));
        }

        $user->update(['username' => $username, 'email' => $email, 'bio' => $bio]);

        // Handle password change
        if (!empty($request->new_password)) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect('/profile/edit')->with('error', 'Password saat ini salah.');
            }
            if (strlen($request->new_password) < 8) {
                return redirect('/profile/edit')->with('error', 'Password baru minimal 8 karakter.');
            }
            $user->update(['password' => $request->new_password]);
        }

        // Handle photo upload
        if ($request->hasFile('foto_profil')) {
            $photo = $this->uploadService->uploadImage($request->file('foto_profil'), 'profiles');
            if ($photo) {
                if ($user->foto_profil) {
                    $this->uploadService->deleteUpload('profiles', $user->foto_profil);
                }
                $user->update(['foto_profil' => $photo]);
            }
        }

        return redirect('/profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
