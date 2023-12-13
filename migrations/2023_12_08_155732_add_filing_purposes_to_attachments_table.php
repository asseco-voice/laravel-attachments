<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilingPurposesToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            if (config('asseco-attachments.migrations.uuid')) {
                $table->foreignUuid('filing_purpose_id')->nullable()->constrained();
            } else {
                $table->foreignId('filing_purpose_id')->nullable()->constrained();
            }
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
            $table->dropColumn('filing_purpose_id');
        });
    }
}
