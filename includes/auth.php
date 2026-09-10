<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function estConnecte(): bool {
    return isset($_SESSION['user_id']);
}

function utilisateurCourant(): ?array {
    if (!estConnecte()) return null;
    return [
        'id'   => $_SESSION['user_id'],
        'nom'  => $_SESSION['user_nom'],
        'role' => $_SESSION['user_role'],
    ];
}

function exigerConnexion(): void {
    if (!estConnecte()) {
        header('Location: /login.php');
        exit;
    }
}

function exigerRole(string $role): void {
    exigerConnexion();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: /index.php');
        exit;
    }
}

function connecter(array $utilisateur): void {
    $_SESSION['user_id']   = $utilisateur['id'];
    $_SESSION['user_nom']  = $utilisateur['nom'];
    $_SESSION['user_role'] = $utilisateur['role'];
}

function deconnecter(): void {
    session_unset();
    session_destroy();
}
