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
                'max_books_per_month' => 5,
                'price' => 49.90,
                'description' => 'Ideal para traduções de livros curtos. Até 200 páginas por livro e 5 livros a cada 30 dias.',
                'active' => true,
            ],
            [
                'name' => 'Plano Profissional',
                'slug' => 'profissional',
                'max_pages' => 500,
                'max_books_per_month' => 15,
                'price' => 99.90,
                'description' => 'Perfeito para livros médios. Até 500 páginas por livro e 15 livros a cada 30 dias.',
                'active' => true,
            ],
            [
                'name' => 'Plano Premium',
                'slug' => 'premium',
                'max_pages' => 1000,
                'max_books_per_month' => 30,
                'price' => 199.90,
                'description' => 'Para profissionais. Até 1.000 páginas por livro e 30 livros a cada 30 dias. Livros maiores podem ser traduzidos em partes.',
                'active' => true,
            ],
        ];

        $timestamp = now();

        DB::table('plans')->upsert(
            array_map(function ($plan) use ($timestamp) {
                return [
                    'name' => $plan['name'],
                    'slug' => $plan['slug'],
                    'max_pages' => $plan['max_pages'],
                    'max_books_per_month' => $plan['max_books_per_month'],
                    'price' => $plan['price'],
                    'description' => $plan['description'],
                    'active' => $plan['active'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }, $plans),
            ['slug'],
            ['name', 'max_pages', 'max_books_per_month', 'price', 'description', 'active', 'updated_at']
        );
    }
}
