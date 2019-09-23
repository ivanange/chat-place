<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Block extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block', function (Blueprint $table) {

             //  general settings 
             $table->charset = 'utf8';
             $table->collation = 'utf8_unicode_ci';
 
             // colums 
             $table->unsignedBigInteger('blockerid');
             $table->unsignedBigInteger('blockedid');

             //indexes
             $table->primary(['blockerid', 'blockedid']);
             $table->foreign('blockerid')->references('id')->on('users');
             $table->foreign('blockedid')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block');
    }
}
