<?php

namespace Database\Factories;

use App\Models\Sensor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reading>
 */
class ReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sensors = Sensor::query()->get();
        $sensor = $sensors->random(1)[0];

        return [
            'sensor_id' => $sensor->id,
            'reading_value' =>
                $sensor->type === 'rain' ?
                    fake()->randomFloat('2', 0, 375) :
                    fake()->randomFloat('2', 10, 100),
            'unit' =>
                $sensor->type === 'rain' ? 'mm' : 'm',
        ];
    }
}
