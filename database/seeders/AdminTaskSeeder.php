<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Task::create([
        //     'name' => 'VLS-TS',
        //     'description' => 'Validating your Visa',
        //     'guideUrl' => 'https://youtu.be/j_suMJ3fIiw',
        //     'registrationUrl' => 'https://administration-etrangers-en-france.interieur.gouv.fr/particuliers/#/',
        //     'pieces' => json_encode(['Deadlines: 3 months', 'Tax fee: 60 euros']),
        //     'completed' => false,  // Si la tâche est à faire
        // ]);

        // Task::create([
        //     'name' => 'Bank',
        //     'description' => 'Opening an account',
        //     'guideUrl' => '',
        //     'registrationUrl' => '',
        //     'pieces' => json_encode(["Pièce d'identité", 'Justificatif de domicile']),
        //     'completed' => false,
        // ]);

        // Task::create([
        //     'name' => 'Social Security',
        //     'description' => 'Enrollement for social security',
        //     'guideUrl' => 'https://youtu.be/iAZDcb0cqJI',
        //     'registrationUrl' => 'https://www.ameli.fr',
        //     'pieces' => json_encode([
        //         'Enrollment Certificate (not your Acceptance Letter)',
        //         'Passport & Visa',
        //         'Bank details',
        //         'Birth certificate'
        //     ]),
        //     'completed' => false,
        // ]);
        Task::create([
            'name' => 'CAF',
            'description' => 'Applying to APL (CAF)',
            'guideUrl' => 'https://www.utbm.fr/wp-content/uploads/2024/04/How-to-apply-to-the-CAF.pdf',
            'registrationUrl' => 'https://www.caf.fr/allocataires/aides-et-demarches/droits-et-prestations/logement/les-aides-personnelles-au-logement',
            'pieces' => json_encode([
                'ID / Passport & Visa',
                'Enrollment Certificate (not your Acceptance Letter)',
                'European : European Health Insurance Card',
                'Bank details',
                'Birth certificate',
                'CROUS: Accommodation Certificate (CROUS’, not UTBM’s)/NEOLIA: CAF Certificate (please ask by email)',
            ]),
            'completed' => false,
        ]);
        Task::create([
            'name' => 'Optymo',
            'description' => 'Get your bus card',
            'guideUrl' =>  'Go to Optymo agency at Pôle Liberté, 13 rue de Madrid, 90000 BELFORT ',
            'registrationUrl' => '',
            'pieces' => json_encode([
                'ID/Passport & Visa',
                'Student ID card',
                'Accomodation certificate',
                'Bank details',
            ]),
            'completed' => false,
        ]);
    }
}
