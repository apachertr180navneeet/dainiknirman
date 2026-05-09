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
        if(!Schema::hasTable('magazines')){
            Schema::create('magazines', function (Blueprint $table) {
                $table->id();
                $table->string('title', 100);
                $table->enum('type', ['D', 'M'])->comment('D:Daily, M:Monthly')->default('D');
                $table->date('date')->comment('date of magazine')->nullable();
                $table->text('description')->nullable();
                $table->string('cover_picture', 100)->comment("Uploaded ebook cover image name.")->nullable();
                $table->string('file_name', 100)->comment("Uploaded ebook pdf name.")->nullable();
                $table->boolean('status')->default(1)->index();
                $table->unsignedBigInteger('created_by')->index()->nullable();
                $table->foreign('created_by')->references('id')->on('users');
                $table->unsignedBigInteger('updated_by')->index()->nullable();
                $table->foreign('updated_by')->references('id')->on('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazines');
    }
};
