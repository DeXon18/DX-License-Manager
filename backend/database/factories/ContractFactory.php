<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'contract_number' => 'CONH' . $this->faker->unique()->numberBetween(1000000, 9999999),
            'client_id' => Client::factory(),
            'vendor_id' => 1, // Assume Siemens exists or use a random one
            'cost_center' => '710-PDM',
            'type_product' => 'NX',
            'sub_product' => 'CAD',
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => 'Cerrado',
            'comment' => $this->faker->sentence(),
        ];
    }
}
