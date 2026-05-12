<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flags = [
            // SIEMENS TOOLS (identities.json keys)
            // No alterar descripciones de herramientas activas
            [
                'key' => 'siemens_nx_suite',
                'label' => 'NX Suite',
                'vendor' => 'Siemens',
                'description' => 'NX, Designcenter, Teamcenter, Simcenter 3D & Amesim',
                'is_active' => true,
            ],
            [
                'key' => 'siemens_star_ccm',
                'label' => 'STAR-CCM+',
                'vendor' => 'Siemens',
                'description' => 'Auditoría y procesamiento de licencias STAR-CCM+',
                'is_active' => true,
            ],

            [
                'key' => 'siemens_heeds',
                'label' => 'HEEDS',
                'vendor' => 'Siemens',
                'description' => 'Auditoría y procesamiento de licencias HEEDS',
                'is_active' => true,
            ],
            [
                'key' => 'siemens_cod',
                'label' => 'COD',
                'vendor' => 'Siemens',
                'description' => 'Generación de certificados de cese en PDF',
                'is_active' => true,
            ],
            [
                'key' => 'siemens_recursos',
                'label' => 'Recursos & enlaces Siemens',
                'vendor' => 'Siemens',
                'description' => 'Documentación oficial y recursos internos Siemens PLM',
                'is_active' => true,
            ],

            // MOLDEX3D
            [
                'key' => 'moldex3d_auditor',
                'label' => 'Moldex3D',
                'vendor' => 'Moldex3D',
                'description' => 'Auditoría y procesamiento de archivos .mac',
                'is_active' => true,
            ],
            [
                'key' => 'moldex3d_recursos',
                'label' => 'Recursos & enlaces Moldex3D',
                'vendor' => 'Moldex3D',
                'description' => 'Documentación oficial y recursos internos Moldex3D',
                'is_active' => true,
            ],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::updateOrCreate(['key' => $flag['key']], $flag);
        }

        // Limpiar keys antiguas si existieran
        FeatureFlag::whereIn('key', ['nx_suite', 'star_ccm', 'heeds', 'request_change', 'resources', 'moldex3d_versions'])->delete();
    }
}
