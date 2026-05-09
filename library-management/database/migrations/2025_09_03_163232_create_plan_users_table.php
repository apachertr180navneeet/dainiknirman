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
        if(!Schema::hasTable('plan_users')){
            Schema::create('plan_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->unsignedBigInteger('subscription_id')->index()->nullable();
                $table->foreign('subscription_id')->references('id')->on('users');
                $table->double('subscription_amount', 10, 2)->comment('Total amount.');
                $table->text('subscription_details')->nullable()->comment('Json of subscription details');
                $table->datetime('start_date')->nullable();
                $table->datetime('end_date')->nullable();
                $table->string('order_number', 100)->comment('Custom order no. system generated');
                $table->string('razorpay_order_id', 100)->comment('Razorpay payment gateway order id');
                $table->text('payment_details')->nullable()->comment('Payment gateway final response json');
                $table->string('payment_gateway', 100)->comment('Name of payment gateway');
                $table->enum('transaction_status', ['PENDING', 'IN_PROCESS', 'COMPLETE', 'CANCELED', 'SUCCESS'])->default('PENDING');
                $table->enum('payment_mode', ['ONLINE', 'CASH'])->default("ONLINE");
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
        Schema::dropIfExists('plan_users');
    }
};
