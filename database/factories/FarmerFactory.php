<?php

namespace Database\Factories;

use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FarmerProfile>
 */
class FarmerFactory extends Factory
{
    protected $model = FarmerProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role'   => 'farmer',
                'status' => 'active',
            ]),
            'business_name'     => fake()->company() . ' Organic Farms',
            'description'       => fake()->paragraph(),
            'stall_number'      => 'Stall #' . fake()->numberBetween(1, 60),
            'address'           => fake()->streetAddress() . ', ' . fake()->city(),
            'latitude'          => fake()->latitude(36.5, 38.5),
            'longitude'         => fake()->longitude(-123.0, -121.0),
            'operating_days'    => fake()->randomElements(
                ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                fake()->numberBetween(2, 4)
            ),
            'pickup_start_time' => '08:00',
            'pickup_end_time'   => '13:00',
            'is_approved'       => true,
            'rejection_reason'  => null,
        ];
    }

    /**
     * Approved and active farmer state.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved'      => true,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Pending approval farmer state.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved'      => false,
            'rejection_reason' => null,
            'user_id'          => User::factory()->state([
                'role'   => 'farmer',
                'status' => 'pending',
            ]),
        ]);
    }
}
