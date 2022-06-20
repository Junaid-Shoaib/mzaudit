<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdviserConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adviser_confirmations', function (Blueprint $table) {
                $table->id();
                $table->date('sent')->nullable();
                $table->date('remind_first')->nullable();
                $table->date('remind_second')->nullable();
                $table->date('received')->nullable();
                $table->tinyInteger('enabled')->default('1');
                $table->unsignedBigInteger('advisor_id');
                $table->unsignedBigInteger('year_id');
                $table->unsignedBigInteger('company_id');
                $table->foreign('advisor_id')->references('id')->on('advisors');
                $table->foreign('year_id')->references('id')->on('years');
                $table->foreign('company_id')->references('id')->on('companies');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adviser_confirmations');
    }
}
