package com.brasilburger.services.interfaces;

import com.brasilburger.models.Burger;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public interface IBurgerService {
    Burger createBurger(String nom, String description, BigDecimal prix, String imageUrl);
    Burger getBurgerById(UUID id);
    List<Burger> getAllBurgers();
    List<Burger> getAvailableBurgers();
    Burger updateBurger(UUID id, String nom, String description, BigDecimal prix, String imageUrl, boolean disponible);
    void deleteBurger(UUID id);
    void toggleAvailability(UUID id);
}
