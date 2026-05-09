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
        if(!Schema::hasTable('royalties')){
            Schema::create('royalties', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('order_id')->index()->nullable();
                $table->foreign('order_id')->references('id')->on('orders');

                $table->unsignedBigInteger('book_id')->index()->nullable();
                $table->foreign('book_id')->references('id')->on('books');

                $table->text('book_details')->nullable()->comment('Json detail of item');
                $table->double('author_royalty', 10, 2)->nullable()->comment('Book royalty for author');
                $table->double('app_royalty', 10, 2)->nullable()->comment('Book royalty for app');

                $table->enum('payment_status', ['PENDING', 'PROCESS'])->default('PENDING')->index();

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
        Schema::dropIfExists('royalties');
    }
};
