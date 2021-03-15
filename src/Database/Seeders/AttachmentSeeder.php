<?php

declare(strict_types=1);

namespace Asseco\Attachments\Database\Seeders;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Seeder;

class AttachmentsSeeder extends Seeder
{
    public function run(): void
    {
        Attachment::factory()->count(50)->create();
    }
}
