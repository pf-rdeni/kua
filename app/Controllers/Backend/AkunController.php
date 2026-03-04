<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * AkunController
 *
 * Controller untuk manajemen akun user yang sedang login.
 * Semua user yang sudah login (apapun rolenya) bisa ganti password sendiri.
 */
class AkunController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan halaman form ganti password.
     * Tersedia untuk semua user yang sudah terautentikasi.
     */
    public function gantiPassword()
    {
        $data = [
            'title'      => 'Ganti Password',
            'breadcrumb' => [
                ['title' => 'Home',          'url' => 'admin/dashboard'],
                ['title' => 'Ganti Password', 'url' => ''],
            ],
        ];

        return view('backend/akun/ganti_password', $data);
    }

    /**
     * Proses perubahan password user yang sedang login.
     * Validasi: password lama harus cocok, password baru harus kuat.
     */
    public function prosesGantiPassword()
    {
        $rules = [
            'password_lama'    => 'required',
            'password_baru'    => 'required|min_length[6]',
            'konfirmasi_baru'  => 'required|matches[password_baru]',
        ];

        $messages = [
            'password_lama' => [
                'required' => 'Password lama wajib diisi.',
            ],
            'password_baru' => [
                'required'   => 'Password baru wajib diisi.',
                'min_length' => 'Password baru minimal 6 karakter.',
            ],
            'konfirmasi_baru' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak cocok dengan password baru.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil user yang sedang login
        $userId      = user_id();
        $currentUser = $this->userModel->find($userId);

        if (!$currentUser) {
            return redirect()->to('admin/dashboard')->with('error', 'User tidak ditemukan.');
        }

        // Verifikasi password lama menggunakan Myth\Auth Password helper
        $passwordLama = $this->request->getPost('password_lama');
        if (!\Myth\Auth\Password::verify($passwordLama, $currentUser->password_hash)) {
            return redirect()->back()->withInput()->with('error', 'Password lama yang Anda masukkan salah.');
        }

        // Set password baru
        $passwordBaru           = $this->request->getPost('password_baru');
        $currentUser->password  = $passwordBaru;

        // Simpan perubahan password
        if (!$this->userModel->save($currentUser)) {
            return redirect()->back()->with('error', 'Gagal menyimpan password baru. Silakan coba lagi.');
        }

        return redirect()->to('admin/akun/ganti-password')
            ->with('success', 'Password berhasil diubah. Gunakan password baru Anda untuk login berikutnya.');
    }
}
