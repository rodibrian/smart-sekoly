<?php

class VersionDocument
{
    private int $id;
    private int $document_id;
    private string $auteur;
    private string $commentaire;
    private string $contenu;
    private string $date_version;

    public function __construct(array $donnees = [])
    {
        $this->id = (int) ($donnees['id'] ?? 0);
        $this->document_id = (int) ($donnees['document_id'] ?? 0);
        $this->auteur = (string) ($donnees['auteur'] ?? '');
        $this->commentaire = (string) ($donnees['commentaire'] ?? '');
        $this->contenu = (string) ($donnees['contenu'] ?? '');
        $this->date_version = (string) ($donnees['date_version'] ?? date('d/m/Y H:i'));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'document_id' => $this->document_id,
            'auteur' => $this->auteur,
            'commentaire' => $this->commentaire,
            'contenu' => $this->contenu,
            'date_version' => $this->date_version,
        ];
    }
}
