<?php
/**
 * Connexion a la base de donnees (PDO) - VERSION INFINITYFREE
 *
 * Remplace les 4 valeurs ci-dessous par celles données dans ton vPanel
 * InfinityFree > MySQL Databases. Exemple de valeurs typiques :
 *
 *   DB_HOST = sqlXXX.infinityfree.com   (jamais "localhost" chez InfinityFree)
 *   DB_NAME = epiz_XXXXXXXX_richard
 *   DB_USER = epiz_XXXXXXXX
 *   DB_PASS = (le mot de passe que tu as choisi a la creation de la base)
 *
 * Une fois rempli, renomme ce fichier en "database.php" et remplace
 * celui du dossier config/ avant d'uploader le projet.
 */

define('DB_HOST', 'REMPLACE_PAR_TON_HOTE_MYSQL');
define('DB_NAME', 'REMPLACE_PAR_LE_NOM_DE_TA_BASE');
define('DB_USER', 'REMPLACE_PAR_TON_UTILISATEUR_MYSQL');
define('DB_PASS', 'REMPLACE_PAR_TON_MOT_DE_PASSE');

function getConnexion() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion a la base de donnees : " . $e->getMessage());
        }
    }
    return $pdo;
}
