<?php
/**
 * Rapports et Statistiques - Contrôleur
 * Génération de rapports académiques, financiers, personnalisés et officiels
 */

class RapportsController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'rapports', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $action = $this->action ?? 'index';
        
        $data = match($action) {
            'academiques' => $this->preparer_academiques(),
            'financiers' => $this->preparer_financiers(),
            'personnalises' => $this->preparer_personnalises(),
            'ministere' => $this->preparer_ministere(),
            default => $this->preparer_index(),
        };

        // Charger la vue appropriée
        require $this->templatePath() . 'rapports/' . $this->getNomVue($action) . '.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }

    private function getNomVue($action): string
    {
        return match($action) {
            'academiques' => 'academiques',
            'financiers' => 'financiers',
            'personnalises' => 'personnalises',
            'ministere' => 'ministere',
            default => 'index',
        };
    }

    /**
     * Tableau de bord des rapports disponibles
     */
    public function preparer_index()
    {
        $data = [
            'module' => 'rapports',
            'action' => 'index',
            'token_csrf' => generer_token_csrf(),
            'rapports_disponibles' => [
                [
                    'titre' => 'Rapports Académiques',
                    'description' => 'Moyennes, taux de réussite, performances par classe',
                    'action' => 'academiques',
                    'icone' => '📊'
                ],
                [
                    'titre' => 'Rapports Financiers',
                    'description' => 'Revenus, dépenses, suivi des paiements et impayés',
                    'action' => 'financiers',
                    'icone' => '💰'
                ],
                [
                    'titre' => 'Rapports Personnalisés',
                    'description' => 'Créer des rapports avec sélection personnalisée',
                    'action' => 'personnalises',
                    'icone' => '⚙️'
                ],
                [
                    'titre' => 'Rapports Officiels',
                    'description' => 'Rapports formatés pour le Ministère',
                    'action' => 'ministere',
                    'icone' => '📋'
                ],
            ],
            'export_formats' => ['PDF', 'Excel', 'CSV']
        ];
        
        return $data;
    }

    /**
     * Rapports Académiques - Moyennes, taux de réussite, performances
     */
    public function preparer_academiques()
    {
        // Récupérer les données des élèves et calculs en session
        $eleves = $_SESSION['eleves'] ?? [];
        
        // Données de test pour prototype
        $rapports = [
            [
                'classe' => '6ème A',
                'total_eleves' => count($eleves) > 0 ? count($eleves) : 45,
                'moyenne_generale' => 12.5,
                'taux_reussite' => 92.0,
                'meilleure_moyenne' => 18.5,
                'pire_moyenne' => 6.0,
                'eleves_en_difficulte' => 3
            ],
            [
                'classe' => '5ème B',
                'total_eleves' => count($eleves) > 0 ? count($eleves) : 42,
                'moyenne_generale' => 13.2,
                'taux_reussite' => 95.0,
                'meilleure_moyenne' => 19.0,
                'pire_moyenne' => 7.5,
                'eleves_en_difficulte' => 2
            ],
            [
                'classe' => '4ème C',
                'total_eleves' => count($eleves) > 0 ? count($eleves) : 40,
                'moyenne_generale' => 11.8,
                'taux_reussite' => 88.0,
                'meilleure_moyenne' => 17.5,
                'pire_moyenne' => 5.0,
                'eleves_en_difficulte' => 5
            ],
            [
                'classe' => '3ème D',
                'total_eleves' => count($eleves) > 0 ? count($eleves) : 38,
                'moyenne_generale' => 13.5,
                'taux_reussite' => 97.0,
                'meilleure_moyenne' => 19.5,
                'pire_moyenne' => 8.0,
                'eleves_en_difficulte' => 1
            ]
        ];
        
        // Trier par taux de réussite décroissant
        usort($rapports, function($a, $b) {
            return $b['taux_reussite'] <=> $a['taux_reussite'];
        });
        
        $data = [
            'module' => 'rapports',
            'action' => 'academiques',
            'token_csrf' => generer_token_csrf(),
            'rapports' => $rapports,
            'periode' => 'Trimestre 2 (2025-2026)',
            'total_classes' => count($rapports),
            'moyenne_etablissement' => 12.75,
            'taux_reussite_etablissement' => 93.0
        ];
        
        return $data;
    }

    /**
     * Rapports Financiers - Revenus, dépenses, paiements
     */
    public function preparer_financiers()
    {
        // Récupérer les données financières en session
        $factures = $_SESSION['finances']['factures'] ?? [];
        $paiements = $_SESSION['finances']['paiements'] ?? [];
        
        // Calculs financiers
        $total_factures = 0;
        $total_paiements = 0;
        
        foreach ($factures as $facture) {
            $total_factures += $facture['montant'] ?? 0;
        }
        
        foreach ($paiements as $paiement) {
            $total_paiements += $paiement['montant'] ?? 0;
        }
        
        $montant_impaye = $total_factures - $total_paiements;
        $taux_recouvrement = $total_factures > 0 ? ($total_paiements / $total_factures) * 100 : 0;
        
        // Rapports mensuels
        $mois = ['Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 
                 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'];
        
        $rapports_mensuels = [];
        foreach ($mois as $index => $nom_mois) {
            $rapports_mensuels[] = [
                'mois' => $nom_mois,
                'factures_emises' => rand(10, 30),
                'montant_factures' => rand(500000, 1500000),
                'paiements_recus' => rand(5, 25),
                'montant_paiements' => rand(300000, 1200000),
                'taux_recouvrement' => rand(80, 100)
            ];
        }
        
        $data = [
            'module' => 'rapports',
            'action' => 'financiers',
            'token_csrf' => generer_token_csrf(),
            'total_factures' => $total_factures,
            'total_paiements' => $total_paiements,
            'montant_impaye' => $montant_impaye,
            'taux_recouvrement' => round($taux_recouvrement, 2),
            'rapports_mensuels' => $rapports_mensuels,
            'nombre_factures' => count($factures),
            'nombre_paiements' => count($paiements),
            'nombre_impaye' => count(array_filter($factures, function($f) { return ($f['montant'] ?? 0) > ($f['paye'] ?? 0); }))
        ];
        
        return $data;
    }

    /**
     * Rapports Personnalisés - Créer un rapport avec sélection personnalisée
     */
    public function preparer_personnalises()
    {
        // Gérer la soumission du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_personnalise();
        }
        
        $rapports_generes = $_SESSION['rapports']['personnalises'] ?? [];
        
        $data = [
            'module' => 'rapports',
            'action' => 'personnalises',
            'token_csrf' => generer_token_csrf(),
            'types_rapports' => [
                'eleves' => 'Élèves (liste, absences, performances)',
                'finances' => 'Finances (factures, paiements, dépenses)',
                'ressources' => 'Ressources Humaines (salaires, présences)',
                'discipline' => 'Discipline (absences, retards, sanctions)',
                'emploi_temps' => 'Emploi du temps (charges, conflits)',
            ],
            'formats_export' => [
                'pdf' => 'PDF (imprimable)',
                'excel' => 'Excel (données)',
                'csv' => 'CSV (import)',
            ],
            'periodes' => [
                'jour' => 'Aujourd\'hui',
                'semaine' => 'Semaine en cours',
                'mois' => 'Mois en cours',
                'trimestre' => 'Trimestre en cours',
                'annee' => 'Année scolaire',
                'personnalisee' => 'Période personnalisée',
            ],
            'rapports_generes' => $rapports_generes,
            'total_rapports_generes' => count($rapports_generes)
        ];
        
        return $data;
    }

    /**
     * Traiter la soumission d'un rapport personnalisé
     */
    private function traiter_post_personnalise()
    {
        $type = $_POST['type_rapport'] ?? '';
        $format = $_POST['format_export'] ?? 'pdf';
        $periode = $_POST['periode'] ?? '';
        
        if (!$type || !$format || !$periode) {
            return;
        }
        
        $rapport = [
            'id' => 'RAP-' . date('YmdHis'),
            'type' => $type,
            'format' => $format,
            'periode' => $periode,
            'date_creation' => date('d/m/Y H:i:s'),
            'statut' => 'Généré',
            'taille' => rand(100, 5000) . ' KB'
        ];
        
        if (!isset($_SESSION['rapports']['personnalises'])) {
            $_SESSION['rapports']['personnalises'] = [];
        }
        
        $_SESSION['rapports']['personnalises'][] = $rapport;
    }

    /**
     * Rapports Officiels - Rapports formatés pour le Ministère
     */
    public function preparer_ministere()
    {
        $eleves = $_SESSION['eleves'] ?? [];
        $enseignants = $_SESSION['enseignants'] ?? [];
        
        $rapports = [
            [
                'type' => 'Rapport d\'Effectif',
                'description' => 'Nombre d\'élèves par classe, par niveau',
                'total_eleves' => count($eleves) > 0 ? count($eleves) : 432,
                'total_enseignants' => count($enseignants) > 0 ? count($enseignants) : 35,
                'ratio' => '12.3 élèves/enseignant',
                'format' => 'Ministère Format',
                'date_derniere_generation' => '2026-06-15'
            ],
            [
                'type' => 'Rapport Pédagogique',
                'description' => 'Moyennes, taux de réussite, résultats aux examens',
                'taux_reussite_moyen' => 92.5,
                'nombre_classes' => 12,
                'nombre_matieres' => 15,
                'format' => 'Ministère Format',
                'date_derniere_generation' => '2026-06-20'
            ],
            [
                'type' => 'Rapport Administratif',
                'description' => 'Données administratives complètes',
                'nombre_inscrits' => count($eleves) > 0 ? count($eleves) : 432,
                'nombre_classes' => 12,
                'nombre_personnels' => count($enseignants) > 0 ? count($enseignants) : 35,
                'format' => 'Ministère Format',
                'date_derniere_generation' => '2026-06-10'
            ],
            [
                'type' => 'Rapport Financier',
                'description' => 'Budget, revenus, dépenses, équilibre financier',
                'budget_annuel' => '485 000 000 FCFA',
                'revenus_collectes' => '450 000 000 FCFA',
                'depenses' => '420 000 000 FCFA',
                'format' => 'Ministère Format',
                'date_derniere_generation' => '2026-06-25'
            ],
            [
                'type' => 'Rapport Sanitaire',
                'description' => 'Données de santé, vaccination, suivi médical',
                'eleves_visites' => count($eleves) > 0 ? count($eleves) : 432,
                'taux_vaccination' => 98.5,
                'visites_medicales' => 'Complètes',
                'format' => 'Ministère Format',
                'date_derniere_generation' => '2026-06-18'
            ]
        ];
        
        $data = [
            'module' => 'rapports',
            'action' => 'ministere',
            'token_csrf' => generer_token_csrf(),
            'rapports' => $rapports,
            'etablissement' => 'Smart-Sekoly',
            'annee_scolaire' => '2025-2026',
            'derniere_generation' => '2026-06-25',
            'total_rapports' => count($rapports)
        ];
        
        return $data;
    }

    /**
     * Afficher le tableau de bord des rapports
     */
    public function afficher_accueil()
    {
        return $this->preparer_index();
    }
}

?>
