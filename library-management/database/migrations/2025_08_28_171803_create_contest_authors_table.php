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
        if(!Schema::hasTable("contest_authors")){
            Schema::create('contest_authors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contest_id')->index()->nullable();
                $table->foreign('contest_id')->references('id')->on('contests');
                $table->string("contest_title");
                $table->date("contest_date")->comment("Contest publish date or day of contest to show")->nullable();
                $table->text("contest_description");
                $table->string("title");
                $table->longText("description");
                $table->text("remark")->nullable();
                $table->boolean("is_accept_terms")->default(false);
                $table->integer("rank")->comment("Result of the contest author")->nullable();
                $table->text("admin_remark")->comment("Admin can add remark while updating result.")->nullable();
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
        Schema::dropIfExists('contest_authors');
    }
};
