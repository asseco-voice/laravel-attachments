<?php

namespace Database\Factories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->word,
            'mime_type'  => $this->faker->mimeType,
            'size'       => $this->faker->numberBetween(10, 10000),
            'path'       => $this->faker->url,
            'hash'       => $this->faker->sha1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
