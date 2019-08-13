<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {

            //  general settings 
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';


            $table->bigIncrements('id');
            $table->unsignedTinyInteger('type')->default(0);
            $table->string('title', 100)->nullable();
            $table->string('desc', 500)->nullable();
            $table->string('avatar', 500)->nullable();
            $table->string('link', 100)->nullable();

            //indexes
            $table->index(["title", "link"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
