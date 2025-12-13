package com.brasilburger.repositories.impl;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Burger;
import com.brasilburger.models.Complement;
import com.brasilburger.models.Menu;
import com.brasilburger.models.TypeComplement;
import com.brasilburger.repositories.interfaces.IMenuRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

public class MenuRepository implements IMenuRepository {
    private static final Logger logger = LoggerFactory.getLogger(MenuRepository.class);

    @Override
    public Menu create(Menu menu) {
        String sql = "INSERT INTO menu (id, nom, description, burger_id, boisson_id, frite_id, prix_total, disponible) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, menu.getId());
            stmt.setString(2, menu.getNom());
            stmt.setString(3, menu.getDescription());
            stmt.setObject(4, menu.getBurgerId());
            stmt.setObject(5, menu.getBoissonId());
            stmt.setObject(6, menu.getFriteId());
            stmt.setBigDecimal(7, menu.getPrixTotal());
            stmt.setBoolean(8, menu.isDisponible());

            stmt.executeUpdate();
            logger.info("Menu créé avec succès: {}", menu.getNom());
            return menu;

        } catch (SQLException e) {
            logger.error("Erreur lors de la création du menu", e);
            throw new RuntimeException("Erreur lors de la création du menu", e);
        }
    }

    @Override
    public Optional<Menu> findById(UUID id) {
        String sql = "SELECT * FROM menu WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, id);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                return Optional.of(mapResultSetToMenu(rs));
            }
            return Optional.empty();

        } catch (SQLException e) {
            logger.error("Erreur lors de la recherche du menu", e);
            throw new RuntimeException("Erreur lors de la recherche du menu", e);
        }
    }

    @Override
    public List<Menu> findAll() {
        String sql = "SELECT * FROM menu ORDER BY nom";
        List<Menu> menus = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                menus.add(mapResultSetToMenu(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des menus", e);
            throw new RuntimeException("Erreur lors de la récupération des menus", e);
        }

        return menus;
    }

    @Override
    public List<Menu> findAllWithDetails() {
        String sql = "SELECT m.*, " +
                     "b.id as burger_id, b.nom as burger_nom, b.description as burger_desc, b.prix as burger_prix, b.disponible as burger_dispo, b.created_at as burger_created, " +
                     "bo.id as boisson_id, bo.nom as boisson_nom, bo.type as boisson_type, bo.prix as boisson_prix, bo.disponible as boisson_dispo, bo.created_at as boisson_created, " +
                     "f.id as frite_id, f.nom as frite_nom, f.type as frite_type, f.prix as frite_prix, f.disponible as frite_dispo, f.created_at as frite_created " +
                     "FROM menu m " +
                     "LEFT JOIN burger b ON m.burger_id = b.id " +
                     "LEFT JOIN complement bo ON m.boisson_id = bo.id " +
                     "LEFT JOIN complement f ON m.frite_id = f.id " +
                     "ORDER BY m.nom";

        List<Menu> menus = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                Menu menu = mapResultSetToMenu(rs);

                Burger burger = new Burger();
                burger.setId((UUID) rs.getObject("burger_id"));
                burger.setNom(rs.getString("burger_nom"));
                burger.setDescription(rs.getString("burger_desc"));
                burger.setPrix(rs.getBigDecimal("burger_prix"));
                burger.setDisponible(rs.getBoolean("burger_dispo"));
                burger.setCreatedAt(rs.getTimestamp("burger_created").toLocalDateTime());
                menu.setBurger(burger);

                Complement boisson = new Complement();
                boisson.setId((UUID) rs.getObject("boisson_id"));
                boisson.setNom(rs.getString("boisson_nom"));
                boisson.setType(TypeComplement.valueOf(rs.getString("boisson_type")));
                boisson.setPrix(rs.getBigDecimal("boisson_prix"));
                boisson.setDisponible(rs.getBoolean("boisson_dispo"));
                boisson.setCreatedAt(rs.getTimestamp("boisson_created").toLocalDateTime());
                menu.setBoisson(boisson);

                Complement frite = new Complement();
                frite.setId((UUID) rs.getObject("frite_id"));
                frite.setNom(rs.getString("frite_nom"));
                frite.setType(TypeComplement.valueOf(rs.getString("frite_type")));
                frite.setPrix(rs.getBigDecimal("frite_prix"));
                frite.setDisponible(rs.getBoolean("frite_dispo"));
                frite.setCreatedAt(rs.getTimestamp("frite_created").toLocalDateTime());
                menu.setFrite(frite);

                menus.add(menu);
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des menus avec détails", e);
            throw new RuntimeException("Erreur lors de la récupération des menus avec détails", e);
        }

        return menus;
    }

    @Override
    public List<Menu> findAvailable() {
        String sql = "SELECT * FROM menu WHERE disponible = true ORDER BY nom";
        List<Menu> menus = new ArrayList<>();

        try (Connection conn = DatabaseConfig.getConnection();
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                menus.add(mapResultSetToMenu(rs));
            }

        } catch (SQLException e) {
            logger.error("Erreur lors de la récupération des menus disponibles", e);
            throw new RuntimeException("Erreur lors de la récupération des menus disponibles", e);
        }

        return menus;
    }

    @Override
    public Menu update(Menu menu) {
        String sql = "UPDATE menu SET nom = ?, description = ?, burger_id = ?, boisson_id = ?, frite_id = ?, prix_total = ?, disponible = ? WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, menu.getNom());
            stmt.setString(2, menu.getDescription());
            stmt.setObject(3, menu.getBurgerId());
            stmt.setObject(4, menu.getBoissonId());
            stmt.setObject(5, menu.getFriteId());
            stmt.setBigDecimal(6, menu.getPrixTotal());
            stmt.setBoolean(7, menu.isDisponible());
            stmt.setObject(8, menu.getId());

            int rowsAffected = stmt.executeUpdate();
            if (rowsAffected == 0) {
                throw new RuntimeException("Menu non trouvé pour la mise à jour");
            }

            logger.info("Menu mis à jour avec succès: {}", menu.getNom());
            return menu;

        } catch (SQLException e) {
            logger.error("Erreur lors de la mise à jour du menu", e);
            throw new RuntimeException("Erreur lors de la mise à jour du menu", e);
        }
    }

    @Override
    public boolean delete(UUID id) {
        String sql = "DELETE FROM menu WHERE id = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setObject(1, id);
            int rowsAffected = stmt.executeUpdate();

            if (rowsAffected > 0) {
                logger.info("Menu supprimé avec succès");
                return true;
            }
            return false;

        } catch (SQLException e) {
            logger.error("Erreur lors de la suppression du menu", e);
            throw new RuntimeException("Erreur lors de la suppression du menu", e);
        }
    }

    @Override
    public boolean existsByNom(String nom) {
        String sql = "SELECT COUNT(*) FROM menu WHERE nom = ?";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, nom);
            ResultSet rs = stmt.executeQuery();

            if (rs.next()) {
                return rs.getInt(1) > 0;
            }
            return false;

        } catch (SQLException e) {
            logger.error("Erreur lors de la vérification de l'existence du menu", e);
            throw new RuntimeException("Erreur lors de la vérification de l'existence du menu", e);
        }
    }

    private Menu mapResultSetToMenu(ResultSet rs) throws SQLException {
        Menu menu = new Menu();
        menu.setId((UUID) rs.getObject("id"));
        menu.setNom(rs.getString("nom"));
        menu.setDescription(rs.getString("description"));
        menu.setBurgerId((UUID) rs.getObject("burger_id"));
        menu.setBoissonId((UUID) rs.getObject("boisson_id"));
        menu.setFriteId((UUID) rs.getObject("frite_id"));
        menu.setPrixTotal(rs.getBigDecimal("prix_total"));
        menu.setDisponible(rs.getBoolean("disponible"));
        menu.setCreatedAt(rs.getTimestamp("created_at").toLocalDateTime());
        return menu;
    }
}
