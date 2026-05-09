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
        if(!Schema::hasTable('books')){
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('book_name', 200)->unique();
                $table->string('author_name', 200)->unique();
                $table->dateTime('date')->nullable();
                $table->dateTime('launch_date')->nullable();
                $table->enum('book_type', ['F', 'P'])->default('F')->comment('F = Free, P = Paid');
                $table->decimal('price', 10,2)->nullable();
                $table->string('cover_picture', 200)->nullable();
                $table->string('book_pdf', 200)->nullable();
                $table->text('description')->nullable();
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
        Schema::dropIfExists('books');
    }
};
