<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('attachmentables', function (Blueprint $table) {
            // Columns
            $table->integer('attachment_id')->unsigned();
            $table->morphs('attachmentable');
            $table->timestamps();

            // Indexes
            $table->unique(['attachment_id', 'attachmentable_id', 'attachmentable_type'], 'attachmentables_ids_type_unique');
            $table->foreign('attachment_id')->references('id')->on(config('attachments'))
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('attachmentables'));
    }
}
