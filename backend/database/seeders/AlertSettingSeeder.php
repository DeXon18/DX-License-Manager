<?php

namespace Database\Seeders;

use App\Models\AlertSetting;
use Illuminate\Database\Seeder;

class AlertSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AlertSetting::updateOrCreate(
            ['id' => 1],
            [
                'threshold_alerta' => 7,
                'threshold_aviso' => 15,
                'threshold_recordatorio' => 30,
                'internal_copy_emails' => 'soporte@dxpro.es',
                'is_active' => true,
            ]
        );
    }
}
