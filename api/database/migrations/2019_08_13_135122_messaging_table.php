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
            $table->unsignedBigInteger('uid')->index();
            $table->unsignedBigInteger('cid')->index();
            $table->unsignedBigInteger('mid')->index();
            $table->unsignedTinyInteger('state')->default(0);
            $table->dateTime('time');

            //indexes
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
