<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = collect(['general', 'laravel', 'reverb', 'echo', 'livewire']);

        $channels->each(function ($channel) {
            Channel::firstOrCreate(['name' => $channel])
                ->subscribers()
                ->attach(
                    User::inRandomOrder()
                        ->take(rand(1, 10))->get()
                );
        });
    }
}
