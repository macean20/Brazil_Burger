<?php
// src/Service/Menu/MenuService.php
namespace App\Service\Menu;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Entity\Complement;
use App\Repository\MenuRepository;
use App\Repository\BurgerRepository;
use App\Repository\ComplementRepository;

class MenuService
{
    private MenuRepository $menuRepository;
    private BurgerRepository $burgerRepository;
    private ComplementRepository $complementRepository;
    private MenuPriceCalculator $priceCalculator;

    public function __construct(
        MenuRepository $menuRepository,
        BurgerRepository $burgerRepository,
        ComplementRepository $complementRepository,
        MenuPriceCalculator $priceCalculator
    ) {
        $this->menuRepository = $menuRepository;
        $this->burgerRepository = $burgerRepository;
        $this->complementRepository = $complementRepository;
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * Créer un nouveau menu
     */
    public function createMenu(array $data): Menu
    {
        $burger = $this->burgerRepository->find($data['burger_id']);
        $boisson = $this->complementRepository->find($data['boisson_id']);
        $frite = $this->complementRepository->find($data['frite_id']);

        if (!$burger || !$boisson || !$frite) {
            throw new \InvalidArgumentException('Burger, boisson ou frite non trouvé');
        }

        $menu = new Menu();
        $menu->setNom($data['nom'] ?? '');
        $menu->setDescription($data['description'] ?? null);
        $menu->setBurger($burger);
        $menu->setBoisson($boisson);
        $menu->setFrite($frite);
        $menu->setDisponible($data['disponible'] ?? true);

        // Calculer le prix total avec réduction
        $prixTotal = $this->priceCalculator->calculateMenuPrice($burger, $boisson, $frite);
        $menu->setPrixTotal($prixTotal);

        $this->menuRepository->save($menu, true);

        return $menu;
    }

    /**
     * Mettre à jour un menu
     */
    public function updateMenu(Menu $menu, array $data): Menu
    {
        if (isset($data['burger_id'])) {
            $burger = $this->burgerRepository->find($data['burger_id']);
            if ($burger) {
                $menu->setBurger($burger);
            }
        }

        if (isset($data['boisson_id'])) {
            $boisson = $this->complementRepository->find($data['boisson_id']);
            if ($boisson) {
                $menu->setBoisson($boisson);
            }
        }

        if (isset($data['frite_id'])) {
            $frite = $this->complementRepository->find($data['frite_id']);
            if ($frite) {
                $menu->setFrite($frite);
            }
        }

        $menu->setNom($data['nom'] ?? $menu->getNom());
        $menu->setDescription($data['description'] ?? $menu->getDescription());
        
        if (isset($data['disponible'])) {
            $menu->setDisponible($data['disponible']);
        }

        // Recalculer le prix
        $prixTotal = $this->priceCalculator->calculateMenuPrice(
            $menu->getBurger(),
            $menu->getBoisson(),
            $menu->getFrite()
        );
        $menu->setPrixTotal($prixTotal);

        $this->menuRepository->save($menu, true);

        return $menu;
    }

    /**
     * Supprimer un menu
     */
    public function deleteMenu(Menu $menu): bool
    {
        $this->menuRepository->remove($menu, true);
        return true;
    }

    /**
     * Changer la disponibilité d'un menu
     */
    public function toggleDisponibilite(Menu $menu): Menu
    {
        $menu->setDisponible(!$menu->isDisponible());
        $this->menuRepository->save($menu, true);

        return $menu;
    }

    /**
     * Calculer le prix d'un menu en temps réel
     */
    public function calculateRealTimePrice(array $data): array
    {
        $burger = $this->burgerRepository->find($data['burger_id'] ?? null);
        $boisson = $this->complementRepository->find($data['boisson_id'] ?? null);
        $frite = $this->complementRepository->find($data['frite_id'] ?? null);

        if (!$burger || !$boisson || !$frite) {
            throw new \InvalidArgumentException('Produits non trouvés');
        }

        $prixTotal = $this->priceCalculator->calculateMenuPrice($burger, $boisson, $frite);

        return [
            'prix_total' => $prixTotal,
            'prix_total_formatted' => number_format($prixTotal, 0, ',', ' ') . ' FCFA',
            'details' => [
                'burger' => $burger->getPrix(),
                'boisson' => $boisson->getPrix(),
                'frite' => $frite->getPrix(),
                'reduction' => '10%'
            ]
        ];
    }

    /**
     * Obtenir les menus disponibles
     */
    public function getAvailableMenus(): array
    {
        return $this->menuRepository->findAvailable();
    }

    /**
     * Obtenir les menus par burger
     */
    public function getMenusByBurger(Burger $burger): array
    {
        return $this->menuRepository->findByBurger($burger->getId());
    }

    /**
     * Vérifier si les produits d'un menu sont disponibles
     */
    public function checkMenuAvailability(Menu $menu): array
    {
        $issues = [];

        if (!$menu->getBurger()->isDisponible()) {
            $issues[] = 'Burger indisponible';
        }

        if (!$menu->getBoisson()->isDisponible()) {
            $issues[] = 'Boisson indisponible';
        }

        if (!$menu->getFrite()->isDisponible()) {
            $issues[] = 'Frites indisponibles';
        }

        return [
            'available' => empty($issues),
            'issues' => $issues
        ];
    }
}