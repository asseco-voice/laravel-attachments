<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('attachables', function (Blueprint $table) {
            $table->id();

            if (config('asseco-attachments.migrations.uuid')) {
                $table->foreignUuid('attachment_id')->constrained()->onDelete('cascade');
                $table->uuidMorphs('attachable');
            } else {
                $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
                $table->morphs('attachable');
            }

            $table->timestamps();
            $table->unique(['attachment_id', 'attachable_type', 'attachable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('attachables');
    }
}
