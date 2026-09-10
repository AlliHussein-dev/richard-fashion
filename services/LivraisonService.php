<?php
/**
 * Service de livraison.
 * Simule la creation d'une expedition aupres d'un transporteur externe.
 */
class LivraisonService {

    public static function creerLivraison(PDO $pdo, int $commandeId, string $adresse): void {
        $dateLivraisonPrevue = date('Y-m-d', strtotime('+3 days'));

        $stmt = $pdo->prepare(
            "INSERT INTO livraisons (commande_id, transporteur, statut, date_expedition, date_livraison_prevue, adresse)
             VALUES (:cid, 'Richard Fashion Express', 'en_preparation', NULL, :date_prev, :adresse)"
        );
        $stmt->execute([
            ':cid' => $commandeId,
            ':date_prev' => $dateLivraisonPrevue,
            ':adresse' => $adresse,
        ]);
    }

    public static function mettreAJourStatut(PDO $pdo, int $livraisonId, string $statut): void {
        $expedition = ($statut === 'en_cours') ? date('Y-m-d H:i:s') : null;

        if ($expedition) {
            $stmt = $pdo->prepare("UPDATE livraisons SET statut = :s, date_expedition = :e WHERE id = :id");
            $stmt->execute([':s' => $statut, ':e' => $expedition, ':id' => $livraisonId]);
        } else {
            $stmt = $pdo->prepare("UPDATE livraisons SET statut = :s WHERE id = :id");
            $stmt->execute([':s' => $statut, ':id' => $livraisonId]);
        }

        // Si livree, la commande passe aussi a "livree"
        if ($statut === 'livree') {
            $pdo->prepare(
                "UPDATE commandes c JOIN livraisons l ON l.commande_id = c.id
                 SET c.statut = 'livree' WHERE l.id = :id"
            )->execute([':id' => $livraisonId]);
        }
    }
}
