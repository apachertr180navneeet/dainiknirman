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
        if(!Schema::hasTable("anthologies")){
            Schema::create('anthologies', function (Blueprint $table) {
                $table->id();
                $table->string("title");
                $table->string("author_name");
                $table->longText("description");
                $table->boolean("is_selected")->default(false);
                $table->boolean("is_accept_terms")->default(false);
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
        Schema::dropIfExists('anthologies');
    }
};
