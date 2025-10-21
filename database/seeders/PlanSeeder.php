<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plano Básico',
                'slug' => 'basico',
                'max_pages' => 200,
                'price' => 49.90,
                'description' => 'Ideal para traduções de livros curtos, até 200 páginas por livro.',
                'active' => true,
            ],
            [
                'name' => 'Plano Profissional',
                'slug' => 'profissional',
                'max_pages' => 500,
                'price' => 99.90,
                'description' => 'Perfeito para livros médios, até 500 páginas por livro.',
                'active' => true,
            ],
            [
                'name' => 'Plano Premium',
                'slug' => 'premium',
                'max_pages' => 0, // 0 = ilimitado
                'price' => 199.90,
                'description' => 'Sem limites! Traduza livros de qualquer tamanho, ideal para obras extensas e uso profissional.',
                'active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->insert([
                'name' => $plan['name'],
                'slug' => $plan['slug'],
                'max_pages' => $plan['max_pages'],
                'price' => $plan['price'],
                'description' => $plan['description'],
                'active' => $plan['active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
