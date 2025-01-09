<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TermsOfUse;

class TermsOfUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsOfUse::create([
            'title' => 'Política de Privacidade',
            'content' => 'Texto completo em português sobre política de privacidade...',
            'language' => 'pt_BR',
        ]);

        TermsOfUse::create([
            'title' => 'Privacy Policy',
            'content' => 'Full English text about privacy policy...',
            'language' => 'en',
        ]);

        TermsOfUse::create([
            'title' => 'Política de Privacidad',
            'content' => 'Texto completo en español sobre política de privacidad...',
            'language' => 'es',
        ]);
    }
}
