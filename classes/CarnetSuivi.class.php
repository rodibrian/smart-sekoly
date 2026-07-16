<?php
/**
 * CarnetSuivi — journal de suivi pour un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class CarnetSuivi
{
    private $id_eleve;
    private $evenements = [];

    public function __construct(int $id_eleve = 0)
    {
        $this->id_eleve = $id_eleve;
    }

    public function ajouter_evenement(string $titre, string $description, string $type = 'info'): self
    {
        $this->evenements[] = [
            'titre' => nettoyer_chaine($titre),
            'description' => nettoyer_chaine($description),
            'type' => nettoyer_chaine($type),
            'date' => date('Y-m-d H:i:s'),
        ];

        return $this;
    }

    public function get_evenements(): array
    {
        return $this->evenements;
    }

    public function get_id_eleve(): int
    {
        return $this->id_eleve;
    }
}
