<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Burger;
use App\Entity\Complement;
use App\Entity\Menu;
use App\Entity\Zone;
use App\Entity\Quartier;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Burgers
        $burgers = [];
        $burgerNames = [
            'Brasil Classic' => 'Burger classique avec viande hachée, fromage, laitue et tomate',
            'Brasil Spicy' => 'Burger épicé avec sauce piquante, piments et oignons croustillants',
            'Brasil Cheese' => 'Double fromage avec sauce spéciale',
            'Brasil Chicken' => 'Burger au poulet croustillant',
            'Brasil Veggie' => 'Burger végétarien avec galette de légumes'
        ];
        
        foreach ($burgerNames as $nom => $description) {
            $burger = new Burger();
            $burger->setNom($nom);
            $burger->setDescription($description);
            $burger->setPrix(rand(2000, 3500));
            $burger->setDisponible(true);
            $manager->persist($burger);
            $burgers[] = $burger;
        }
        
        // Compléments
        $boissons = [];
        $frites = [];
        
        $boissonNames = ['Coca-Cola', 'Fanta Orange', 'Sprite', 'Eau Minérale'];
        foreach ($boissonNames as $nom) {
            $complement = new Complement();
            $complement->setNom($nom);
            $complement->setType('BOISSON');
            $complement->setPrix(500);
            $complement->setDisponible(true);
            $manager->persist($complement);
            $boissons[] = $complement;
        }
        
        $friteNames = ['Frites Standard', 'Frites Large', 'Frites Maison'];
        foreach ($friteNames as $nom) {
            $complement = new Complement();
            $complement->setNom($nom);
            $complement->setType('FRITE');
            $complement->setPrix(rand(800, 1200));
            $complement->setDisponible(true);
            $manager->persist($complement);
            $frites[] = $complement;
        }
        
        // Menus
        $menuNames = ['Menu Classic', 'Menu Spicy', 'Menu Cheese', 'Menu Chicken'];
        foreach ($menuNames as $index => $nom) {
            if (isset($burgers[$index], $boissons[$index % count($boissons)], $frites[$index % count($frites)])) {
                $menu = new Menu();
                $menu->setNom($nom);
                $menu->setDescription("Menu complet avec {$burgers[$index]->getNom()}");
                $menu->setBurger($burgers[$index]);
                $menu->setBoisson($boissons[$index % count($boissons)]);
                $menu->setFrite($frites[$index % count($frites)]);
                
                // Calcul du prix avec réduction de 10%
                $prixTotal = (
                    (float)$burgers[$index]->getPrix() + 
                    (float)$boissons[$index % count($boissons)]->getPrix() + 
                    (float)$frites[$index % count($frites)]->getPrix()
                ) * 0.9;
                
                $menu->setPrixTotal(number_format($prixTotal, 2, '.', ''));
                $menu->setDisponible(true);
                $manager->persist($menu);
            }
        }
        
        // Zones de livraison
        $zones = [];
        $zoneData = [
            ['Dakar Plateau', 1000, 'Centre-ville historique'],
            ['Dakar Centre', 1500, 'Zones résidentielles centrales'],
            ['Dakar Nord', 2000, 'Zones nord de Dakar'],
            ['Dakar Banlieue', 2500, 'Pikine, Guédiawaye']
        ];
        
        foreach ($zoneData as $data) {
            $zone = new Zone();
            $zone->setNom($data[0]);
            $zone->setPrixLivraison($data[1]);
            $zone->setDescription($data[2]);
            $manager->persist($zone);
            $zones[] = $zone;
        }
        
        // Clients
        $clients = [];
        $clientNames = [
            ['Moussa', 'Diop', 'moussa.diop@example.com', '771234567'],
            ['Aminata', 'Ndiaye', 'aminata.ndiaye@example.com', '772345678'],
            ['Ibrahima', 'Fall', 'ibrahima.fall@example.com', '773456789'],
            ['Fatou', 'Sow', 'fatou.sow@example.com', '774567890']
        ];
        
        foreach ($clientNames as $data) {
            $client = new Client();
            $client->setNom($data[0]);
            $client->setPrenom($data[1]);
            $client->setEmail($data[2]);
            $client->setTelephone($data[3]);
            $client->setPassword(password_hash('client123', PASSWORD_BCRYPT));
            $manager->persist($client);
            $clients[] = $client;
        }
        
        $manager->flush();
        
        echo "✅ Données de test créées avec succès!\n";
        echo "🍔 Burgers: " . count($burgers) . "\n";
        echo "🥤 Boissons: " . count($boissons) . "\n";
        echo "🍟 Frites: " . count($frites) . "\n";
        echo "📦 Menus: " . count($menuNames) . "\n";
        echo "🗺️ Zones: " . count($zones) . "\n";
        echo "👥 Clients: " . count($clients) . "\n";
    }
}