package com.brasilburger.models;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.UUID;

public class Complement {
    private UUID id;
    private String nom;
    private TypeComplement type;
    private BigDecimal prix;
    private boolean disponible;
    private LocalDateTime createdAt;

    public Complement() {
        this.id = UUID.randomUUID();
        this.disponible = true;
        this.createdAt = LocalDateTime.now();
    }

    public Complement(String nom, TypeComplement type, BigDecimal prix) {
        this();
        this.nom = nom;
        this.type = type;
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

    public TypeComplement getType() {
        return type;
    }

    public void setType(TypeComplement type) {
        this.type = type;
    }

    public BigDecimal getPrix() {
        return prix;
    }

    public void setPrix(BigDecimal prix) {
        this.prix = prix;
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
        return String.format("Complement{id=%s, nom='%s', type=%s, prix=%.2f, disponible=%s}",
                id, nom, type, prix, disponible);
    }
}
