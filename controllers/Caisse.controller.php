<?php
/**
 * Contrôleur de caisse.
 */
class CaisseController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'caisses', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'caisses/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'caisses/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'caisses/liste.view.php';
    }

    private function preparer_formulaire(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    private function preparer_liste(): array
    {
        $caisses = [
            new Caisse(['id_caisse' => 1, 'date_caisse' => '2026-10-01', 'fond_de_caisse' => 150000.00]),
            new Caisse(['id_caisse' => 2, 'date_caisse' => '2026-10-02', 'fond_de_caisse' => 125000.00]),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'caisses' => array_map(function (Caisse $caisse): array {
                return [
                    'id' => $caisse->get_id_caisse(),
                    'date' => $caisse->get_date_caisse(),
                    'fond' => number_format($caisse->get_fond_de_caisse(), 0, ',', ' '),
                ];
            }, $caisses),
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $caisse = new Caisse(['id_caisse' => $id, 'date_caisse' => '2026-10-01', 'fond_de_caisse' => 150000.00]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'caisse' => [
                'id' => $caisse->get_id_caisse(),
                'date' => $caisse->get_date_caisse(),
                'fond' => number_format($caisse->get_fond_de_caisse(), 0, ',', ' '),
            ],
        ];
    }
}
