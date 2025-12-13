package com.brasilburger.repositories.interfaces;

import com.brasilburger.models.Burger;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface IBurgerRepository {
    Burger create(Burger burger);
    Optional<Burger> findById(UUID id);
    List<Burger> findAll();
    List<Burger> findAvailable();
    Burger update(Burger burger);
    boolean delete(UUID id);
    boolean existsByNom(String nom);
}
