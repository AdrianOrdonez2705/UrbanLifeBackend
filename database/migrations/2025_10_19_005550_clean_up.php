<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // A list of tables to drop.
        $tables = [
            'users',
            'sessions',
            'personal_access_tokens',
            'password_reset_tokens',
            'jobs',
            'job_batches',
            'failed_jobs',
            'cache_locks',
            'cache',
        ];

        // Disable foreign key checks to avoid errors during deletion.
        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration is designed for a one-way cleanup.
        // Recreating these tables would require running the original migrations.
        // It's safer to leave this empty or throw an exception.
        throw new \Exception('This migration cannot be reversed. Restore from a backup or run original migrations if needed.');
    }
};
