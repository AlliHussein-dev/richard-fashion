# Déployer Richard Fashion sur InfinityFree (hébergement gratuit)

## Étape 1 — Créer le compte
1. Va sur **https://infinityfree.net** → "Sign Up" → crée un compte avec ton email.
2. Confirme ton email (lien reçu par mail).

## Étape 2 — Créer un compte d'hébergement (site)
1. Dans le dashboard, clique **"Create Account"**.
2. Choisis un sous-domaine gratuit, par exemple `richardfashion.infinityfreeapp.com`,
   ou connecte un nom de domaine que tu possèdes déjà.
3. Attends l'activation (souvent 5 à 15 minutes, parfois plus).

## Étape 3 — Créer la base de données MySQL
1. Dans le **vPanel** (panneau du site), section **"MySQL Databases"**.
2. Clique "Create Database", donne un nom (ex: `richard`).
3. InfinityFree génère automatiquement un nom complet du style
   `epiz_12345678_richard` — **note-le**, ainsi que :
   - l'hôte MySQL (ex: `sql200.infinityfree.com`)
   - le nom d'utilisateur (souvent identique au nom de la base)
   - le mot de passe (celui que tu choisis à la création)

## Étape 4 — Adapter les fichiers AVANT d'uploader
1. Ouvre **`config/database.infinityfree.php`** fourni dans ce dossier,
   remplace les 4 valeurs `REMPLACE_PAR_...` par celles notées à l'étape 3.
2. Renomme ce fichier en `database.php` et remplace celui qui se trouve
   déjà dans `config/` (écrase l'ancien qui pointait vers `localhost`).
3. Le fichier **`database/richard_fashion_infinityfree.sql`** est déjà
   prêt : il ne contient pas la commande `CREATE DATABASE` (inutile,
   car la base existe déjà côté InfinityFree). C'est celui-là qu'il
   faut importer, pas `richard_fashion.sql`.

## Étape 5 — Importer la base de données
1. Dans le vPanel, ouvre **phpMyAdmin**.
2. Sélectionne ta base (`epiz_XXXXXXXX_richard`) dans la colonne de gauche.
3. Onglet **"Importer"** → choisis `richard_fashion_infinityfree.sql` → **Exécuter**.
4. Vérifie que les tables sont bien créées (10 produits, 5 catégories, etc.).

## Étape 6 — Uploader les fichiers du site
Deux methodes possibles :

**A. Gestionnaire de fichiers en ligne (le plus simple)**
1. Dans le vPanel → "File Manager".
2. Va dans le dossier `htdocs`.
3. Upload **tout le contenu** du dossier `richard_fashion/` (pas le dossier
   lui-même, mais ce qu'il y a dedans : `index.php`, `assets/`, etc.)
   directement à la racine de `htdocs`.

**B. FTP (FileZilla) — plus rapide pour beaucoup de fichiers**
1. Récupère les identifiants FTP dans le vPanel → "FTP Accounts".
2. Ouvre FileZilla, connecte-toi (hôte FTP, utilisateur, mot de passe, port 21).
3. Glisse tout le contenu de `richard_fashion/` dans le dossier `htdocs`
   du serveur distant.

## Étape 7 — Tester
1. Ouvre `http://richardfashion.infinityfreeapp.com` (ou ton domaine).
2. Teste la connexion : `richard@richardfashion.bi` / `1234`.
3. Vérifie que les photos s'affichent (elles doivent être dans
   `htdocs/assets/images/produits/`).

## Problèmes fréquents
- **"Error establishing a database connection"** → vérifie les 4 valeurs
  dans `config/database.php` (host, nom base, user, password).
- **Photos qui ne s'affichent pas** → vérifie que le dossier
  `assets/images/` a bien été uploadé en entier avec les fichiers dedans.
- **Site très lent** → normal sur un hébergement gratuit, surtout au
  premier chargement ; suffisant pour une démo/soutenance.
- **Redirection en boucle ou session qui ne se garde pas** → certains
  hébergeurs gratuits limitent les sessions PHP ; si ça arrive, teste
  avec un autre navigateur ou vide le cache.
