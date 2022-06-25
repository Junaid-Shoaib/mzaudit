<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColoumnToAdvisorConfirmations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adviser_confirmations', function (Blueprint $table) {
            //
            $table->renameColumn('remind_first', 'reminder');
            $table->renameColumn('remind_second', 'confirm_create');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adviser_confirmations', function (Blueprint $table) {
            //
        });
    }
}
