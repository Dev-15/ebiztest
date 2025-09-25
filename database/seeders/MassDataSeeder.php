<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use DB;

class MassDataSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(1000)->create();
        $users = User::pluck('id')->toArray();
        $faker = \Faker\Factory::create();
        $batchSize = 1000;
        $total = 100000;
        for ($i=0;$i<$total;$i+=$batchSize) {
            $values = [];
            for ($j=0;$j<$batchSize;$j++) {
                $u = $users[array_rand($users)];
                $amt = $faker->randomFloat(2,0,10000);
                $type = $faker->randomElement(['sale','refund','fee']);
                $created = $faker->dateTimeBetween('-2 years','now')->format('Y-m-d H:i:s');
                $values[] = "($u, $amt, '$type', '$created', '$created')";
            }
            DB::insert('INSERT INTO transactions (user_id, amount, type, created_at, updated_at) VALUES '.implode(',',$values));
        }
    }
}
