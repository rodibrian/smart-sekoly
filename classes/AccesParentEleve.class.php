<?php
/**
 * Classe AccesParentEleve
 * Représente les accès parents/élèves générés pour consultation externe.
 */

class AccesParentEleve
{
    public int $id;
    public string $parent_nom;
    public string $parent_type;
    public string $code;
    public array $enfants;
    public string $statut;
    public string $date_creation;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? random_int(1000, 9999);
        $this->parent_nom = $data['parent_nom'] ?? 'Parent inconnu';
        $this->parent_type = $data['parent_type'] ?? 'Parent';
        $this->code = $data['code'] ?? strtoupper(substr(sha1((string) microtime(true)), 0, 10));
        $this->enfants = $data['enfants'] ?? [];
        $this->statut = $data['statut'] ?? 'actif';
        $this->date_creation = $data['date_creation'] ?? date('d/m/Y H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parent_nom' => $this->parent_nom,
            'parent_type' => $this->parent_type,
            'code' => $this->code,
            'enfants' => $this->enfants,
            'statut' => $this->statut,
            'date_creation' => $this->date_creation,
        ];
    }
}
