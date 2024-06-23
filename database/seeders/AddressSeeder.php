<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Home;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả các id từ bảng Home
        $homeIds = Home::pluck('id')->toArray();

        // Shuffle các id để đảm bảo tính ngẫu nhiên
        shuffle($homeIds);

        // Duyệt qua từng id và tạo bản ghi Address
        foreach ($homeIds as $homeId) {
            Address::factory(10)->create(['id' => $homeId]);
        }
    }
}
