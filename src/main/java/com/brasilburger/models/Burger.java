package com.brasilburger.models;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

public class Burger {
    private UUID id;
    private String nom;
    private String description;
    private BigDecimal prix;
    private String imageUrl;
    private boolean disponible;
    private LocalDateTime createdAt;

    public Burger() {
        this.id = UUID.randomUUID();
        this.disponible = true;
        this.createdAt = LocalDateTime.now();
    }

    public Burger(String nom, String description, BigDecimal prix) {
        this();
        this.nom = nom;
        this.description = description;
        this.prix = prix;
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

    public BigDecimal getPrix() {
        return prix;
    }

    public void setPrix(BigDecimal prix) {
        this.prix = prix;
    }

    public String getImageUrl() {
        return imageUrl;
    }

    public void setImageUrl(String imageUrl) {
        this.imageUrl = imageUrl;
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

    @Override
    public String toString() {
        return String.format("Burger{id=%s, nom='%s', prix=%.2f, disponible=%s}",
                id, nom, prix, disponible);
    }
}
