<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tasks')->insert([
            [
                'name' => 'VLS-TS',
                'description' => 'Validating your Visa',
                'guide_url' => 'https://youtu.be/j_suMJ3fIiw',
                'registration_url' => 'https://administration-etrangers-en-france.interieur.gouv.fr/particuliers/#/',
                'pieces' => json_encode(['Deadlines: 3 months', 'Tax fee: 60 euros']),
                'completed' => false,
            ],
            [
                'name' => 'Bank',
                'description' => 'Opening an account',
                'guide_url' => null,
                'registration_url' => null,
                'pieces' => json_encode(["Pièce d'identité", 'Justificatif de domicile', '']),
                'completed' => false,
            ],
            [
                'name' => 'Social Security',
                'description' => 'Enrollement for social security',
                'guide_url' => 'https://youtu.be/iAZDcb0cqJI',
                'registration_url' => 'https://www.ameli.fr',
                'pieces' => json_encode([
                    'Enrollment Certificate (not your Acceptance Letter)',
                    'Passport & Visa',
                    'Bank details',
                    'Birth certificate',
                ]),
                'completed' => false,
            ],
            [
                'name' => 'CAF',
                'description' => 'Applying to APL (CAF)',
                'guide_url' => 'https://www.utbm.fr/wp-content/uploads/2024/04/How-to-apply-to-the-CAF.pdf',
                'registration_url' => 'https://www.caf.fr/allocataires/aides-et-demarches/droits-et-prestations/logement/les-aides-personnelles-au-logement',
                'pieces' => json_encode([
                    'ID / Passport & Visa',
                    'Enrollment Certificate (not your Acceptance Letter)',
                    'European : European Health Insurance Card',
                    'Bank details',
                    'Birth certificate',
                    'CROUS: Accommodation Certificate (CROUS’, not UTBM’s)/NEOLIA: CAF Certificate (please ask by email)',
                ]),
                'completed' => false,
            ],
            [
                'name' => 'Optymo',
                'description' => 'Get your bus card',
                'guide_url' =>  'Go to Optymo agency at Pôle Liberté, 13 rue de Madrid, 90000 BELFORT ',
                'registration_url' => null,
                'pieces' => json_encode([
                    'ID/Passport & Visa',
                    'Student ID card',
                    'Accomodation certificate',
                    'Bank details',
                ]),
                'completed' => false,
            ],
        ]);
    }
}
