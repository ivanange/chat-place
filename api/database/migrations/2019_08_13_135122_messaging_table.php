<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MessagingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messaging', function (Blueprint $table) {

            //  general settings 
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            // colums 
            $table->unsignedBigInteger('cid');
            $table->unsignedBigInteger('uid');
            $table->unsignedBigInteger('mid');
            $table->unsignedTinyInteger('state')->default(0);
            $table->dateTime('time');

            //indexes
            $table->foreign('cid')->references('id')->on('chats');
            $table->foreign('uid')->references('id')->on('users');
            $table->foreign('mid')->references('id')->on('messages');
            $table->primary(['cid', 'uid', 'mid']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messaging');
    }
}
