<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Client;

class DemoContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function up()
    {
        // En un Seeder normal usamos run(), pero como quiero que sea fácil de ejecutar...
    }

    public function run(): void
    {
        $clientId = 136;
        $client = Client::find($clientId);

        if (!$client) {
            $this->command->error("Cliente ID 136 no encontrado.");
            return;
        }

        $contacts = [
            [
                'name' => 'Oskar Blazquez',
                'email' => 'dexon18@gmail.com',
                'position' => 'IT Manager',
                'phone' => '669012408',
            ],
            [
                'name' => 'Laura García',
                'email' => 'laura.garcia@demo.es',
                'position' => 'Compras',
                'phone' => '600111222',
            ],
            [
                'name' => 'Soporte Técnico DX',
                'email' => 'soporte@dxpro.es',
                'position' => 'Partner Técnico',
                'phone' => '900800700',
            ],
        ];

        foreach ($contacts as $contactData) {
            Contact::updateOrCreate(
                ['email' => $contactData['email'], 'client_id' => $clientId],
                $contactData
            );
        }

        $this->command->info("Contactos DEMO añadidos al cliente 136 correctamente.");
    }
}
