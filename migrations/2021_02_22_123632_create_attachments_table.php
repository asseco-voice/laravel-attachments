<?php

declare(strict_types=1);

use Asseco\BlueprintAudit\App\MigrationMethodPicker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            if (config('asseco-attachments.migrations.uuid')) {
                $table->uuid('id')->primary();
            } else {
                $table->id();
            }

            $table->string('name', 255);
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->string('path', 512);
            $table->string('hash');

            MigrationMethodPicker::pick($table, config('asseco-attachments.migrations.timestamps'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
}
