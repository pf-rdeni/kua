<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: CreateKeuanganTables
 * Membuat 5 tabel untuk modul keuangan bendahara multi-entitas.
 *
 * Tabel yang dibuat:
 *  1. tbl_keuangan_kategori  — Kategori pemasukan/pengeluaran
 *  2. tbl_keuangan_kas       — Kas/rekening per entitas (termasuk per masjid)
 *  3. tbl_keuangan_transaksi — Semua transaksi pemasukan & pengeluaran
 *  4. tbl_keuangan_iuran_setting — Konfigurasi jenis iuran per entitas
 *  5. tbl_keuangan_iuran_anggota — Pencatatan bayar iuran per anggota
 */
class CreateKeuanganTables extends Migration
{
    public function up()
    {
        // ============================================================
        // 1. TABEL KATEGORI KEUANGAN
        // Menyimpan kategori seperti: Infaq, Sedekah, Operasional, dll.
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nama kategori transaksi, misal: Infaq, Operasional',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['pemasukan', 'pengeluaran', 'keduanya'],
                'default'    => 'keduanya',
                'comment'    => 'Jenis transaksi yang berlaku untuk kategori ini',
            ],
            'warna_badge' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'secondary',
                'comment'    => 'Warna badge Bootstrap: primary, success, danger, dll',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_keuangan_kategori', true);

        // Seed kategori default
        $this->db->table('tbl_keuangan_kategori')->insertBatch([
            ['nama_kategori' => 'Infaq',           'jenis' => 'pemasukan',   'warna_badge' => 'success',  'deskripsi' => 'Penerimaan infaq dari jamaah', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Sedekah',          'jenis' => 'pemasukan',   'warna_badge' => 'success',  'deskripsi' => 'Penerimaan sedekah', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Iuran Anggota',    'jenis' => 'pemasukan',   'warna_badge' => 'info',     'deskripsi' => 'Iuran rutin dari anggota/personil', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Bantuan/Hibah',    'jenis' => 'pemasukan',   'warna_badge' => 'primary',  'deskripsi' => 'Bantuan atau hibah dari pihak luar', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Penerimaan Lain',  'jenis' => 'pemasukan',   'warna_badge' => 'secondary','deskripsi' => 'Penerimaan di luar kategori yang ada', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Operasional',      'jenis' => 'pengeluaran', 'warna_badge' => 'warning',  'deskripsi' => 'Biaya operasional rutin', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Pemeliharaan',     'jenis' => 'pengeluaran', 'warna_badge' => 'warning',  'deskripsi' => 'Biaya pemeliharaan fasilitas', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Honor/Insentif',   'jenis' => 'pengeluaran', 'warna_badge' => 'danger',   'deskripsi' => 'Pembayaran honor atau insentif', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Pengeluaran Lain', 'jenis' => 'pengeluaran', 'warna_badge' => 'secondary','deskripsi' => 'Pengeluaran di luar kategori yang ada', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);

        // ============================================================
        // 2. TABEL KAS
        // Menyimpan kas/rekening per entitas.
        // entitas_type: misal 'masjid_mushola', 'mubaligh'
        // entitas_id: ID spesifik (misal ID masjid), null jika satu kas per tipe
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Tipe entitas: masjid_mushola, mubaligh, majelis_taklim, dll',
            ],
            'entitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID entitas spesifik (misal id_masjid_mushola), null berarti kas berlaku untuk seluruh tipe',
            ],
            'nama_kas' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'comment'    => 'Nama kas, misal: Kas Masjid Al-Falah, Kas Mubaligh',
            ],
            'saldo_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'comment'    => 'Saldo awal kas ketika pertama kali dibuat',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID user pembuat (dari tbl_auth_users)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['entitas_type', 'entitas_id']); // Index untuk query cepat per entitas
        $this->forge->createTable('tbl_keuangan_kas', true);

        // ============================================================
        // 3. TABEL TRANSAKSI KEUANGAN
        // Semua pemasukan dan pengeluaran dicatat di sini.
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK ke tbl_keuangan_kas, nullable agar bisa tanpa kas',
            ],
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Tipe entitas pemilik transaksi',
            ],
            'entitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID entitas spesifik (misal id masjid), null jika berlaku ke seluruh tipe',
            ],
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK ke tbl_keuangan_kategori',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['pemasukan', 'pengeluaran'],
                'comment'    => 'Jenis transaksi',
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'comment'    => 'Nominal transaksi dalam Rupiah',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Deskripsi detail transaksi',
            ],
            'tanggal_transaksi' => [
                'type'    => 'DATE',
                'comment' => 'Tanggal terjadinya transaksi',
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama file bukti transaksi (foto kuitansi, dll)',
            ],
            'no_referensi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nomor referensi/kuitansi opsional',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID user yang menginput transaksi',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['entitas_type', 'entitas_id']); // Index untuk query per entitas
        $this->forge->addKey('tanggal_transaksi'); // Index untuk query per tanggal/periode
        $this->forge->createTable('tbl_keuangan_transaksi', true);

        // ============================================================
        // 4. TABEL SETTING IURAN
        // Konfigurasi jenis iuran per entitas.
        // Periode: harian, mingguan, bulanan, tahunan, sekali
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'entitas_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Tipe entitas: mubaligh, majelis_taklim, dll',
            ],
            'entitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID entitas spesifik, null = berlaku untuk seluruh entitas tipe ini',
            ],
            'nama_iuran' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'comment'    => 'Nama iuran, misal: Iuran Bulanan, Iuran Tahunan 2025',
            ],
            'periode' => [
                'type'       => 'ENUM',
                'constraint' => ['harian', 'mingguan', 'bulanan', 'tahunan', 'sekali'],
                'default'    => 'bulanan',
                'comment'    => 'Frekuensi iuran',
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'comment'    => 'Nominal iuran per periode',
            ],
            'tanggal_mulai' => [
                'type'    => 'DATE',
                'comment' => 'Tanggal mulai berlakunya iuran',
            ],
            'tanggal_selesai' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal selesai iuran, null = tidak ada batas',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['entitas_type', 'entitas_id']);
        $this->forge->createTable('tbl_keuangan_iuran_setting', true);

        // ============================================================
        // 5. TABEL IURAN ANGGOTA
        // Pencatatan pembayaran iuran per anggota (personil).
        // periode_bayar: "2025-01" untuk bulanan, "2025" untuk tahunan,
        //               "2025-01-15" untuk harian/sekali
        // ============================================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_iuran_setting' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'FK ke tbl_keuangan_iuran_setting',
            ],
            'id_personil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'FK ke tbl_personil',
            ],
            'periode_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'Format periode: "2025-01" (bulanan), "2025" (tahunan), "2025-01-15" (harian/sekali)',
            ],
            'tanggal_bayar' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal aktual pembayaran diterima',
            ],
            'jumlah_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'comment'    => 'Jumlah yang dibayarkan (bisa berbeda dari nominal setting)',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['lunas', 'sebagian', 'belum'],
                'default'    => 'lunas',
                'comment'    => 'Status pembayaran iuran',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_iuran_setting');
        $this->forge->addKey('id_personil');
        // Index unique untuk mencegah double-entry per anggota per periode
        $this->forge->addUniqueKey(['id_iuran_setting', 'id_personil', 'periode_bayar']);
        $this->forge->createTable('tbl_keuangan_iuran_anggota', true);
    }

    public function down()
    {
        // Hapus tabel dalam urutan terbalik (hindari FK issue)
        $this->forge->dropTable('tbl_keuangan_iuran_anggota', true);
        $this->forge->dropTable('tbl_keuangan_iuran_setting', true);
        $this->forge->dropTable('tbl_keuangan_transaksi', true);
        $this->forge->dropTable('tbl_keuangan_kas', true);
        $this->forge->dropTable('tbl_keuangan_kategori', true);
    }
}
