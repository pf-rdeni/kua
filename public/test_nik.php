<?php
// Script cepat untuk test logika PersonilApiController::checkNikSharing
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Models\PersonilModel;

try {
    $entitasType = 'mubaligh';
    $entitasId = 4;

    $personilModel = new PersonilModel();
    $currentEntity = $personilModel->where('entitas_type', $entitasType)
                                   ->where('id', $entitasId)
                                   ->first();

    if (!$currentEntity || empty($currentEntity['nik'])) {
        echo "No current entity or NIK.\n";
        exit;
    }

    $siblings = $personilModel->where('nik', $currentEntity['nik'])
                              ->where('id !=', $currentEntity['id'])
                              ->findAll();

    if (empty($siblings)) {
         echo "No siblings.\n";
         exit;
    }

    $db = \Config\Database::connect();
    $builder = $db->table('tbl_entitas_type');
    $types = $builder->get()->getResultArray();
    $typeNames = [];
    foreach ($types as $t) {
         $typeNames[$t['kode']] = $t['nama_label'];
    }

    $rolesArray = [];
    foreach ($siblings as $s) {
        $rolesArray[] = $typeNames[$s['entitas_type']] ?? $s['entitas_type'];
    }

    echo "Success! Siblings found:\n";
    print_r(array_unique($rolesArray));

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
