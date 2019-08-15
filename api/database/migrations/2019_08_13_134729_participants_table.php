<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {

            //  general settings 
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            // colums 
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('uid');
            $table->unsignedInteger('permissions');
            $table->dateTime('time');

            //indexes
            $table->foreign('cid')->references('id')->on('chats');
            $table->foreign('uid')->references('id')->on('users');
            $table->primary(['cid', 'uid']);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
}
