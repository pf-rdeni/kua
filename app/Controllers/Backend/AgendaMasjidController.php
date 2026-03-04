<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\AgendaKegiatanModel;
use App\Models\MasjidMusholaModel;
use App\Models\MajelisTaklimModel;
use App\Models\PersonilModel;

/**
 * AgendaMasjidController (Multi-Entitas)
 * Operator Masjid/Mushola DAN Majelis Taklim dapat mengelola agenda kegiatan mandiri.
 */
class AgendaMasjidController extends BaseController
{
    protected $agendaModel;
    protected $masjidModel;
    protected $majelisModel;
    protected $personilModel;

    public function __construct()
    {
        $this->agendaModel   = new AgendaKegiatanModel();
        $this->masjidModel   = new MasjidMusholaModel();
        $this->majelisModel  = new MajelisTaklimModel();
        $this->personilModel = new PersonilModel();

        if (!function_exists('in_groups')) {
            helper('auth');
        }
    }

    /**
     * Deteksi entitas user yang login
     * @return array|null ['type' => string, 'id' => int]
     */
    private function getEntitasInfo(): ?array
    {
        // Admin: bisa akses semua, ambil dari query string
        if (in_groups('SuperAdmin') || in_groups('Admin')) {
            $type = $this->request->getGet('entitas_type') ?? 'masjid_mushola';
            $id   = (int)($this->request->getGet('entitas_id') ?? $this->request->getGet('masjid_id') ?? 0);
            if ($id) {
                return ['type' => $type, 'id' => $id];
            }
            return null;
        }

        // Operator Masjid/Mushola
        if (in_groups('OperatorMasjidMushola')) {
            $u = user();
            if ($u && $u->entitas_type === 'masjid_mushola' && !empty($u->entitas_id)) {
                return ['type' => 'masjid_mushola', 'id' => (int)$u->entitas_id];
            }
        }

        // Operator Majelis Taklim
        if (in_groups('OperatorMajelisTaklim')) {
            $u = user();
            if ($u && $u->entitas_type === 'majelis_taklim' && !empty($u->entitas_id)) {
                return ['type' => 'majelis_taklim', 'id' => (int)$u->entitas_id];
            }
        }

        return null;
    }

    /**
     * Ambil nama entitas untuk tampilan
     */
    private function getEntitasData(string $type, int $id): ?array
    {
        if ($type === 'masjid_mushola') {
            $data = $this->masjidModel->find($id);
            if ($data) {
                $data['_nama_entitas'] = $data['nama'];
                $data['_jenis_entitas'] = $data['jenis'] ?? 'Masjid';
            }
            return $data;
        }
        if ($type === 'majelis_taklim') {
            $data = $this->majelisModel->find($id);
            if ($data) {
                $data['_nama_entitas'] = $data['nama_majelis_taklim'];
                $data['_jenis_entitas'] = 'Majelis Taklim';
            }
            return $data;
        }
        return null;
    }

    /**
     * Tampilkan daftar agenda + jadwal mubaligh KUA (khusus masjid)
     */
    public function index()
    {
        $entitas = $this->getEntitasInfo();

        if (!$entitas) {
            return redirect()->to(base_url('admin/dashboard'))
                             ->with('error', 'Pilih entitas terlebih dahulu atau akun Anda belum terhubung.');
        }

        $entitasData = $this->getEntitasData($entitas['type'], $entitas['id']);
        if (!$entitasData) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Entitas tidak ditemukan.');
        }

        $bulan = (int)($this->request->getGet('bulan') ?? date('m'));
        $tahun = (int)($this->request->getGet('tahun') ?? date('Y'));

        // Agenda mandiri
        $agendaList = $this->agendaModel->getAgendaDenganDetail($entitas['type'], $entitas['id'], [
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);

        // Jadwal mubaligh KUA (hanya untuk masjid)
        $jadwalKUAByTanggal = [];
        if ($entitas['type'] === 'masjid_mushola') {
            $db = \Config\Database::connect();
            $tahunHijriAktif = $db->table('tbl_tema_ceramah')
                                   ->select('tahun_hijriah')
                                   ->orderBy('id', 'DESC')
                                   ->limit(1)
                                   ->get()->getRowArray()['tahun_hijriah'] ?? '1446 H';

            $jadwalKUA = $this->agendaModel->getJadwalMubalighUntukMasjid($entitas['id'], $tahunHijriAktif);
            foreach ($jadwalKUA as $j) {
                if (!empty($j['tanggal'])) {
                    $jadwalKUAByTanggal[$j['tanggal']][] = $j;
                }
            }
        }

        $tahunList = $this->agendaModel->getTahunTersedia($entitas['type'], $entitas['id']);

        // Daftar entitas untuk admin filter
        $entitasList = [];
        if (in_groups('SuperAdmin') || in_groups('Admin')) {
            if ($entitas['type'] === 'masjid_mushola') {
                foreach ($this->masjidModel->orderBy('nama', 'ASC')->findAll() as $m) {
                    $entitasList[] = ['id' => $m['id_masjid_mushola'], 'nama' => $m['nama'] . ' (' . $m['jenis'] . ')'];
                }
            } else {
                foreach ($this->majelisModel->orderBy('nama_majelis_taklim', 'ASC')->findAll() as $m) {
                    $entitasList[] = ['id' => $m['id_majelis_taklim'], 'nama' => $m['nama_majelis_taklim']];
                }
            }
        }

        $data = [
            'title'               => 'Agenda Kegiatan ' . $entitasData['_jenis_entitas'],
            'entitas'             => $entitasData,
            'entitasType'         => $entitas['type'],
            'entitasId'           => $entitas['id'],
            'agendaList'          => $agendaList,
            'jadwalKUAByTanggal'  => $jadwalKUAByTanggal,
            'bulan'               => $bulan,
            'tahun'               => $tahun,
            'tahunList'           => $tahunList,
            'isAdmin'             => in_groups('SuperAdmin') || in_groups('Admin'),
            'entitasList'         => $entitasList,
        ];

        return view('backend/agenda_masjid/index', $data);
    }

    /**
     * Form tambah agenda
     */
    public function create()
    {
        $entitas = $this->getEntitasInfo();
        if (!$entitas) {
            return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Akun belum terhubung ke entitas.');
        }

        $entitasData = $this->getEntitasData($entitas['type'], $entitas['id']);

        $data = [
            'title'       => 'Tambah Agenda Kegiatan',
            'entitas'     => $entitasData,
            'entitasType' => $entitas['type'],
            'entitasId'   => $entitas['id'],
            'agenda'      => null,
            'isAdmin'     => in_groups('SuperAdmin') || in_groups('Admin'),
        ];

        return view('backend/agenda_masjid/form', $data);
    }

    /**
     * Simpan agenda baru
     */
    public function store()
    {
        $entitas = $this->getEntitasInfo();
        if (!$entitas) {
            return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul_kegiatan' => 'required|min_length[3]|max_length[200]',
            'tanggal'        => 'required|valid_date[Y-m-d]',
            'jenis'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idPersonil     = $this->request->getPost('id_personil') ?: null;
        $namaPenceramah = $this->request->getPost('nama_penceramah') ?: null;

        if ($idPersonil) {
            $personil = $this->personilModel->find($idPersonil);
            if ($personil) {
                $namaPenceramah = $personil['nama_lengkap'];
            }
        }

        $this->agendaModel->save([
            'entitas_type'      => $entitas['type'],
            'entitas_id'        => $entitas['id'],
            'tanggal'           => $this->request->getPost('tanggal'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai') ?: null,
            'waktu_selesai'     => $this->request->getPost('waktu_selesai') ?: null,
            'judul_kegiatan'    => $this->request->getPost('judul_kegiatan'),
            'jenis'             => $this->request->getPost('jenis'),
            'deskripsi'         => $this->request->getPost('deskripsi') ?: null,
            'nama_penceramah'   => $namaPenceramah,
            'id_personil'       => $idPersonil,
            'lokasi'            => $this->request->getPost('lokasi') ?: null,
            'is_published'      => $this->request->getPost('is_published') ?? 1,
            'created_by'        => user()->id,
        ]);

        $redirect = base_url('admin/agenda-masjid');
        if ($entitas['type'] === 'majelis_taklim') {
            $redirect .= '?entitas_type=majelis_taklim&entitas_id=' . $entitas['id'];
        }

        return redirect()->to($redirect)->with('success', 'Agenda berhasil ditambahkan!');
    }

    /**
     * Form edit agenda
     */
    public function edit(int $id)
    {
        $agenda = $this->agendaModel->find($id);
        if (!$agenda) {
            return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Agenda tidak ditemukan.');
        }

        $entitas = $this->getEntitasInfo();

        // Operator hanya bisa edit agenda entitasnya sendiri
        if (!in_groups('SuperAdmin') && !in_groups('Admin')) {
            if ($agenda['entitas_type'] !== $entitas['type'] || $agenda['entitas_id'] != $entitas['id']) {
                return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Akses ditolak.');
            }
        }

        $entitasData = $this->getEntitasData($agenda['entitas_type'], $agenda['entitas_id']);

        $data = [
            'title'       => 'Edit Agenda Kegiatan',
            'entitas'     => $entitasData,
            'entitasType' => $agenda['entitas_type'],
            'entitasId'   => $agenda['entitas_id'],
            'agenda'      => $agenda,
            'isAdmin'     => in_groups('SuperAdmin') || in_groups('Admin'),
        ];

        return view('backend/agenda_masjid/form', $data);
    }

    /**
     * Update agenda
     */
    public function update(int $id)
    {
        $agenda = $this->agendaModel->find($id);
        if (!$agenda) {
            return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Agenda tidak ditemukan.');
        }

        $entitas = $this->getEntitasInfo();
        if (!in_groups('SuperAdmin') && !in_groups('Admin')) {
            if ($agenda['entitas_type'] !== $entitas['type'] || $agenda['entitas_id'] != $entitas['id']) {
                return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Akses ditolak.');
            }
        }

        $rules = [
            'judul_kegiatan' => 'required|min_length[3]|max_length[200]',
            'tanggal'        => 'required|valid_date[Y-m-d]',
            'jenis'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idPersonil     = $this->request->getPost('id_personil') ?: null;
        $namaPenceramah = $this->request->getPost('nama_penceramah') ?: null;

        if ($idPersonil) {
            $personil = $this->personilModel->find($idPersonil);
            if ($personil) {
                $namaPenceramah = $personil['nama_lengkap'];
            }
        }

        $this->agendaModel->update($id, [
            'tanggal'        => $this->request->getPost('tanggal'),
            'waktu_mulai'    => $this->request->getPost('waktu_mulai') ?: null,
            'waktu_selesai'  => $this->request->getPost('waktu_selesai') ?: null,
            'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
            'jenis'          => $this->request->getPost('jenis'),
            'deskripsi'      => $this->request->getPost('deskripsi') ?: null,
            'nama_penceramah'=> $namaPenceramah,
            'id_personil'    => $idPersonil,
            'lokasi'         => $this->request->getPost('lokasi') ?: null,
            'is_published'   => $this->request->getPost('is_published') ?? 1,
        ]);

        return redirect()->to(base_url('admin/agenda-masjid'))
                         ->with('success', 'Agenda berhasil diperbarui!');
    }

    /**
     * Hapus agenda
     */
    public function delete(int $id)
    {
        $agenda = $this->agendaModel->find($id);
        if (!$agenda) {
            return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Agenda tidak ditemukan.');
        }

        $entitas = $this->getEntitasInfo();
        if (!in_groups('SuperAdmin') && !in_groups('Admin')) {
            if ($agenda['entitas_type'] !== $entitas['type'] || $agenda['entitas_id'] != $entitas['id']) {
                return redirect()->to(base_url('admin/agenda-masjid'))->with('error', 'Akses ditolak.');
            }
        }

        $this->agendaModel->delete($id);

        return redirect()->to(base_url('admin/agenda-masjid'))
                         ->with('success', 'Agenda berhasil dihapus.');
    }

    /**
     * AJAX: Cari mubaligh dari database
     */
    public function searchMubaligh()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $keyword = $this->request->getGet('q') ?? '';

        $builder = $this->personilModel->ofType('mubaligh')->where('status_aktif', 1);

        if ($keyword) {
            $builder->groupStart()
                    ->like('nama_lengkap', $keyword)
                    ->orLike('nia', $keyword)
                    ->groupEnd();
        }

        $results = $builder->findAll(20);

        $data = [];
        foreach ($results as $m) {
            $data[] = [
                'id'   => $m['id'],
                'text' => ($m['nia'] ? $m['nia'] . ' — ' : '') . $m['nama_lengkap'],
            ];
        }

        return $this->response->setJSON(['results' => $data]);
    }
}
