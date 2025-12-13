package com.brasilburger.services.interfaces;

import com.brasilburger.models.Menu;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public interface IMenuService {
    Menu createMenu(String nom, String description, UUID burgerId, UUID boissonId, UUID friteId, BigDecimal prixTotal);
    Menu getMenuById(UUID id);
    List<Menu> getAllMenus();
    List<Menu> getAllMenusWithDetails();
    List<Menu> getAvailableMenus();
    Menu updateMenu(UUID id, String nom, String description, UUID burgerId, UUID boissonId, UUID friteId, BigDecimal prixTotal, boolean disponible);
    void deleteMenu(UUID id);
    void toggleAvailability(UUID id);
}
