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
        if(!Schema::hasColumns('books', ['book_category'])){
            Schema::table('books', function (Blueprint $table) {
                $table->enum('book_category', ['ANTHOLOGY', 'SINGLE_AUTHOR', 'NATIVE'])->default('ANTHOLOGY')->after('book_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumns('books', ['book_category'])){
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('book_category');
            });
        }
    }
};
