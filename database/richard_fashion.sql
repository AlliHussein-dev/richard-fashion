-- =========================================================
--  RICHARD FASHION - Boutique en ligne (habits & accessoires)
--  Projet E-commerce - Genie Logiciel
--  Base de donnees relationnelle MySQL
-- =========================================================

CREATE DATABASE IF NOT EXISTS richard_fashion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE richard_fashion;

-- ---------------------------------------------------------
-- 1. UTILISATEURS (acteurs : client, vendeur, administrateur)
-- ---------------------------------------------------------
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(30),
    adresse VARCHAR(255),
    role ENUM('client','vendeur','administrateur') NOT NULL DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2. CATEGORIES (au moins 5 categories differentes)
-- ---------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. PRODUITS (lies a un vendeur)
-- ---------------------------------------------------------
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendeur_id INT NOT NULL,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    taille VARCHAR(50),
    couleur VARCHAR(50),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. IMAGES PRODUITS (plusieurs photos par produit)
-- ---------------------------------------------------------
CREATE TABLE produit_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    chemin_image VARCHAR(255) NOT NULL,
    est_principale TINYINT(1) DEFAULT 0,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 5. PRODUIT_CATEGORIES (table pivot N-N)
-- ---------------------------------------------------------
CREATE TABLE produit_categories (
    produit_id INT NOT NULL,
    categorie_id INT NOT NULL,
    PRIMARY KEY (produit_id, categorie_id),
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 6. PANIER (un panier actif par client)
-- ---------------------------------------------------------
CREATE TABLE paniers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE panier_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    panier_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    FOREIGN KEY (panier_id) REFERENCES paniers(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 7. COMMANDES
-- ---------------------------------------------------------
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente','payee','en_preparation','expediee','livree','annulee') DEFAULT 'en_attente',
    adresse_livraison VARCHAR(255) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE commande_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 8. PAIEMENTS (service de paiement simule)
-- ---------------------------------------------------------
CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    methode ENUM('mobile_money','carte_bancaire','especes_a_la_livraison') NOT NULL,
    reference_transaction VARCHAR(100),
    statut ENUM('en_attente','valide','echoue') DEFAULT 'en_attente',
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 9. LIVRAISONS (service de livraison simule)
-- ---------------------------------------------------------
CREATE TABLE livraisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL UNIQUE,
    transporteur VARCHAR(100) DEFAULT 'Richard Fashion Express',
    statut ENUM('en_preparation','en_cours','livree') DEFAULT 'en_preparation',
    date_expedition DATETIME,
    date_livraison_prevue DATE,
    adresse VARCHAR(255) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
--  DONNEES DE DEMONSTRATION
-- =========================================================

-- Utilisateurs : 1 admin, 2 vendeurs, 2 clients (mdp = "password123" hache en bcrypt)
INSERT INTO utilisateurs (nom, email, mot_de_passe, telephone, adresse, role) VALUES
('richard', 'richard@richardfashion.bi', '$2b$10$0mLAxZ36NVxGTYAS88saCun2PBfg/DlCbnGeJaSeo/W5i3VLBQPym', '+25761000000', 'Bujumbura', 'administrateur'),
('espoir', 'espoir@richardfashion.bi', '$2b$10$f5JOVP40IXliEW04HkILQ.gAke.sWprFGWIlpn4JsXJZ05cZM2YCi', '+25761000001', 'Bujumbura', 'administrateur'),
('Eric Ndayishimiye', 'vendeur1@richardfashion.bi', '$2b$10$zZziMH8ghyDP/rN67eLXC.uTIUNBLzHCweCJL2d0UUAi3.0eC2B/.', '+25761111111', 'Bujumbura', 'vendeur'),
('Claudine Niyonzima', 'vendeur2@richardfashion.bi', '$2b$10$zZziMH8ghyDP/rN67eLXC.uTIUNBLzHCweCJL2d0UUAi3.0eC2B/.', '+25761222222', 'Bujumbura', 'vendeur'),
('Jean Client', 'client1@richardfashion.bi', '$2b$10$zZziMH8ghyDP/rN67eLXC.uTIUNBLzHCweCJL2d0UUAi3.0eC2B/.', '+25761333333', 'Rohero, Bujumbura', 'client'),
('Alice Client', 'client2@richardfashion.bi', '$2b$10$zZziMH8ghyDP/rN67eLXC.uTIUNBLzHCweCJL2d0UUAi3.0eC2B/.', '+25761444444', 'Kanyosha, Bujumbura', 'client');
-- Comptes admin : richard@richardfashion.bi / 1234  et  espoir@richardfashion.bi / 000
-- Mot de passe pour les comptes vendeur/client de demo : password123

-- 5 categories minimum
INSERT INTO categories (nom, description) VALUES
('Vetements Homme', 'Chemises, jeans, vestes pour homme'),
('Vetements Femme', 'Robes, blazers pour femme'),
('Chaussures', 'Baskets et chaussures de ville'),
('Accessoires', 'Lunettes, montres, ceintures'),
('Sacs & Bagagerie', 'Sacs a main et bagagerie');

-- 10 produits (vendeur_id 2 ou 3)
INSERT INTO produits (vendeur_id, nom, description, prix, stock, taille, couleur) VALUES
(2, 'Chemise Slim Homme', 'Chemise en coton, coupe ajustee, ideale pour le bureau.', 45000, 30, 'M-XL', 'Bleu marine'),
(3, "Robe d'ete Femme", 'Robe legere fleurie, parfaite pour la saison chaude.', 60000, 20, 'S-L', 'Rouge'),
(2, 'Jean Classique Homme', 'Jean droit resistant, denim de qualite.', 55000, 25, '30-38', 'Bleu'),
(2, 'Baskets Sport Unisexe', 'Baskets confortables pour le sport et le quotidien.', 80000, 15, '38-44', 'Vert/Blanc'),
(3, 'Sac a Main Femme', 'Sac a main en cuir synthetique, plusieurs compartiments.', 70000, 12, 'Unique', 'Violet'),
(2, 'Ceinture Cuir Homme', 'Ceinture en cuir veritable, boucle metallique.', 25000, 40, 'Unique', 'Marron'),
(3, 'Veste Blazer Femme', 'Blazer elegant pour un look professionnel.', 95000, 10, 'S-XL', 'Orange'),
(2, 'Chaussures de Ville Homme', 'Chaussures en cuir pour occasions formelles.', 90000, 18, '40-45', 'Noir'),
(3, 'Lunettes de Soleil', 'Protection UV, monture legere unisexe.', 30000, 22, 'Unique', 'Jaune'),
(2, 'Montre Elegante', 'Montre a quartz, bracelet acier inoxydable.', 120000, 8, 'Unique', 'Turquoise');

-- Images (2 vraies photos par produit, la premiere en image principale)
INSERT INTO produit_images (produit_id, chemin_image, est_principale) VALUES
(1, 'assets/images/produits/produit1_1.jpg', 1),
(1, 'assets/images/produits/produit1_2.jpg', 0),
(2, 'assets/images/produits/produit2_1.jpg', 1),
(2, 'assets/images/produits/produit2_2.jpg', 0),
(3, 'assets/images/produits/produit3_1.jpg', 1),
(3, 'assets/images/produits/produit3_2.jpg', 0),
(4, 'assets/images/produits/produit4_1.jpg', 1),
(4, 'assets/images/produits/produit4_2.jpg', 0),
(5, 'assets/images/produits/produit5_1.jpg', 1),
(5, 'assets/images/produits/produit5_2.jpg', 0),
(6, 'assets/images/produits/produit6_1.jpg', 1),
(6, 'assets/images/produits/produit6_2.jpg', 0),
(7, 'assets/images/produits/produit7_1.jpg', 1),
(7, 'assets/images/produits/produit7_2.jpg', 0),
(8, 'assets/images/produits/produit8_1.jpg', 1),
(8, 'assets/images/produits/produit8_2.jpg', 0),
(9, 'assets/images/produits/produit9_1.jpg', 1),
(9, 'assets/images/produits/produit9_2.jpg', 0),
(10, 'assets/images/produits/produit10_1.jpg', 1),
(10, 'assets/images/produits/produit10_2.jpg', 0),
(10, 'assets/images/produits/produit10_3.jpg', 0),
(2, 'assets/images/produits/produit2_3.jpg', 0),
(3, 'assets/images/produits/produit3_3.jpg', 0),
(7, 'assets/images/produits/produit7_3.jpg', 0),
(8, 'assets/images/produits/produit8_3.jpg', 0),
(8, 'assets/images/produits/produit8_4.jpg', 0),
(9, 'assets/images/produits/produit9_3.jpg', 0);

-- Association produits <-> categories (chaque produit relie a une ou plusieurs des 5 categories)
INSERT INTO produit_categories (produit_id, categorie_id) VALUES
(1, 1),               -- Chemise -> Vetements Homme
(2, 2),               -- Robe -> Vetements Femme
(3, 1),               -- Jean -> Vetements Homme
(4, 3), (4, 1), (4,2),-- Baskets -> Chaussures + Homme + Femme (unisexe)
(5, 5), (5, 4),       -- Sac -> Sacs & Bagagerie + Accessoires
(6, 4), (6, 1),       -- Ceinture -> Accessoires + Homme
(7, 2),               -- Blazer -> Vetements Femme
(8, 3), (8, 1),       -- Chaussures ville -> Chaussures + Homme
(9, 4),               -- Lunettes -> Accessoires
(10, 4);              -- Montre -> Accessoires
