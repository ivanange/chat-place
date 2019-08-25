<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            //  general settings 
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            // colums 
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 50)->unique()->nullable();
            $table->string('avatar', 500)->nullable();
            $table->string('desc', 500)->nullable();
            $table->string('password', 500);
            $table->text('settings')->nullable();
            $table->unsignedTinyInteger('state')->default(0);
            $table->unsignedTinyInteger('visibility')->default(1);
            $table->string('link', 100)->unique();

            //indexes
            $table->index(["state", "name", "email", "link"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
