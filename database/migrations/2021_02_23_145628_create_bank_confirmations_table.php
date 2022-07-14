<?php

use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\DBAL;

class CreateBankConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_confirmations', function (Blueprint $table) {
            $table->id();
            $table->date('sent')->nullable();
            $table->date('remind_first')->nullable();
            $table->date('remind_second')->nullable();
            $table->date('received')->nullable();
            $table->Longtext('path')->nullable();
            $table->tinyInteger('enabled')->default('1');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('branch_id')->references('id')->on('bank_branches');
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });


    }


    // Schema::table('bank_confirmations', function (Blueprint $table) {
    //     $table->renameColumn('remind_first', 'remind');
    //     $table->renameColumn('remind_second', 'conf_create');

    // });





    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        Schema::dropIfExists('bank_confirmations');
    }
}
