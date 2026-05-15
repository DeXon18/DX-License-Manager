<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class CodFeatureSeeder extends Seeder
{
    public function run(): void
    {
        FeatureFlag::updateOrCreate(
            ['key' => 'siemens_cod'],
            [
                'vendor' => 'Siemens',
                'label' => 'Generador COD',
                'description' => 'Certificado de Cese oficial de Siemens para cambios de licencia.',
                'is_active' => true,
            ]
        );
    }
}
