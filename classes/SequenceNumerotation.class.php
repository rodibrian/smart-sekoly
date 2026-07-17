<?php

class SequenceNumerotation
{
    private $prefixe;
    private $longueur;
    private $valeurActuelle;

    public function __construct(array $donnees = [])
    {
        $this->prefixe = (string) ($donnees['prefixe'] ?? '');
        $this->longueur = max(1, (int) ($donnees['longueur'] ?? 4));
        $this->valeurActuelle = max(1, (int) ($donnees['valeur_actuelle'] ?? 1));
    }

    public function prochain(): string
    {
        $numero = str_pad((string) $this->valeurActuelle, $this->longueur, '0', STR_PAD_LEFT);
        $this->valeurActuelle++;
        return $this->prefixe . $numero;
    }

    public function getPrefixe(): string
    {
        return $this->prefixe;
    }

    /**
     * Static helper to fetch and increment the sequence for a document type and year (id_annee).
     * Returns ['numero' => int, 'formatte' => string]
     */
    public static function getNext(string $type_document, int $id_annee, ?string $format_override = null): array
    {
        $pdo = get_connexion_base_donnees();
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('Base de données indisponible');
        }

        $param = ParametrageEtablissement::findCurrent();
        if ($param === null || $param->get_id_parametrage() === null) {
            throw new RuntimeException('Paramétrage établissement introuvable.');
        }

        try {
            $pdo->beginTransaction();

            $select = $pdo->prepare('SELECT id_sequence, dernier_numero, format FROM sequence_numerotation WHERE type_document = :type AND id_annee = :annee FOR UPDATE');
            $select->execute([':type' => $type_document, ':annee' => $id_annee]);
            $row = $select->fetch(PDO::FETCH_ASSOC);

            if ($row !== false) {
                $next = (int) $row['dernier_numero'] + 1;
                $update = $pdo->prepare('UPDATE sequence_numerotation SET dernier_numero = :next WHERE id_sequence = :id');
                $update->execute([':next' => $next, ':id' => $row['id_sequence']]);
                $usedFormat = $row['format'];
            } else {
                $next = 1;
                $usedFormat = $format_override ?? $param->get_format_matricule();
                $insert = $pdo->prepare('INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, dernier_numero, format) VALUES (:id_param, :type, :annee, :dernier, :format)');
                $insert->execute([
                    ':id_param' => $param->get_id_parametrage(),
                    ':type' => $type_document,
                    ':annee' => $id_annee,
                    ':dernier' => $next,
                    ':format' => $usedFormat,
                ]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        $anneeLabel = null;
        $stmt = $pdo->prepare('SELECT libelle FROM annee_scolaire WHERE id_annee = :id');
        $stmt->execute([':id' => $id_annee]);
        $rowAn = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rowAn !== false) {
            $anneeLabel = $rowAn['libelle'];
        }

        $formatted = self::formatSequence($usedFormat, $param, $next, $anneeLabel ?? $param->get_annee_courante());

        return ['numero' => $next, 'formatte' => $formatted];
    }

    private static function formatSequence(string $format, ParametrageEtablissement $param, int $sequence, string $annee): string
    {
        $numero = str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
        $prefix = $param->get_prefixe_matricule();

        return str_replace(
            ['{PREFIXE}', '{ANNEE}', '{NUMERO_SEQUENTIEL}'],
            [strtoupper($prefix), $annee, $numero],
            $format
        );
    }
}
