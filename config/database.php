<?php
/**
 * Connexion a la base de donnees (PDO) - InfinityFree
 * Compte : nyamalibu.wuaze.com
 */

define('DB_HOST', 'sql103.infinityfree.com');
define('DB_NAME', 'if0_42882239_richard');
define('DB_USER', 'if0_42882239');
define('DB_PASS', 'KxSEHEBm80Uh5');

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
