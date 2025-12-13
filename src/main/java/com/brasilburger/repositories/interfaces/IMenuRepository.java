package com.brasilburger.repositories.interfaces;

import com.brasilburger.models.Menu;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface IMenuRepository {
    Menu create(Menu menu);
    Optional<Menu> findById(UUID id);
    List<Menu> findAll();
    List<Menu> findAllWithDetails();
    List<Menu> findAvailable();
    Menu update(Menu menu);
    boolean delete(UUID id);
    boolean existsByNom(String nom);
}
