<?php

use App\Models\UsersDrug;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=database/migrations/2025_10_16_100630_create_users_drugs_table.php
     */
    public function up(): void
    {
        $tableName = UsersDrug::getTableName();
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('rxcui');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'rxcui']);

            $table->index('rxcui');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(UsersDrug::getTableName());
    }
};
