<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RbacSeeder::class);

        $pusat = Wilayah::firstOrCreate([
            'nama_wilayah' => env('SUPERADMIN_WILAYAH', 'Pusat'),
        ]);

        User::updateOrCreate([
            'email' => env('SUPERADMIN_EMAIL', 'superadmin@gkkdjakarta.local'),
        ], [
            'name' => env('SUPERADMIN_NAME', 'Superadmin GKKD'),
            'password' => env('SUPERADMIN_PASSWORD', 'Superadmin#2026!'),
            'role_id' => Role::where('name', Role::SUPERADMIN)->value('id'),
            'wilayah_id' => $pusat->id,
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
