package com.brasilburger.repositories.impl;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Burger;
import com.brasilburger.repositories.interfaces.IBurgerRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public class BurgerRepository implements IBurgerRepository {
    private static final Logger logger = LoggerFactory.getLogger(BurgerRepository.class);

    @Override
    public Burger create(Burger burger) {
        String sql = "INSERT INTO burger (id, nom, description, prix, image_url, disponible) VALUES (?, ?, ?, ?, ?, ?)";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, burger.getId());
            stmt.setString(2, burger.getNom());
            stmt.setString(3, burger.getDescription());
            stmt.setBigDecimal(4, burger.getPrix());
            stmt.setString(5, burger.getImageUrl());
            stmt.setBoolean(6, burger.isDisponible());

            stmt.executeUpdate();
            logger.info("Burger créé avec succès: {}", burger.getNom());
            return burger;

        } catch (SQLException e) {
            logger.error("Erreur lors de la création du burger", e);
            throw new RuntimeException("Erreur lors de la création du burger", e);
        }
    }

    @Override
    public Optional<Burger> findById(UUID id) {
        String sql = "SELECT * FROM burger WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, id);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                return Optional.of(mapResultSetToBurger(rs));
            }
            return Optional.empty();

        } catch (SQLException e) {
            logger.error("Erreur lors de la recherche du burger", e);
            throw new RuntimeException("Erreur lors de la recherche du burger", e);
        }
    }

    @Override
    public List<Burger> findAll() {
        String sql = "SELECT * FROM burger ORDER BY nom";
        List<Burger> burgers = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                burgers.add(mapResultSetToBurger(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des burgers", e);
            throw new RuntimeException("Erreur lors de la récupération des burgers", e);
        }

        return burgers;
    }

    @Override
    public List<Burger> findAvailable() {
        String sql = "SELECT * FROM burger WHERE disponible = true ORDER BY nom";
        List<Burger> burgers = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                burgers.add(mapResultSetToBurger(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des burgers disponibles", e);
            throw new RuntimeException("Erreur lors de la récupération des burgers disponibles", e);
        }

        return burgers;
    }

    @Override
    public Burger update(Burger burger) {
        String sql = "UPDATE burger SET nom = ?, description = ?, prix = ?, image_url = ?, disponible = ? WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, burger.getNom());
            stmt.setString(2, burger.getDescription());
            stmt.setBigDecimal(3, burger.getPrix());
            stmt.setString(4, burger.getImageUrl());
            stmt.setBoolean(5, burger.isDisponible());
            stmt.setObject(6, burger.getId());

            int rowsAffected = stmt.executeUpdate();
            if (rowsAffected == 0) {
                throw new RuntimeException("Burger non trouvé pour la mise à jour");
            }

            logger.info("Burger mis à jour avec succès: {}", burger.getNom());
            return burger;

        } catch (SQLException e) {
            logger.error("Erreur lors de la mise à jour du burger", e);
            throw new RuntimeException("Erreur lors de la mise à jour du burger", e);
        }
    }

    @Override
    public boolean delete(UUID id) {
        String sql = "DELETE FROM burger WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, id);
            int rowsAffected = stmt.executeUpdate();

            if (rowsAffected > 0) {
                logger.info("Burger supprimé avec succès");
                return true;
            }
            return false;

        } catch (SQLException e) {
            logger.error("Erreur lors de la suppression du burger", e);
            throw new RuntimeException("Erreur lors de la suppression du burger", e);
        }
    }

    @Override
    public boolean existsByNom(String nom) {
        String sql = "SELECT COUNT(*) FROM burger WHERE nom = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, nom);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                return rs.getInt(1) > 0;
            }
            return false;

        } catch (SQLException e) {
            logger.error("Erreur lors de la vérification de l'existence du burger", e);
            throw new RuntimeException("Erreur lors de la vérification de l'existence du burger", e);
        }
    }

    private Burger mapResultSetToBurger(ResultSet rs) throws SQLException {
        Burger burger = new Burger();
        burger.setId((UUID) rs.getObject("id"));
        burger.setNom(rs.getString("nom"));
        burger.setDescription(rs.getString("description"));
        burger.setPrix(rs.getBigDecimal("prix"));
        burger.setImageUrl(rs.getString("image_url"));
        burger.setDisponible(rs.getBoolean("disponible"));
        burger.setCreatedAt(rs.getTimestamp("created_at").toLocalDateTime());
        return burger;
    }
}
