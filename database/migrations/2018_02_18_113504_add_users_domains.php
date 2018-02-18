<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersDomains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!env('MAIL_RECIPIENT_NAME') || !env('MAIL_RECIPIENT_EMAIL')) {
            return;
        }

        DB::transaction(function () {
            DB::table('users')->insert([
                'id' => 1,
                'name' => env('MAIL_RECIPIENT_NAME'),
                'email' => env('MAIL_RECIPIENT_EMAIL'),
                'password' => bcrypt('secret'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            $domains = array_pluck(DB::select('SELECT distinct substring_index(email, "@", -1) as domain FROM addresses'), 'domain');

            foreach ($domains as $domain) {
                DB::table('domains')->insert([
                    'domain' => $domain,
                    'user_id' => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);

                $id = DB::table('domains')->where('domain', $domain)->where('user_id', 1)->first()->id;

                DB::table('addresses')->where('email', 'like', '%@'.$domain)->update([
                    'domain_id' => $id,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('addresses')->update(['domain_id' => null]);
        DB::table('domains')->delete();
        DB::table('users')->delete();
    }
}
