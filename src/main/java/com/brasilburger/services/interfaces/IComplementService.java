package com.brasilburger.services.interfaces;

import com.brasilburger.models.Complement;
import com.brasilburger.models.TypeComplement;

import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public interface IComplementService {
    Complement createComplement(String nom, TypeComplement type, BigDecimal prix);
    Complement getComplementById(UUID id);
    List<Complement> getAllComplements();
    List<Complement> getComplementsByType(TypeComplement type);
    List<Complement> getAvailableComplements();
    Complement updateComplement(UUID id, String nom, TypeComplement type, BigDecimal prix, boolean disponible);
    void deleteComplement(UUID id);
    void toggleAvailability(UUID id);
}
