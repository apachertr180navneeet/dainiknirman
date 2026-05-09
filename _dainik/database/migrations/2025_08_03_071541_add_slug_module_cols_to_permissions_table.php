<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(!Schema::hasColumns('permissions', ['slug', 'module_name'])){
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('slug', 225)->nullable()->after('guard_name')->unique();
                $table->string('module_name', 225)->nullable()->after('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumns('permissions', ['slug', 'module_name'])){
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('slug');
                $table->dropColumn('module_name');
            });
        }
    }
};
