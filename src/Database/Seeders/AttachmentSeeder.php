<?php

declare(strict_types=1);

namespace Asseco\Attachments\Database\Seeders;

use Asseco\Attachments\App\Contracts\Attachment;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Attachment $attachment */
        $attachment = app(Attachment::class);

        $attachment::factory()->count(50)->create();
    }
}
