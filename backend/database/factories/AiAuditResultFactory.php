<?php

namespace Database\Factories;

use App\Models\AiAuditResult;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiAuditResultFactory extends Factory
{
    protected $model = AiAuditResult::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'sold_to' => $this->faker->numberBetween(1000000, 9999999),
            'customer_name' => $this->faker->company(),
            'vendor' => 'Siemens',
            'results' => ['total_items' => 10],
            'status' => 'success',
        ];
    }
}
