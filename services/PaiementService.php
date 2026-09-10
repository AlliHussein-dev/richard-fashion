<?php
/**
 * Service de paiement.
 * Simule l'appel a une passerelle externe (Mobile Money, carte bancaire).
 * Dans un vrai projet, on remplacerait traiterPaiement() par un appel API
 * (ex: Lumicash, EcoCash, Stripe...) mais l'interface applicative reste la meme.
 */
class PaiementService {

    /**
     * @param string $methode   mobile_money | carte_bancaire | especes_a_la_livraison
     * @param float  $montant
     * @return array ['succes' => bool, 'reference' => string, 'statut' => string]
     */
    public static function traiterPaiement(string $methode, float $montant): array {
        $reference = 'PAY-' . strtoupper(bin2hex(random_bytes(4)));

        // Le paiement a la livraison est toujours "en_attente" jusqu'a reception
        if ($methode === 'especes_a_la_livraison') {
            return [
                'succes'    => true,
                'reference' => $reference,
                'statut'    => 'en_attente',
            ];
        }

        // Simulation d'un appel a une passerelle (mobile money / carte) : taux de reussite 95%
        $succes = (mt_rand(1, 100) <= 95);

        return [
            'succes'    => $succes,
            'reference' => $reference,
            'statut'    => $succes ? 'valide' : 'echoue',
        ];
    }

    public static function libelleMethode(string $methode): string {
        return match ($methode) {
            'mobile_money' => 'Mobile Money',
            'carte_bancaire' => 'Carte bancaire',
            'especes_a_la_livraison' => 'Especes a la livraison',
            default => $methode,
        };
    }
}
