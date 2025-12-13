package com.brasilburger.repositories.impl;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Complement;
import com.brasilburger.models.TypeComplement;
import com.brasilburger.repositories.interfaces.IComplementRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public class ComplementRepository implements IComplementRepository {
    private static final Logger logger = LoggerFactory.getLogger(ComplementRepository.class);

    @Override
    public Complement create(Complement complement) {
        String sql = "INSERT INTO complement (id, nom, type, prix, disponible) VALUES (?, ?, ?, ?, ?)";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, complement.getId());
            stmt.setString(2, complement.getNom());
            stmt.setString(3, complement.getType().name());
            stmt.setBigDecimal(4, complement.getPrix());
            stmt.setBoolean(5, complement.isDisponible());

            stmt.executeUpdate();
            logger.info("Complément créé avec succès: {}", complement.getNom());
            return complement;

        } catch (SQLException e) {
            logger.error("Erreur lors de la création du complément", e);
            throw new RuntimeException("Erreur lors de la création du complément", e);
        }
    }

    @Override
    public Optional<Complement> findById(UUID id) {
        String sql = "SELECT * FROM complement WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, id);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                return Optional.of(mapResultSetToComplement(rs));
            }
            return Optional.empty();

        } catch (SQLException e) {
            logger.error("Erreur lors de la recherche du complément", e);
            throw new RuntimeException("Erreur lors de la recherche du complément", e);
        }
    }

    @Override
    public List<Complement> findAll() {
        String sql = "SELECT * FROM complement ORDER BY type, nom";
        List<Complement> complements = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                complements.add(mapResultSetToComplement(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des compléments", e);
            throw new RuntimeException("Erreur lors de la récupération des compléments", e);
        }

        return complements;
    }

    @Override
    public List<Complement> findByType(TypeComplement type) {
        String sql = "SELECT * FROM complement WHERE type = ? ORDER BY nom";
        List<Complement> complements = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, type.name());
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                complements.add(mapResultSetToComplement(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des compléments par type", e);
            throw new RuntimeException("Erreur lors de la récupération des compléments par type", e);
        }

        return complements;
    }

    @Override
    public List<Complement> findAvailable() {
        String sql = "SELECT * FROM complement WHERE disponible = true ORDER BY type, nom";
        List<Complement> complements = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                complements.add(mapResultSetToComplement(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des compléments disponibles", e);
            throw new RuntimeException("Erreur lors de la récupération des compléments disponibles", e);
        }

        return complements;
    }

    @Override
    public Complement update(Complement complement) {
        String sql = "UPDATE complement SET nom = ?, type = ?, prix = ?, disponible = ? WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, complement.getNom());
            stmt.setString(2, complement.getType().name());
            stmt.setBigDecimal(3, complement.getPrix());
            stmt.setBoolean(4, complement.isDisponible());
            stmt.setObject(5, complement.getId());

            int rowsAffected = stmt.executeUpdate();
            if (rowsAffected == 0) {
                throw new RuntimeException("Complément non trouvé pour la mise à jour");
            }

            logger.info("Complément mis à jour avec succès: {}", complement.getNom());
            return complement;

        } catch (SQLException e) {
            logger.error("Erreur lors de la mise à jour du complément", e);
            throw new RuntimeException("Erreur lors de la mise à jour du complément", e);
        }
    }

    @Override
    public boolean delete(UUID id) {
        String sql = "DELETE FROM complement WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, id);
            int rowsAffected = stmt.executeUpdate();

            if (rowsAffected > 0) {
                logger.info("Complément supprimé avec succès");
                return true;
            }
            return false;

        } catch (SQLException e) {
            logger.error("Erreur lors de la suppression du complément", e);
            throw new RuntimeException("Erreur lors de la suppression du complément", e);
        }
    }

    private Complement mapResultSetToComplement(ResultSet rs) throws SQLException {
        Complement complement = new Complement();
        complement.setId((UUID) rs.getObject("id"));
        complement.setNom(rs.getString("nom"));
        complement.setType(TypeComplement.valueOf(rs.getString("type")));
        complement.setPrix(rs.getBigDecimal("prix"));
        complement.setDisponible(rs.getBoolean("disponible"));
        complement.setCreatedAt(rs.getTimestamp("created_at").toLocalDateTime());
        return complement;
    }
}
