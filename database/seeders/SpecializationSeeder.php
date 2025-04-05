<?php

namespace Database\Seeders;

use App\Models\specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run()
    {
        $specializations = [
            [
                'name' => 'front-end',
                'description' => 'Specializing in building user interfaces using HTML, CSS, JavaScript and frameworks like React, Vue or Angular.'
            ],
            [
                'name' => 'back-end',
                'description' => 'Focusing on server-side logic, databases, and application integration using languages like PHP, Python, Java, or Node.js.'
            ],
            [
                'name' => 'database developer',
                'description' => 'Expertise in database design, optimization, and management with systems like MySQL, PostgreSQL, MongoDB, or Oracle.'
            ],
            [
                'name' => 'Artificial_Intelligence',
                'description' => 'Specializing in machine learning, deep learning, natural language processing and AI model development.'
            ],
            [
                'name' => 'Full-stack',
                'description' => 'Combining both front-end and back-end skills to build complete web applications.'
            ],
            [
                'name' => 'Mobile_Development',
                'description' => 'Building native or cross-platform mobile applications for iOS and Android.'
            ],
            [
                'name' => 'DevOps_Engineering',
                'description' => 'Focusing on deployment, automation, and infrastructure management using tools like Docker, Kubernetes, and CI/CD pipelines.'
            ],
            [
                'name' => 'UI/UX_Design',
                'description' => 'Specializing in user interface design and user experience research and implementation.'
            ]
        ];

        foreach ($specializations as $specialization) {
            Specialization::create($specialization);
        }
    }
}
