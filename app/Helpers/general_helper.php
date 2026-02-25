<?php

/**
 * General Helper for formatting dates and other common tasks
 */

if (!function_exists('bulan_indo')) {
    /**
     * Konversi angka bulan ke nama bulan Indonesia
     */
    function bulan_indo(int $bulan): string
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $nama[$bulan] ?? '';
    }
}

if (!function_exists('tanggal_indo')) {
    /**
     * Format tanggal ke format Indonesia: "21 Februari 2026"
     */
    function tanggal_indo(string $date): string
    {
        $timestamp = strtotime($date);
        $hari  = date('j', $timestamp);
        $bulan = bulan_indo((int) date('n', $timestamp));
        $tahun = date('Y', $timestamp);
        return "$hari $bulan $tahun";
    }
}

if (!function_exists('hari_indo')) {
    /**
     * Konversi tanggal ke nama hari bahasa Indonesia
     */
    function hari_indo(string $date): string
    {
        $namaHari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $timestamp = strtotime($date);
        return $namaHari[date('l', $timestamp)] ?? date('l', $timestamp);
    }
}

if (!function_exists('tanggal_indo_panjang')) {
    /**
     * Format tanggal lengkap: "Jumat, 21 Februari 2026"  
     */
    function tanggal_indo_panjang(string $date): string
    {
        return hari_indo($date) . ', ' . tanggal_indo($date);
    }
}
