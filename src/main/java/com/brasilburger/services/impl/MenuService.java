package com.brasilburger.services.impl;

import com.brasilburger.models.Menu;
import com.brasilburger.repositories.interfaces.IMenuRepository;
import com.brasilburger.services.interfaces.IMenuService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public class MenuService implements IMenuService {
    private static final Logger logger = LoggerFactory.getLogger(MenuService.class);
    private final IMenuRepository menuRepository;

    public MenuService(IMenuRepository menuRepository) {
        this.menuRepository = menuRepository;
    }

    @Override
    public Menu createMenu(String nom, String description, UUID burgerId, UUID boissonId, UUID friteId, BigDecimal prixTotal) {
        if (nom == null || nom.trim().isEmpty()) {
            throw new IllegalArgumentException("Le nom du menu est obligatoire");
        }
        if (burgerId == null) {
            throw new IllegalArgumentException("Le burger est obligatoire");
        }
        if (boissonId == null) {
            throw new IllegalArgumentException("La boisson est obligatoire");
        }
        if (friteId == null) {
            throw new IllegalArgumentException("Les frites sont obligatoires");
        }
        if (prixTotal == null || prixTotal.compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("Le prix total doit être supérieur à 0");
        }
        if (menuRepository.existsByNom(nom)) {
            throw new IllegalArgumentException("Un menu avec ce nom existe déjà");
        }

        Menu menu = new Menu(nom, description, burgerId, boissonId, friteId, prixTotal);
        return menuRepository.create(menu);
    }

    @Override
    public Menu getMenuById(UUID id) {
        return menuRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Menu non trouvé avec l'ID: " + id));
    }

    @Override
    public List<Menu> getAllMenus() {
        return menuRepository.findAll();
    }

    @Override
    public List<Menu> getAllMenusWithDetails() {
        return menuRepository.findAllWithDetails();
    }

    @Override
    public List<Menu> getAvailableMenus() {
        return menuRepository.findAvailable();
    }

    @Override
    public Menu updateMenu(UUID id, String nom, String description, UUID burgerId, UUID boissonId, UUID friteId, BigDecimal prixTotal, boolean disponible) {
        Menu existingMenu = getMenuById(id);

        if (nom == null || nom.trim().isEmpty()) {
            throw new IllegalArgumentException("Le nom du menu est obligatoire");
        }
        if (burgerId == null) {
            throw new IllegalArgumentException("Le burger est obligatoire");
        }
        if (boissonId == null) {
            throw new IllegalArgumentException("La boisson est obligatoire");
        }
        if (friteId == null) {
            throw new IllegalArgumentException("Les frites sont obligatoires");
        }
        if (prixTotal == null || prixTotal.compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("Le prix total doit être supérieur à 0");
        }

        existingMenu.setNom(nom);
        existingMenu.setDescription(description);
        existingMenu.setBurgerId(burgerId);
        existingMenu.setBoissonId(boissonId);
        existingMenu.setFriteId(friteId);
        existingMenu.setPrixTotal(prixTotal);
        existingMenu.setDisponible(disponible);

        return menuRepository.update(existingMenu);
    }

    @Override
    public void deleteMenu(UUID id) {
        if (!menuRepository.delete(id)) {
            throw new RuntimeException("Impossible de supprimer le menu avec l'ID: " + id);
        }
    }

    @Override
    public void toggleAvailability(UUID id) {
        Menu menu = getMenuById(id);
        menu.setDisponible(!menu.isDisponible());
        menuRepository.update(menu);
        logger.info("Disponibilité du menu {} changée à: {}", menu.getNom(), menu.isDisponible());
    }
}
