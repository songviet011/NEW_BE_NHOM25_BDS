<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Legacy helper migration kept empty.
     *
     * All tables are now created by dedicated create_* migrations.
     */
    public function up(): void
    {
        // intentionally left blank
    }

    public function down(): void
    {
        // intentionally left blank
    }
};
