package com.brasilburger.models;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

public class Menu {
    private UUID id;
    private String nom;
    private String description;
    private UUID burgerId;
    private UUID boissonId;
    private UUID friteId;
    private BigDecimal prixTotal;
    private boolean disponible;
    private LocalDateTime createdAt;

    private Burger burger;
    private Complement boisson;
    private Complement frite;

    public Menu() {
        this.id = UUID.randomUUID();
        this.disponible = true;
        this.createdAt = LocalDateTime.now();
    }

    public Menu(String nom, String description, UUID burgerId, UUID boissonId, UUID friteId, BigDecimal prixTotal) {
        this();
        this.nom = nom;
        this.description = description;
        this.burgerId = burgerId;
        this.boissonId = boissonId;
        this.friteId = friteId;
        this.prixTotal = prixTotal;
    }

    public UUID getId() {
        return id;
    }

    public void setId(UUID id) {
        this.id = id;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public UUID getBurgerId() {
        return burgerId;
    }

    public void setBurgerId(UUID burgerId) {
        this.burgerId = burgerId;
    }

    public UUID getBoissonId() {
        return boissonId;
    }

    public void setBoissonId(UUID boissonId) {
        this.boissonId = boissonId;
    }

    public UUID getFriteId() {
        return friteId;
    }

    public void setFriteId(UUID friteId) {
        this.friteId = friteId;
    }

    public BigDecimal getPrixTotal() {
        return prixTotal;
    }

    public void setPrixTotal(BigDecimal prixTotal) {
        this.prixTotal = prixTotal;
    }

    public boolean isDisponible() {
        return disponible;
    }

    public void setDisponible(boolean disponible) {
        this.disponible = disponible;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(LocalDateTime createdAt) {
        this.createdAt = createdAt;
    }

    public Burger getBurger() {
        return burger;
    }

    public void setBurger(Burger burger) {
        this.burger = burger;
    }

    public Complement getBoisson() {
        return boisson;
    }

    public void setBoisson(Complement boisson) {
        this.boisson = boisson;
    }

    public Complement getFrite() {
        return frite;
    }

    public void setFrite(Complement frite) {
        this.frite = frite;
    }

    @Override
    public String toString() {
        return String.format("Menu{id=%s, nom='%s', prixTotal=%.2f, disponible=%s}",
                id, nom, prixTotal, disponible);
    }
}
