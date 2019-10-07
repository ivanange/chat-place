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

    public function participant(int $cid) {
        return DB::table("participants")->where("cid","=", $cid)->pluck("uid")->random();
    }

    public function notParticipant(int $cid) {
        return DB::table("participants")->where("cid","<>", $cid)->pluck("uid")->random();
    }

    public function chat(int $uid) {
        return DB::table("participants")->where("uid","=", $uid)->pluck("cid")->random();
    }

    public function notChat(int $uid) {
        return DB::table("participants")->where("uid","<>", $uid)->pluck("cid")->random();
    }
    
    public function run(Faker $faker)
    {
        $users = 100;
        $chats = 250;
        $messages = 1000;
        $messagings = 2500;
        $participants = 1400;
        $outsidersMessages = 200;

        $user = factory(App\User::class)->make();
        $user->email = "me@example.com";
        $user->state = user::AUTHENTICATED;
        $user->save();
        factory(App\User::class, $users)->create();
        factory(App\chat::class, $chats)->create();
        factory(App\message::class, $messages)->create();

        $userIds = DB::table('users')->pluck('id')->toArray();
        $chatIds = DB::table('chats')->pluck('id')->toArray();
        $messageIds = DB::table('messages')->pluck('id')->toArray();



        for ($i = 0; $i < $participants ; $i++ ) {
            try {
                User::find($userIds[array_rand($userIds, 1)])->chats()->attach( $chatIds[array_rand($chatIds, 1)], [
                    "permissions" => rand(0, 959),
                    "time" => $faker->datetime,
                ] );
            }
            catch (\Exception $e ) {}
        }

        for ($i = 0; $i < $messagings ; $i++ ) {
            // seul les participant ai des messages 
            try {
                $uid = $userIds[array_rand($userIds, 1)];
                User::find($uid)->messages()->attach($messageIds[array_rand($messageIds, 1)], [
                    "cid"=> $this->chat($uid),
                    "state"=>rand(0, 15),
                    "time"=>$faker->datetime,
                ] );
            }
            catch (\Exception $e ) {}
        }

        for ($i = 0; $i < $outsidersMessages ; $i++ ) {
            // messages of participants that have left 
            try {
                $uid = $userIds[array_rand($userIds, 1)];
                User::find($uid)->messages()->attach($messageIds[array_rand($messageIds, 1)], [
                    "cid"=> $this->notChat($uid),
                    "state"=>rand(0, 15),
                    "time"=>$faker->datetime,
                ] );
            }
            catch (\Exception $e ) {}
        }

    }
}
