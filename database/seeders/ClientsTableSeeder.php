<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $clients = ['client one', 'client two'];

        foreach ($clients as $client) {

            Client::create([

                'name' => $client,
                'phone' => '01061894125',
                'address' => 'Ain Shams'

            ]);

        }

        
    }
}
