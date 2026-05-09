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
        if(!Schema::hasColumns('author_ebooks', ['publish_date'])){
            Schema::table('author_ebooks', function (Blueprint $table) {
                $table->dateTime('publish_date')->nullable()->after('file_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumns('author_ebooks', ['publish_date'])){
            Schema::table('author_ebooks', function (Blueprint $table) {
                $table->dropColumn('publish_date');
            });
        }
    }
};
