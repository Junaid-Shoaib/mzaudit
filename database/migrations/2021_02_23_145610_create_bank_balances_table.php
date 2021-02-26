<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_balances', function (Blueprint $table) {
            $table->id();
            $table->decimal('ledger',14,2)->nullable();
            $table->decimal('statement',14,2)->nullable();
            $table->decimal('confirmation',14,2)->nullable();
            $table->tinyInteger('enabled')->default('1');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('account_id')->references('id')->on('bank_accounts');
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
        Schema::dropIfExists('bank_balances');
    }
}
