<?php

use App\Models\Drug;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate --path=database/migrations/2025_10_17_142739_create_drugs_table.php
     */
    public function up(): void
    {
        $tableName = Drug::getTableName();
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('rxcui')->unique();
            $table->string('name');
            $table->json('base_names')->nullable();
            $table->json('dose_form_group_names')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Drug::getTableName());
    }
};
