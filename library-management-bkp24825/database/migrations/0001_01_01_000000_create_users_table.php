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
        if(!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id')->index()->nullable();
                $table->foreign('role_id')->references('roles')->on('id')->onDelete('set null');
                $table->string('name');
                $table->string('email', 250)->index();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('mobile', 250)->index();
                $table->timestamp('mobile_verified_at')->nullable();
                $table->string('username', 225)->nullable()->index();
                $table->string('password')->nullable();
                $table->enum('gender', ['M', 'F'])->nullable()->default(null)->index();
                $table->text('address')->nullable();
                $table->string('profile_photo')->nullable();
                $table->string('reset_password_code')->nullable();
                $table->enum('device_type', ['A', 'I'])->index()->comment("A = Android, I = IOS");
                $table->string('device_token')->nullable();
                $table->string('is_login')->nullable();
                $table->string('login_datetime')->nullable();
                $table->boolean('status')->default(1)->index();
                
                $table->unsignedBigInteger('created_by')->index()->nullable();
                $table->foreign('created_by')->references('id')->on('users');
                
                $table->unsignedBigInteger('updated_by')->index()->nullable();
                $table->foreign('updated_by')->references('id')->on('users');
                
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        

        if(!Schema::hasTable('password_reset_tokens')){
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email', 250)->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if(!Schema::hasTable('sessions')){
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id', 250)->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
