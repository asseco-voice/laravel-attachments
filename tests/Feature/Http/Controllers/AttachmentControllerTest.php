<?php

declare(strict_types=1);

namespace Asseco\Attachments\Tests\Feature\Http\Controllers;

use Asseco\Attachments\App\Contracts\Attachment;
use Asseco\Attachments\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AttachmentControllerTest extends TestCase
{
    protected Attachment $attachment;

    public function setUp(): void
    {
        parent::setUp();

        $this->attachment = app(Attachment::class);
    }

    /** @test */
    public function can_fetch_all_attachment_fields()
    {
        $this->withoutExceptionHandling();

        $this
            ->getJson(route('attachments.index'))
            ->assertJsonCount(0);

        $this->attachment::factory()->count(5)->create();

        $this
            ->getJson(route('attachments.index'))
            ->assertJsonCount(5);

        $this->assertCount(5, $this->attachment::all());
    }

    /** @test */
    public function rejects_creating_attachment_with_invalid_name()
    {
        $request = $this->attachment::factory()->make([
            'name' => Str::random(101),
        ])->toArray();

        $this
            ->postJson(route('attachments.store'), $request)
            ->assertStatus(422);
    }

    /** @test */
    public function creates_attachment()
    {
        $file = UploadedFile::fake()->create('testing.xlsx');

        $this
            ->postJson(route('attachments.store'), ['attachment' => $file])
            ->assertStatus(200);
    }

    /** @test */
    public function can_return_attachment_by_id()
    {
        $this->attachment::factory()->count(5)->create();

        $this
            ->getJson(route('attachments.show', 3))
            ->assertJsonFragment(['id' => 3]);
    }

    /** @test */
    public function can_delete_attachment()
    {
        $attachment = $this->attachment::factory()->create();

        $this->assertCount(1, $this->attachment::all());

        $this
            ->deleteJson(route('attachments.destroy', $attachment->id))
            ->assertOk();

        $this->assertCount(0, $this->attachment::all());
    }
}
