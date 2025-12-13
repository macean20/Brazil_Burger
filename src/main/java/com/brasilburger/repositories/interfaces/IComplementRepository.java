package com.brasilburger.repositories.interfaces;

import com.brasilburger.models.Complement;
import com.brasilburger.models.TypeComplement;

import java.util.List;
import java.util.Optional;
import java.util.UUID;

public interface IComplementRepository {
    Complement create(Complement complement);
    Optional<Complement> findById(UUID id);
    List<Complement> findAll();
    List<Complement> findByType(TypeComplement type);
    List<Complement> findAvailable();
    Complement update(Complement complement);
    boolean delete(UUID id);
}
