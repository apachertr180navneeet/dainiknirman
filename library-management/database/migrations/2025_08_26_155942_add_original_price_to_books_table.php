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
        if(!Schema::hasColumns('books', ['original_price'])){
            Schema::table('books', function (Blueprint $table) {
                $table->double('original_price', 10,2)->nullable()->after('book_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumns('books', ['original_price'])){
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('original_price');
            });
        }
    }
};
