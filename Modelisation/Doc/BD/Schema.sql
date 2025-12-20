CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP TABLE IF EXISTS paiement CASCADE;
DROP TABLE IF EXISTS ligne_commande CASCADE;
DROP TABLE IF EXISTS commande CASCADE;
DROP TABLE IF EXISTS quartier CASCADE;
DROP TABLE IF EXISTS zone CASCADE;
DROP TABLE IF EXISTS menu CASCADE;
DROP TABLE IF EXISTS complement CASCADE;
DROP TABLE IF EXISTS burger CASCADE;
DROP TABLE IF EXISTS livreur CASCADE;
DROP TABLE IF EXISTS gestionnaire CASCADE;
DROP TABLE IF EXISTS client CASCADE;

CREATE TABLE client (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gestionnaire (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'GESTIONNAIRE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE livreur (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE burger (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL CHECK (prix > 0),
    image_url VARCHAR(255),
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE complement (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    type VARCHAR(20) NOT NULL CHECK (type IN ('BOISSON', 'FRITE')),
    prix DECIMAL(10, 2) NOT NULL CHECK (prix >= 0),
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    burger_id UUID NOT NULL REFERENCES burger(id) ON DELETE CASCADE,
    boisson_id UUID NOT NULL REFERENCES complement(id) ON DELETE RESTRICT,
    frite_id UUID NOT NULL REFERENCES complement(id) ON DELETE RESTRICT,
    prix_total DECIMAL(10, 2) NOT NULL CHECK (prix_total > 0),
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE zone (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL UNIQUE,
    prix_livraison DECIMAL(10, 2) NOT NULL CHECK (prix_livraison >= 0),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quartier (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom VARCHAR(100) NOT NULL,
    zone_id UUID NOT NULL REFERENCES zone(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(nom, zone_id)
);

CREATE TABLE commande (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    numero_commande VARCHAR(50) UNIQUE NOT NULL,
    client_id UUID NOT NULL REFERENCES client(id) ON DELETE CASCADE,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(10, 2) NOT NULL CHECK (montant_total >= 0),
    etat VARCHAR(20) NOT NULL DEFAULT 'EN_ATTENTE'
        CHECK (etat IN ('EN_ATTENTE', 'VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE', 'ANNULEE')),
    zone_id UUID REFERENCES zone(id) ON DELETE SET NULL,
    livreur_id UUID REFERENCES livreur(id) ON DELETE SET NULL,
    adresse_livraison TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ligne_commande (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    commande_id UUID NOT NULL REFERENCES commande(id) ON DELETE CASCADE,
    type_item VARCHAR(20) NOT NULL CHECK (type_item IN ('BURGER', 'MENU')),
    burger_id UUID REFERENCES burger(id) ON DELETE RESTRICT,
    menu_id UUID REFERENCES menu(id) ON DELETE RESTRICT,
    quantite INTEGER NOT NULL CHECK (quantite > 0),
    prix_unitaire DECIMAL(10, 2) NOT NULL CHECK (prix_unitaire > 0),
    sous_total DECIMAL(10, 2) NOT NULL CHECK (sous_total > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (
        (type_item = 'BURGER' AND burger_id IS NOT NULL AND menu_id IS NULL) OR
        (type_item = 'MENU' AND menu_id IS NOT NULL AND burger_id IS NULL)
    )
);

CREATE TABLE paiement (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    commande_id UUID UNIQUE NOT NULL REFERENCES commande(id) ON DELETE CASCADE,
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant DECIMAL(10, 2) NOT NULL CHECK (montant > 0),
    mode_paiement VARCHAR(10) NOT NULL CHECK (mode_paiement IN ('WAVE', 'OM')),
    reference_transaction VARCHAR(100) UNIQUE NOT NULL,
    statut VARCHAR(20) DEFAULT 'EN_ATTENTE' CHECK (statut IN ('EN_ATTENTE', 'REUSSI', 'ECHOUE')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_commande_client ON commande(client_id);
CREATE INDEX idx_commande_etat ON commande(etat);
CREATE INDEX idx_commande_date ON commande(date_commande);
CREATE INDEX idx_commande_zone ON commande(zone_id);
CREATE INDEX idx_commande_livreur ON commande(livreur_id);
CREATE INDEX idx_ligne_commande_commande ON ligne_commande(commande_id);
CREATE INDEX idx_paiement_commande ON paiement(commande_id);
CREATE INDEX idx_quartier_zone ON quartier(zone_id);
CREATE INDEX idx_menu_burger ON menu(burger_id);
CREATE INDEX idx_burger_disponible ON burger(disponible);
CREATE INDEX idx_menu_disponible ON menu(disponible);

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_commande_updated_at
    BEFORE UPDATE ON commande
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE SEQUENCE IF NOT EXISTS seq_commande START 1;

CREATE OR REPLACE FUNCTION generate_numero_commande()
RETURNS TRIGGER AS $$
BEGIN
    NEW.numero_commande = 'CMD-' || TO_CHAR(CURRENT_TIMESTAMP, 'YYYYMMDD') || '-' ||
                          LPAD(NEXTVAL('seq_commande')::TEXT, 6, '0');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_generate_numero_commande
    BEFORE INSERT ON commande
    FOR EACH ROW
    WHEN (NEW.numero_commande IS NULL OR NEW.numero_commande = '')
    EXECUTE FUNCTION generate_numero_commande();

CREATE OR REPLACE VIEW v_commandes_en_cours_jour AS
SELECT COUNT(*) as total
FROM commande
WHERE DATE(date_commande) = CURRENT_DATE
  AND etat IN ('EN_ATTENTE', 'VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON');

CREATE OR REPLACE VIEW v_commandes_validees_jour AS
SELECT COUNT(*) as total
FROM commande
WHERE DATE(date_commande) = CURRENT_DATE
  AND etat IN ('VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE');

CREATE OR REPLACE VIEW v_recettes_journalieres AS
SELECT COALESCE(SUM(p.montant), 0) as total
FROM paiement p
INNER JOIN commande c ON p.commande_id = c.id
WHERE DATE(c.date_commande) = CURRENT_DATE
  AND p.statut = 'REUSSI';

CREATE OR REPLACE VIEW v_items_plus_vendus_jour AS
SELECT
    lc.type_item,
    COALESCE(b.nom, m.nom) as nom_item,
    SUM(lc.quantite) as quantite_vendue,
    SUM(lc.sous_total) as chiffre_affaires
FROM ligne_commande lc
INNER JOIN commande c ON lc.commande_id = c.id
LEFT JOIN burger b ON lc.burger_id = b.id
LEFT JOIN menu m ON lc.menu_id = m.id
WHERE DATE(c.date_commande) = CURRENT_DATE
  AND c.etat != 'ANNULEE'
GROUP BY lc.type_item, COALESCE(b.nom, m.nom)
ORDER BY quantite_vendue DESC;

CREATE OR REPLACE VIEW v_commandes_annulees_jour AS
SELECT COUNT(*) as total
FROM commande
WHERE DATE(date_commande) = CURRENT_DATE
  AND etat = 'ANNULEE';
