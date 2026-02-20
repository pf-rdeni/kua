<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CopyAssets extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Assets';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'assets:copy';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Copy AdminLTE assets from vendor to public folder';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'assets:copy';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Copying AdminLTE assets...', 'yellow');

        $sourcePath = ROOTPATH . 'vendor/almasaeed2010/adminlte/';
        $destPath   = FCPATH . 'template/backend/';

        if (!is_dir($sourcePath)) {
            CLI::error('AdminLTE vendor package not found at: ' . $sourcePath);
            return;
        }

        if (!is_dir($destPath)) {
            mkdir($destPath, 0755, true);
        }

        $folders = ['dist', 'plugins'];

        foreach ($folders as $folder) {
            $src = $sourcePath . $folder;
            $dst = $destPath . $folder;

            if (is_dir($src)) {
                CLI::write("Copying {$folder}...", 'white');
                $this->recurseCopy($src, $dst);
            } else {
                CLI::write("Source folder not found: {$src}", 'red');
            }
        }

        CLI::write('Assets copied successfully!', 'green');
    }

    /**
     * Recursive Copy
     */
    private function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
