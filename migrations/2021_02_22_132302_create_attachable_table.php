<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('attachable', function (Blueprint $table) {
            // Columns
            $table->integer('attachment_id')->unsigned();
            $table->morphs('attachable');
            $table->timestamps();

            // Indexes
            $table->unique(['attachment_id', 'attachable_id', 'attachable_type'], 'aßttachable_ids_type_unique');
            $table->foreign('attachment_id')->references('id')->on('attachments')
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
        Schema::dropIfExists('attachable');
    }
}
