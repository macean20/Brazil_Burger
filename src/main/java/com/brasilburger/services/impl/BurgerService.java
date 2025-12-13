package com.brasilburger.services.impl;

import com.brasilburger.models.Burger;
import com.brasilburger.repositories.interfaces.IBurgerRepository;
import com.brasilburger.services.interfaces.IBurgerService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public class BurgerService implements IBurgerService {
    private static final Logger logger = LoggerFactory.getLogger(BurgerService.class);
    private final IBurgerRepository burgerRepository;

    public BurgerService(IBurgerRepository burgerRepository) {
        this.burgerRepository = burgerRepository;
    }

    @Override
    public Burger createBurger(String nom, String description, BigDecimal prix, String imageUrl) {
        if (nom == null || nom.trim().isEmpty()) {
            throw new IllegalArgumentException("Le nom du burger est obligatoire");
        }
        if (prix == null || prix.compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("Le prix doit être supérieur à 0");
        }
        if (burgerRepository.existsByNom(nom)) {
            throw new IllegalArgumentException("Un burger avec ce nom existe déjà");
        }

        Burger burger = new Burger(nom, description, prix);
        burger.setImageUrl(imageUrl);

        return burgerRepository.create(burger);
    }

    @Override
    public Burger getBurgerById(UUID id) {
        return burgerRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Burger non trouvé avec l'ID: " + id));
    }

    @Override
    public List<Burger> getAllBurgers() {
        return burgerRepository.findAll();
    }

    @Override
    public List<Burger> getAvailableBurgers() {
        return burgerRepository.findAvailable();
    }

    @Override
    public Burger updateBurger(UUID id, String nom, String description, BigDecimal prix, String imageUrl, boolean disponible) {
        Burger existingBurger = getBurgerById(id);

        if (nom == null || nom.trim().isEmpty()) {
            throw new IllegalArgumentException("Le nom du burger est obligatoire");
        }
        if (prix == null || prix.compareTo(BigDecimal.ZERO) <= 0) {
            throw new IllegalArgumentException("Le prix doit être supérieur à 0");
        }

        existingBurger.setNom(nom);
        existingBurger.setDescription(description);
        existingBurger.setPrix(prix);
        existingBurger.setImageUrl(imageUrl);
        existingBurger.setDisponible(disponible);

        return burgerRepository.update(existingBurger);
    }

    @Override
    public void deleteBurger(UUID id) {
        if (!burgerRepository.delete(id)) {
            throw new RuntimeException("Impossible de supprimer le burger avec l'ID: " + id);
        }
    }

    @Override
    public void toggleAvailability(UUID id) {
        Burger burger = getBurgerById(id);
        burger.setDisponible(!burger.isDisponible());
        burgerRepository.update(burger);
        logger.info("Disponibilité du burger {} changée à: {}", burger.getNom(), burger.isDisponible());
    }
}
