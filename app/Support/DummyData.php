<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * SEMENTARA (tahap frontend).
 * Hanya 2 akun user untuk preview role. Data lain sengaja kosong.
 */
class DummyData
{
    public static function users(): Collection
    {
        return collect([
            (object) ['id' => 1, 'name' => 'Owner Utama',  'username' => 'owner_utama',  'email' => 'owner@sim-b.com', 'phone' => '081200000001', 'role' => 'owner',   'created_by' => 'Sistem Utama'],
            (object) ['id' => 2, 'name' => 'Budi Santoso', 'username' => 'budi_admin',   'email' => 'budi@sim-b.com',  'phone' => '081200000002', 'role' => 'admin',   'created_by' => 'owner_utama'],
        ]);
    }

    public static function categories(): Collection { return collect(); }
    public static function suppliers(): Collection  { return collect(); }
    public static function items(): Collection      { return collect(); }
    public static function mutations(): Collection  { return collect(); }
    public static function loginLogs(): Collection  { return collect(); }
    public static function accessLogs(): Collection { return collect(); }
}
