<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdviserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adviser_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advisor_id');
                $table->unsignedBigInteger('year_id');
                $table->unsignedBigInteger('company_id');
                $table->foreign('advisor_id')->references('id')->on('adviser_accounts');
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
        Schema::dropIfExists('adviser_accounts');
    }
}
