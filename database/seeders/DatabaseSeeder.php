<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if(User::count()==0) {
            User::factory(1)->create([
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]);
        }

        if(Categories::count()==0) {
            Categories::factory(20)->create();
        }

        if (Products::count() == 0) {
            for ($y = 0; $y < 10; $y++) {
                Products::factory(1)->create();
                $numberOfIterations = rand(1, 5);
                for ($i = 0; $i < $numberOfIterations; $i++) {
                    ProductImage::factory(1)->create();
                }
            }
        }
    }
}
