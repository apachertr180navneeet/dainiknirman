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
        if(!Schema::hasTable('cms')){
            Schema::create('cms', function (Blueprint $table) {
                $table->id();
                $table->string('title', 100);
                $table->string('slug', 100)->nullable();
                $table->text('description')->nullable();
                $table->string('image', 100)->comment("cms page image name.")->nullable();
                $table->string('meta_title', 100);
                $table->text('meta_keywords')->nullable();
                $table->text('meta_description')->nullable();
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
        Schema::dropIfExists('cms');
    }
};
