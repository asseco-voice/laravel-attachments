<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalIdToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('external_id')->nullable()->default(null);

            $table->index('external_id', 'attachments_ext_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });
    }
}
