<?php

declare(strict_types=1);

namespace Asseco\Attachments\Tests\Feature\Http\Controllers;

use Asseco\Attachments\App\Models\Attachment;
use Asseco\Attachments\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AttachmentControllerTest extends TestCase
{
    /** @test */
    public function can_fetch_all_attachment_fields()
    {
        $this
            ->getJson(route('attachments.index'))
            ->assertJsonCount(0);

        Attachment::factory()->count(5)->create();

        $this
            ->getJson(route('attachments.index'))
            ->assertJsonCount(5);

        $this->assertCount(5, Attachment::all());
    }

    /** @test */
    public function rejects_creating_attachment_with_invalid_name()
    {
        $request = Attachment::factory()->make([
            'name' => Str::random(101),
        ])->toArray();

        $this
            ->postJson(route('attachments.store'), $request)
            ->assertStatus(422);
    }

    /** @test */
    public function creates_attachment()
    {
        $request = Attachment::factory()->make()->toArray();
        $name = 'testing.xlsx';
        $path = 'tests/assets/' . $name;
        $file = new UploadedFile($path, $name, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $route = 'api/attachments';

        $params = [];
        $response = $this->call('POST', $route, $params, [], ['attachment' => $file]);
        $response->assertStatus(200);
    }

    /** @test */
    public function can_return_attachment_by_id()
    {
        Attachment::factory()->count(5)->create();

        $this
            ->getJson(route('attachments.show', 3))
            ->assertJsonFragment(['id' => 3]);
    }

    /** @test */
    public function can_delete_attachment()
    {
        $attachment = Attachment::factory()->create();

        $this->assertCount(1, Attachment::all());

        $this
            ->deleteJson(route('attachments.destroy', $attachment->id))
            ->assertOk();

        $this->assertCount(0, Attachment::all());
    }
}
