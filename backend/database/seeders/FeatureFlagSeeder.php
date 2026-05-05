<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flags = [
            [
                'key' => 'siemens_nx_suite',
                'label' => 'NX Suite',
                'vendor' => 'Siemens',
                'description' => 'NX, Designcenter, Teamcenter, Simcenter 3D & Amesim',
                'is_active' => false,
            ],
            [
                'key' => 'siemens_star_ccm',
                'label' => 'STAR-CCM+',
                'vendor' => 'Siemens',
                'description' => 'Auditoría y procesamiento de licencias STAR-CCM+',
                'is_active' => false,
            ],
            [
                'key' => 'siemens_heeds',
                'label' => 'HEEDS',
                'vendor' => 'Siemens',
                'description' => 'Auditoría y procesamiento de licencias HEEDS',
                'is_active' => false,
            ],
            [
                'key' => 'siemens_cod',
                'label' => 'COD',
                'vendor' => 'Siemens',
                'description' => 'Generación de certificados de cese en PDF',
                'is_active' => false,
            ],
            [
                'key' => 'siemens_recursos',
                'label' => 'Recursos & enlaces Siemens',
                'vendor' => 'Siemens',
                'description' => 'Documentación oficial y recursos internos Siemens',
                'is_active' => false,
            ],
            [
                'key' => 'moldex3d_auditor',
                'label' => 'Moldex3D',
                'vendor' => 'Moldex3D',
                'description' => 'Auditoría y procesamiento de archivos .mac',
                'is_active' => false,
            ],
            [
                'key' => 'moldex3d_recursos',
                'label' => 'Recursos & enlaces Moldex3D',
                'vendor' => 'Moldex3D',
                'description' => 'Documentación oficial y recursos internos Moldex3D',
                'is_active' => false,
            ],
        ];

        foreach ($flags as $flag) {
            DB::table('feature_flags')->updateOrInsert(['key' => $flag['key']], $flag);
        }
    }
}
