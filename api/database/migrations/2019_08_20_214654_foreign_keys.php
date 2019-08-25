<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('chats');
            $table->foreign('uid')->references('id')->on('users');
        });

        Schema::table('messaging', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('chats');
            $table->foreign('uid')->references('id')->on('users');
            $table->foreign('mid')->references('id')->on('messages');
        });

        Schema::table('block', function (Blueprint $table) {
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
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign('participants_cid_foreign');
            $table->dropForeign('participants_uid_foreign');
        });

        Schema::table('messaing', function (Blueprint $table) {
            $table->dropForeign('messaing_cid_foreign');
            $table->dropForeign('messaing_uid_foreign');
            $table->dropForeign('messaing_mid_foreign');
        });

        Schema::table('block', function (Blueprint $table) {
            $table->dropForeign('block_blockerid_foreign');
            $table->dropForeign('block_blockedid_foreign');
        });
    }
}
