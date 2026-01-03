<?php
// src/DataFixtures/MenuFixtures.php
namespace App\DataFixtures;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Entity\Complement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MenuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer les burgers depuis les fixtures
        $burgerClassic  = $this->getReference('burger_classic', Burger::class);
        $burgerSpicy    = $this->getReference('burger_spicy', Burger::class);
        $burgerCheese   = $this->getReference('burger_cheese', Burger::class);
        $burgerChicken = $this->getReference('burger_chicken' , Burger::class);
        $burgerVeggie = $this->getReference('burger_veggie', Burger::class);

        // Récupérer les compléments depuis AppFixtures
        $coca = $this->getReference('coca_cola' , Complement::class);
        $fanta = $this->getReference('fanta_orange' , Complement::class);
        $sprite = $this->getReference('sprite', Complement::class);
        $fritesStandard = $this->getReference('frites_standard', Complement::class);
        $fritesLarge = $this->getReference('frites_large', Complement::class);

        $menus = [
            [
                'nom' => 'Menu Brasil Classic',
                'description' => 'Le combo parfait: Brasil Classic + Boisson + Frites',
                'burger' => $burgerClassic,
                'boisson' => $coca,
                'frite' => $fritesStandard,
                'prixTotal' => 3420, // (2500 + 500 + 800) * 0.9
                'disponible' => true
            ],
            [
                'nom' => 'Menu Brasil Spicy',
                'description' => 'Pour les audacieux: Brasil Spicy + Boisson + Frites',
                'burger' => $burgerSpicy,
                'boisson' => $fanta,
                'frite' => $fritesLarge,
                'prixTotal' => 4050, // (2800 + 500 + 1200) * 0.9
                'disponible' => true
            ],
            [
                'nom' => 'Menu Brasil Cheese',
                'description' => 'L\'ultime fromage: Brasil Cheese + Boisson + Frites',
                'burger' => $burgerCheese,
                'boisson' => $sprite,
                'frite' => $fritesLarge,
                'prixTotal' => 4320, // (3200 + 500 + 1200) * 0.9
                'disponible' => true
            ],
            [
                'nom' => 'Menu Brasil Chicken',
                'description' => 'Le poulet croustillant: Brasil Chicken + Boisson + Frites',
                'burger' => $burgerChicken,
                'boisson' => $coca,
                'frite' => $fritesStandard,
                'prixTotal' => 3600, // (2700 + 500 + 800) * 0.9
                'disponible' => true
            ],
            [
                'nom' => 'Menu Brasil Veggie',
                'description' => 'L\'option healthy: Brasil Veggie + Boisson + Frites',
                'burger' => $burgerVeggie,
                'boisson' => $fanta,
                'frite' => $fritesStandard,
                'prixTotal' => 3330, // (2400 + 500 + 800) * 0.9
                'disponible' => true
            ]
        ];

        foreach ($menus as $data) {
            $menu = new Menu();
            $menu->setNom($data['nom']);
            $menu->setDescription($data['description']);
            $menu->setBurger($data['burger']);
            $menu->setBoisson($data['boisson']);
            $menu->setFrite($data['frite']);
            $menu->setPrixTotal($data['prixTotal']);
            $menu->setDisponible($data['disponible']);

            $manager->persist($menu);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BurgerFixtures::class,
            AppFixtures::class
        ];
    }

    
}