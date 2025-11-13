<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create one
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Create sample sections
        $sections = [
            [
                'name' => 'Marketing bo\'limi',
                'description' => 'Marketing va reklama bo\'yicha hujjatlar',
                'status' => 'active',
                'user_id' => $user->id,
            ],
            [
                'name' => 'IT bo\'limi',
                'description' => 'Texnologiya va dasturlash hujjatlari',
                'status' => 'active',
                'user_id' => $user->id,
            ],
            [
                'name' => 'HR bo\'limi',
                'description' => 'Kadrlar bo\'yicha hujjatlar',
                'status' => 'active',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Moliya bo\'limi',
                'description' => 'Moliyaviy hisobotlar va hujjatlar',
                'status' => 'active',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Arxiv',
                'description' => 'Eski va arxivlangan hujjatlar',
                'status' => 'inactive',
                'user_id' => $user->id,
            ],
        ];

        foreach ($sections as $section) {
            Section::create($section);
        }
    }
}
