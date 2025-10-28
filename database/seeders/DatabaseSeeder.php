<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criação de 20 usuários aleatórios
        \App\Models\User::factory(20)->create();

        // Criação de um usuário de teste
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seeders adicionais
        $this->call([
            TermsOfUseSeeder::class,
            PlanSeeder::class,
        ]);
    }
}
