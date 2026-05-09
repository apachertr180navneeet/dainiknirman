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
        if(!Schema::hasTable("order_details")){
            Schema::create('order_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index()->nullable();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->enum('type', ['BOOK', 'EBOOK', 'ANTHOLOGY', 'MAGAZINE'])->default('BOOK');
                $table->unsignedBigInteger('type_id')->index()->nullable();
                $table->text('item_details')->nullable()->comment('Json detail of item');
                $table->double('amount', 10, 2)->comment('Total item amount.');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
