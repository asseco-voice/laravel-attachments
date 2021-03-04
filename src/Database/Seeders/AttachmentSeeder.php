<?php

declare(strict_types=1);

namespace Asseco\Attachments\Database\Seeders;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $attachments = Attachment::factory()->count(50)->raw();

        Attachment::query()->insert($attachments);
    }
}
