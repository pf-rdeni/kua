<?php

namespace App\Models;

use CodeIgniter\Model;

class MubalighModel extends Model
{
    protected $table            = 'tbl_mubaligh';
    protected $primaryKey       = 'id_mubaligh';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kelurahan_desa',
        'no_hp',
        'pendidikan_terakhir',
        'pekerjaan',
        'status_aktif',
        'foto',
        'latitude',
        'longitude',
    ];

    // ============================================================
    // Validation Rules
    // ============================================================
    protected $validationRules = [
        'nama_lengkap' => 'required|min_length[3]|max_length[255]',
        'nik'          => 'permit_empty|exact_length[16]|numeric',
        'jenis_kelamin' => 'required|in_list[L,P]',
        'no_hp'        => 'permit_empty|max_length[20]',
    ];

    protected $validationMessages = [
        'nama_lengkap' => [
            'required'   => 'Nama lengkap wajib diisi.',
            'min_length' => 'Nama lengkap minimal 3 karakter.',
        ],
        'nik' => [
            'exact_length' => 'NIK harus 16 digit.',
            'numeric'      => 'NIK harus berupa angka.',
        ],
        'jenis_kelamin' => [
            'required' => 'Jenis kelamin wajib dipilih.',
            'in_list'  => 'Jenis kelamin tidak valid.',
        ],
    ];

    protected $skipValidation = false;

    // ============================================================
    // Custom Methods
    // ============================================================

    /**
     * Dapatkan semua data mubaligh yang aktif
     */
    public function getAktif()
    {
        return $this->where('status_aktif', 1)->findAll();
    }

    /**
     * Dapatkan data mubaligh dengan pencarian
     */
    public function search(string $keyword)
    {
        return $this->groupStart()
                    ->like('nama_lengkap', $keyword)
                    ->orLike('nik', $keyword)
                    ->orLike('alamat', $keyword)
                    ->orLike('kelurahan_desa', $keyword)
                    ->orLike('pekerjaan', $keyword)
                    ->groupEnd();
    }
}
