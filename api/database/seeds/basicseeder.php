<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\User;
use App\chat;
use App\message;

class basicseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $users = 50;
        $chats = 150;
        $messages = 500;
        $messagings = 500;
        $participants = 400;

        factory(App\User::class, $users)->create();
        factory(App\chat::class, $chats)->create();
        factory(App\message::class, $messages)->create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $chatIds = DB::table('chats')->pluck('id')->toArray();
        $messageIds = DB::table('messages')->pluck('id')->toArray();

        for ($i = 0; $i < $messagings ; $i++ ) {
                try {
                    User::find($userIds[array_rand($userIds, 1)])->messages()->attach($messageIds[array_rand($messageIds, 1)], [
                        "cid"=>$chatIds[array_rand($chatIds, 1)],
                        "state"=>rand(0, 8),
                        "time"=>$faker->datetime,
                    ] );
                }
                catch (\Exception $e ) {}
        }

        for ($i = 0; $i < $participants ; $i++ ) {
            try {
                User::find($userIds[array_rand($userIds, 1)])->chats()->attach( $chatIds[array_rand($chatIds, 1)], [
                    "permissions" => rand(0, 8),
                    "time" => $faker->datetime,
                ] );
            }
            catch (\Exception $e ) {}
        }

    }
}
