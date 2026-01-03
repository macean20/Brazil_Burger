<?php
// src/DataFixtures/BurgerFixtures.php
namespace App\DataFixtures;

use App\Entity\Burger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BurgerFixtures extends Fixture
{
    public const BURGER_REFERENCES = [
        'burger_classic',
        'burger_spicy',
        'burger_cheese',
        'burger_chicken',
        'burger_veggie'
    ];

    public function load(ObjectManager $manager): void
    {
        $burgers = [
            [
                'nom' => 'Brasil Classic',
                'description' => 'Notre burger signature: bœuf haché 100% pur, cheddar fondu, laitue croquante, tomates fraîches et sauce spéciale dans un pain brioché toasté.',
                'prix' => 2500,
                'image' => 'classic.jpg',
                'disponible' => true
            ],
            [
                'nom' => 'Brasil Spicy',
                'description' => 'Pour les amateurs de piquant: bœuf épicé, pepper jack, piments jalapeños, oignons croustillants et sauce piquante maison.',
                'prix' => 2800,
                'image' => 'spicy.jpg',
                'disponible' => true
            ],
            [
                'nom' => 'Brasil Cheese',
                'description' => 'L\'indémodable: double steak, double cheddar, bacon croustillant et sauce fromagère dans un pain aux graines de sésame.',
                'prix' => 3200,
                'image' => 'cheese.jpg',
                'disponible' => true
            ],
            [
                'nom' => 'Brasil Chicken',
                'description' => 'Filet de poulet croustillant, sauce ranch, laitue iceberg et tomates dans un pain aux céréales.',
                'prix' => 2700,
                'image' => 'chicken.jpg',
                'disponible' => true
            ],
            [
                'nom' => 'Brasil Veggie',
                'description' => 'Notre option végétarienne: galette de légumes grillés, avocat, roquette, tomates séchées et sauce tahini.',
                'prix' => 2400,
                'image' => 'veggie.jpg',
                'disponible' => true
            ]
        ];

        foreach ($burgers as $index => $data) {
            $burger = new Burger();
            $burger->setNom($data['nom']);
            $burger->setDescription($data['description']);
            $burger->setPrix($data['prix']);
            $burger->setDisponible($data['disponible']);
            
            // Pour les tests, on peut utiliser une image placeholder
            $burger->setImageUrl('/images/burgers/' . $data['image']);

            $manager->persist($burger);
            
            // Stocker la référence pour les menus
            if ($index < count(self::BURGER_REFERENCES)) {
                $this->addReference(self::BURGER_REFERENCES[$index], $burger);
            }
        }

        $manager->flush();
    }
}