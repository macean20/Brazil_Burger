package com.brasilburger.services.impl;

import com.brasilburger.models.Complement;
import com.brasilburger.models.TypeComplement;
import com.brasilburger.repositories.interfaces.IComplementRepository;
import com.brasilburger.services.interfaces.IComplementService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public class ComplementService implements IComplementService {
    private static final Logger logger = LoggerFactory.getLogger(ComplementService.class);
    private final IComplementRepository complementRepository;

    public ComplementService(IComplementRepository complementRepository) {
        this.complementRepository = complementRepository;
    }

    @Override
    public Complement createComplement(String nom, TypeComplement type, BigDecimal prix) {
        if (nom == null || nom.trim().isEmpty()) {
            throw new IllegalArgumentException("Le nom du complément est obligatoire");
        }
        if (type == null) {
            throw new IllegalArgumentException("Le type du complément est obligatoire");
        }
        if (prix == null || prix.compareTo(BigDecimal.ZERO) < 0) {
            throw new IllegalArgumentException("Le prix doit être supérieur ou égal à 0");
        }

        Complement complement = new Complement(nom, type, prix);
        return complementRepository.create(complement);
    }

    @Override
    public Complement getComplementById(UUID id) {
        return complementRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Complément non trouvé avec l'ID: " + id));
    }

    @Override
    public List<Complement> getAllComplements() {
        return complementRepository.findAll();
    }

    @Override
    public List<Complement> getComplementsByType(TypeComplement type) {
        return complementRepository.findByType(type);
    }

    @Override
    public List<Complement> getAvailableComplements() {
        return complementRepository.findAvailable();
    }

    @Override
    public Complement updateComplement(UUID id, String nom, TypeComplement type, BigDecimal prix, boolean disponible) {
        Complement existingComplement = getComplementById(id);

        if (nom == null || nom.trim().isEmpty()) {
            throw new IllegalArgumentException("Le nom du complément est obligatoire");
        }
        if (type == null) {
            throw new IllegalArgumentException("Le type du complément est obligatoire");
        }
        if (prix == null || prix.compareTo(BigDecimal.ZERO) < 0) {
            throw new IllegalArgumentException("Le prix doit être supérieur ou égal à 0");
        }

        existingComplement.setNom(nom);
        existingComplement.setType(type);
        existingComplement.setPrix(prix);
        existingComplement.setDisponible(disponible);

        return complementRepository.update(existingComplement);
    }

    @Override
    public void deleteComplement(UUID id) {
        if (!complementRepository.delete(id)) {
            throw new RuntimeException("Impossible de supprimer le complément avec l'ID: " + id);
        }
    }

    @Override
    public void toggleAvailability(UUID id) {
        Complement complement = getComplementById(id);
        complement.setDisponible(!complement.isDisponible());
        complementRepository.update(complement);
        logger.info("Disponibilité du complément {} changée à: {}", complement.getNom(), complement.isDisponible());
    }
}
