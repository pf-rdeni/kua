<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ServiceWorkerFilter
 *
 * Menambahkan header `Service-Worker-Allowed` pada response file sw-display.js.
 * Header ini diperlukan agar browser mengizinkan Service Worker didaftarkan
 * dengan scope yang lebih sempit (/display/) dari lokasi file SW itu sendiri (/).
 *
 * Tanpa header ini, browser akan menolak registrasi SW dengan error:
 * "The script has an unsupported MIME type ('text/html')" atau
 * "The path of the provided scope ('/display/') is not under the max scope allowed ('/sw-display.js')."
 */
class ServiceWorkerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Tidak ada aksi sebelum request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tambahkan header yang mengizinkan scope /display/ untuk SW ini
        $response->setHeader('Service-Worker-Allowed', '/display/');

        return $response;
    }
}
