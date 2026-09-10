# RICHARD FASHION — Boutique en ligne d'habits & accessoires
Projet academique — Cours E-commerce, Genie Logiciel

## 1. Description
Application web e-commerce fonctionnelle permettant la vente en ligne d'habits
et d'accessoires. Elle implemente le flux complet :

```
Accueil → Produit → Panier → Commande → Paiement → Confirmation → Livraison
```

avec 3 acteurs humains (**Client**, **Vendeur**, **Administrateur**) et 2
services simules (**Service de paiement**, **Service de livraison**), plus le
**Systeme** (l'application elle-meme, la base de donnees et les regles
metier) qui orchestre tout.

## 2. Technologies
- PHP 8 (PDO, sessions, MVC simplifie en procedural)
- MySQL / MariaDB
- Bootstrap 5 + Bootstrap Icons (CDN)
- Aucune dependance externe cote serveur

## 3. Installation (XAMPP / WAMP / Laragon)
1. Copier le dossier `richard_fashion/` dans `htdocs/` (XAMPP) ou `www/` (WAMP).
2. Ouvrir phpMyAdmin, creer la base en important `database/richard_fashion.sql`
   (le script cree la base `richard_fashion` et insere les donnees de demo).
3. Verifier les identifiants dans `config/database.php` (par defaut :
   host=localhost, user=root, password vide — configuration XAMPP standard).
4. Lancer Apache + MySQL, puis ouvrir `http://localhost/richard_fashion/`.

## 4. Comptes de demonstration
Mot de passe pour tous les comptes : **password123**

| Role           | Email                          |
|----------------|---------------------------------|
| Administrateur | admin@richardfashion.bi         |
| Vendeur        | vendeur1@richardfashion.bi      |
| Vendeur        | vendeur2@richardfashion.bi      |
| Client         | client1@richardfashion.bi       |
| Client         | client2@richardfashion.bi       |

## 5. Acteurs et fonctionnalites

**Client** : parcourir le catalogue, filtrer par categorie, consulter une
fiche produit (plusieurs photos), ajouter au panier, passer commande,
choisir un mode de paiement, recevoir une confirmation, suivre sa livraison.

**Vendeur** : tableau de bord, ajout de produits (plusieurs photos,
plusieurs categories), modification de ses produits, consultation des
commandes contenant ses produits.

**Administrateur** : statistiques globales, gestion de tous les produits,
gestion des commandes (mise a jour du statut de livraison), gestion des
utilisateurs.

**Service de paiement** (`services/PaiementService.php`) : simule
l'integration a une passerelle externe (Mobile Money, carte bancaire,
especes a la livraison). Genere une reference de transaction et un statut.
Dans un contexte reel, cette classe appellerait l'API du prestataire
(Lumicash, EcoCash, Stripe...).

**Service de livraison** (`services/LivraisonService.php`) : simule la
creation d'une expedition aupres d'un transporteur et le suivi de son
statut (en preparation → en cours → livree).

## 6. Base de donnees — relations entre les tables

```
utilisateurs (1) ───< produits (N)              [un vendeur possede plusieurs produits]
produits (1) ───< produit_images (N)             [un produit a plusieurs photos]
produits (N) ───< produit_categories >─── (N) categories   [table pivot N-N, ≥5 categories]
utilisateurs (1) ───< paniers (1)                [un panier actif par client]
paniers (1) ───< panier_details (N) >─── produits
utilisateurs (1) ───< commandes (N)
commandes (1) ───< commande_details (N) >─── produits
commandes (1) ───< paiements (N)                 [historique des tentatives de paiement]
commandes (1) ─── livraisons (1)                 [une livraison par commande]
```

Cle etrangere de reference : `produits.vendeur_id → utilisateurs.id`,
`commandes.client_id → utilisateurs.id`, etc. Toutes les FK sont en
`ON DELETE CASCADE` sauf `commande_details.produit_id` (historique conserve).

## 7. Catalogue de demonstration
- **5 categories** : Vetements Homme, Vetements Femme, Chaussures,
  Accessoires, Sacs & Bagagerie.
- **10 produits** repartis dans ces categories (certains dans plusieurs,
  ex. les baskets sont a la fois Chaussures/Homme/Femme), chacun avec
  2 vraies photos (`assets/images/produits/produitN_1.jpg` et `_2.jpg`),
  affichees en carrousel sur la fiche produit.

## 8. Arborescence
```
richard_fashion/
├── admin/                  (dashboard, produits, commandes, utilisateurs)
├── vendeur/                (dashboard, ajout/modif produit)
├── assets/{css,images}/
├── config/database.php
├── includes/{header,footer,auth}.php
├── services/{PaiementService,LivraisonService}.php
├── database/richard_fashion.sql
├── index.php | produit.php | panier.php | commande.php
├── paiement.php | confirmation.php | livraison.php
├── login.php | register.php | logout.php | mes_commandes.php
```

## 9. Pistes d'amelioration (pour la soutenance)
- Ajout d'avis/notes clients sur les produits
- Recherche full-text et tri (prix, popularite)
- Vraie integration API de paiement mobile money
- Notifications email/SMS a chaque changement de statut
- Gestion multi-images en drag & drop cote vendeur
