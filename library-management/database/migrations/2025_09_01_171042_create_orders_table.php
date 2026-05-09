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
        if(!Schema::hasTable("orders")){
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->string('order_number', 100)->comment('Custom order no. system generated');
                $table->text('order_details')->nullable()->comment('Json of order details including items');
                $table->datetime('start_date')->nullable();
                $table->datetime('end_date')->nullable();
                $table->integer('total_items')->comment('No. of items in a order');
                $table->double('amount', 10, 2)->comment('Total order amount.');
                $table->enum('payment_mode', ['ONLINE', 'CASH'])->default("ONLINE");
                
                $table->string('razorpay_order_id', 100)->comment('Razorpay payment gateway order id');
                $table->text('payment_details')->nullable()->comment('Payment gateway final response json');
                $table->string('payment_gateway', 100)->comment('Name of payment gateway');
                $table->enum('transaction_status', ['PENDING', 'SUCCESS', 'FAILED', 'CANCELED'])->default('PENDING');
                
                
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
        Schema::dropIfExists('orders');
    }
};
