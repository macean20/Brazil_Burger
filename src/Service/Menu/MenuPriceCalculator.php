<?php
// src/Service/Menu/MenuPriceCalculator.php
namespace App\Service\Menu;

use App\Entity\Burger;
use App\Entity\Complement;

class MenuPriceCalculator
{
    private float $discountPercentage = 10.0; // 10% de réduction

    /**
     * Calculer le prix d'un menu avec réduction
     */
    public function calculateMenuPrice(Burger $burger, Complement $boisson, Complement $frite): string
    {
        $burgerPrice = (float) $burger->getPrix();
        $boissonPrice = (float) $boisson->getPrix();
        $fritePrice = (float) $frite->getPrix();

        $totalPrice = $burgerPrice + $boissonPrice + $fritePrice;
        $discountedPrice = $totalPrice * (1 - ($this->discountPercentage / 100));

        return number_format($discountedPrice, 2, '.', '');
    }

    /**
     * Calculer le montant de la réduction
     */
    public function calculateDiscountAmount(Burger $burger, Complement $boisson, Complement $frite): string
    {
        $burgerPrice = (float) $burger->getPrix();
        $boissonPrice = (float) $boisson->getPrix();
        $fritePrice = (float) $frite->getPrix();

        $totalPrice = $burgerPrice + $boissonPrice + $fritePrice;
        $discountAmount = $totalPrice * ($this->discountPercentage / 100);

        return number_format($discountAmount, 2, '.', '');
    }

    /**
     * Obtenir le prix original (sans réduction)
     */
    public function getOriginalPrice(Burger $burger, Complement $boisson, Complement $frite): string
    {
        $burgerPrice = (float) $burger->getPrix();
        $boissonPrice = (float) $boisson->getPrix();
        $fritePrice = (float) $frite->getPrix();

        $totalPrice = $burgerPrice + $boissonPrice + $fritePrice;

        return number_format($totalPrice, 2, '.', '');
    }

    /**
     * Formater le prix pour l'affichage
     */
    public function formatPriceForDisplay(string $price): string
    {
        $priceFloat = (float) $price;
        return number_format($priceFloat, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Calculer le prix unitaire d'un produit dans le menu
     */
    public function calculateUnitPriceInMenu(string $productPrice): string
    {
        $price = (float) $productPrice;
        // Appliquer la réduction proportionnelle
        $discountedPrice = $price * (1 - ($this->discountPercentage / 100));
        
        return number_format($discountedPrice, 2, '.', '');
    }

    /**
     * Modifier le pourcentage de réduction
     */
    public function setDiscountPercentage(float $percentage): void
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new \InvalidArgumentException('Le pourcentage doit être entre 0 et 100');
        }
        
        $this->discountPercentage = $percentage;
    }

    /**
     * Obtenir le pourcentage de réduction actuel
     */
    public function getDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }
}