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
        if(!Schema::hasTable("subscriptions")){
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string("name", 100);
                $table->text("description")->nullable();
                $table->double("amount", 10,2);
                $table->integer("validity")->comment("Validity in months");
                $table->enum('type', ['AUTHOR', 'READER'])->default("AUTHOR")->comment("Plan for user role type	");
                $table->boolean('status')->default(1)->index();
                $table->unsignedBigInteger('created_by')->index();
                $table->foreign('created_by')->references('id')->on('users');
                $table->unsignedBigInteger('updated_by')->index();
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
        Schema::dropIfExists('subscriptions');
    }
};
