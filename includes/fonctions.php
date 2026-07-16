<?php
/**
 * Fonctions utilitaires communes pour Smart-Sekoly.
 *
 * @package Smart-Sekoly
 * @subpackage Includes
 */

/**
 * Nettoie une chaîne de caractères pour l'affichage et la validation.
 *
 * @param mixed $valeur
 * @return string
 */
function nettoyer_chaine($valeur): string
{
    if (!is_scalar($valeur)) {
        return '';
    }

    return trim(strip_tags((string) $valeur));
}

/**
 * Valide une adresse email simple.
 *
 * @param string $email
 * @return array{valide: bool, erreurs: array<string, string>, donnees: array}
 */
function valider_email(string $email): array
{
    $donnees_nettoyees = nettoyer_chaine($email);
    $erreurs = [];

    if ($donnees_nettoyees === '' || filter_var($donnees_nettoyees, FILTER_VALIDATE_EMAIL) === false) {
        $erreurs['email'] = 'Adresse email invalide.';
    }

    return [
        'valide' => empty($erreurs),
        'erreurs' => $erreurs,
        'donnees' => ['email' => $donnees_nettoyees],
    ];
}

/**
 * Formate un montant pour l'affichage utilisateur.
 *
 * @param float|int|string $montant
 * @return string
 */
function format_montant($montant): string
{
    return number_format((float) $montant, 2, ',', ' ');
}

/**
 * Formate une date au format français.
 *
 * @param string|null $date
 * @param string $format
 * @return string
 */
function format_date_fr($date, string $format = 'd/m/Y H:i'): string
{
    if ($date === null || $date === '') {
        return '';
    }

    try {
        $date_objet = new DateTimeImmutable((string) $date);
        return $date_objet->format($format);
    } catch (Exception $exception) {
        return '';
    }
}

/**
 * Génère ou récupère un jeton CSRF stocké en session.
 *
 * @return string
 */
function generer_token_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un jeton CSRF.
 *
 * @param string $token
 * @return bool
 */
function verifier_token_csrf(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Génère un identifiant auto-incrémenté simple pour une collection stockée en session.
 *
 * @param array $collection
 * @param string $cle_id
 * @return int
 */
function generer_identifiant(array $collection, string $cle_id): int
{
    $max = 0;

    foreach ($collection as $element) {
        $identifiant = $element[$cle_id] ?? 0;
        if (is_numeric($identifiant) && (int) $identifiant > $max) {
            $max = (int) $identifiant;
        }
    }

    return $max + 1;
}

/**
 * Echappe une valeur pour l'affichage HTML.
 *
 * @param mixed $valeur
 * @return string
 */
function e($valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un matricule au format EL-ANNEE-XXXX.
 *
 * @param string $prefixe
 * @param int|null $annee
 * @return string
 */
function generer_matricule(string $prefixe = 'EL', ?int $annee = null): string
{
    $annee_utilisee = $annee ?? (int) date('Y');
    $suffixe = substr(strtoupper(uniqid('', false)), 0, 6);

    return strtoupper($prefixe) . '-' . $annee_utilisee . '-' . $suffixe;
}
