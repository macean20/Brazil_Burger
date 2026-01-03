<?php
// src/DataFixtures/GestionnaireFixtures.php
namespace App\DataFixtures;

use App\Entity\Gestionnaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GestionnaireFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $gestionnaires = [
            [
                'nom' => 'Admin',
                'prenom' => 'Brasil',
                'email' => 'admin@brasilburger.com',
                'password' => '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', // admin123
                'role' => 'ADMIN'
            ],
            [
                'nom' => 'Manager',
                'prenom' => 'Restaurant',
                'email' => 'manager@brasilburger.com',
                'password' => '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', // admin123
                'role' => 'GESTIONNAIRE'
            ],
            [
                'nom' => 'Superviseur',
                'prenom' => 'Livraison',
                'email' => 'livraison@brasilburger.com',
                'password' => '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', // admin123
                'role' => 'GESTIONNAIRE'
            ]
        ];

        foreach ($gestionnaires as $data) {
            $gestionnaire = new Gestionnaire();
            $gestionnaire->setNom($data['nom']);
            $gestionnaire->setPrenom($data['prenom']);
            $gestionnaire->setEmail($data['email']);
            $gestionnaire->setPassword($data['password']);
            $gestionnaire->setRole($data['role']);

            $manager->persist($gestionnaire);
        }

        $manager->flush();
    }
}