<?php

declare(strict_types=1);

namespace Asseco\Attachments\Database\Factories;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function modelName()
    {
        return config('asseco-attachments.models.attachment');
    }

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
