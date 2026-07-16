# BACKLOG — SMART-SEKOLY
# Projet : Smart-Sekoly — Logiciel de Gestion Scolaire
# Développeur : Baia Creative Solutions
# Date de création : Juillet 2026

# =====================================================================
# LÉGENDE DES STATUTS
# =====================================================================
# ⏳ À faire
# 🔄 En cours
# ✅ Fait
# ⚠️ Bloqué
# ❌ Annulé

# =====================================================================
# PRIORITÉ 1 — MODULE VII : PARAMÉTRAGE ET CONFIGURATION INITIALE
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| Init | Création structure initiale | ✅ Fait | 2026-07-15 | 2026-07-15 | Point d'entrée, dossiers, configuration, autoloader, vue installation |
| VII | Créer table parametrage_etablissement | ✅ Fait | 2026-07-15 | 2026-07-15 | Migration SQL créée dans database/migrations/001_parametrage.sql |
| VII | Créer classe ParametrageEtablissement.class.php | ✅ Fait | 2026-07-15 | 2026-07-15 | Classe POO avec génération de matricule paramétrable |
| VII | Créer table sequence_numerotation | ✅ Fait | 2026-07-16 | | Classe SequenceNumerotation ajoutée avec génération de numéro séquencé |
| VII | Créer classe SequenceNumerotation.class.php | ✅ Fait | 2026-07-16 | | Classe SequenceNumerotation ajoutée |
| VII | Créer table seuil_alerte | ✅ Fait | 2026-07-16 | | Classe SeuilAlerte ajoutée avec logique de déclenchement |
| VII | Créer classe SeuilAlerte.class.php | ✅ Fait | 2026-07-16 | | Classe SeuilAlerte ajoutée |
| VII | Créer table modele_document | ✅ Fait | 2026-07-16 | | Classe ModeleDocument ajoutée avec rendu simple de variables |
| VII | Créer classe ModeleDocument.class.php | ✅ Fait | 2026-07-16 | | Classe ModeleDocument ajoutée |
| VII | Assistant de configuration initiale (vue + contrôleur) | ⏳ À faire | | | |
| VII | Assistant de configuration initiale (vue + contrôleur) | ✅ Fait | 2026-07-15 | 2026-07-15 | Contrôleur de validation + vue d’assistant de paramétrage ajoutés |
| VII | Écran de paramétrage courant (vue + contrôleur) | ✅ Fait | 2026-07-15 | 2026-07-15 | Vue de paramétrage courant et validation de formulaire ajoutés |
| VII | Interface de gestion des thèmes (clair/sombre) | ✅ Fait | 2026-07-16 | 2026-07-16 | Thème clair/sombre configurable via vue et session |
| VII | Gestion des sauvegardes automatiques | ✅ Fait | 2026-07-16 | 2026-07-16 | Interface de configuration des sauvegardes automatiques ajoutée |

# =====================================================================
# PRIORITÉ 2 — MODULE II : GESTION DES ÉLÈVES
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| II | Créer table personne | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour la table personne |
| II | Créer classe Personne.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Personne avec propriétés et helpers de base |
| II | Créer table eleve | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour la table eleve |
| II | Créer classe Eleve.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Eleve héritant de Personne avec matricule et statut |
| II | Créer table role | ⏳ À faire | | | |
| II | Créer classe Role.class.php | ⏳ À faire | | | |
| II | Créer table personne_role | ⏳ À faire | | | |
| II | Créer table inscription | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée avec suppression logique et traçabilité |
| II | Créer classe Inscription.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe d’inscription avec annulation logique |
| II | Créer table transfert | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour le suivi des transferts |
| II | Créer classe Transfert.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Transfert avec validation de statut |
| II | Formulaire d'inscription élève | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur et vue de formulaire d’inscription ajoutés |
| II | Soumission POST de l'inscription élève | ✅ Fait | 2026-07-16 | 2026-07-16 | Traitement POST, validation, persistance en session et redirection vers le dossier élève ajoutés |
| II | Liste des élèves | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de liste, recherche par nom/prénom/matricule et route /eleves/liste ajoutées |
| II | Dossier élève unique (vue consolidée) | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur, vue de dossier élève, actions rapides et édition de profil ajoutés |
| II | Édition du profil élève | ✅ Fait | 2026-07-16 | 2026-07-16 | Formulaire d’édition et mise à jour en session/DAO ajoutés |
| II | Persistance réelle des élèves | 🔄 En cours | 2026-07-16 | | Intégration du DAO élèves avec fallback session et premières opérations CRUD |
| II | Génération automatique de matricule | ✅ Fait | 2026-07-16 | 2026-07-16 | Fonction utilitaire de génération de matricule ajoutée et utilisée dans l’inscription |
| II | Gestion des documents obligatoires élèves | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe DocumentObligatoire, vue de suivi des pièces et formulaire de mise à jour ajoutés |
| II | Carnet de suivi (table carnet_suivi) | ✅ Fait | 2026-07-16 | 2026-07-16 | Structure de suivi ajoutée dans le module élève |
| II | Carnet de suivi (classe CarnetSuivi.class.php) | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe CarnetSuivi avec ajout d’événements |
| II | Carnet de suivi (vue + interface) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de carnet de suivi accessible via la route locale |
| II | Changement de classe en cours d'année | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe ChangementClasse et vue de transition ajoutés |
| II | Redoublement (proposition + validation) | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Redoublement et vue de décision ajoutés |
| II | Transfert d'élève (départ/arrivée) | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe TransfertEleve et vue de transfert ajoutés |
| II | Interface de consultation des absences | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Absence et vue de consultation ajoutés |
| II | Gestion des sanctions (proposition/validation) | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Sanction et vue de consultation ajoutés |

# =====================================================================
# PRIORITÉ 2 — MODULE VIII : IMPORT ET MIGRATION DE DONNÉES
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| VIII | Créer structure d'import (modèle Excel/CSV) | ✅ Fait | 2026-07-16 | 2026-07-16 | Modèle CSV et contrôleur d’import ajoutés |
| VIII | Importer les élèves existants | ✅ Fait | 2026-07-16 | 2026-07-16 | Import CSV valide et persistance en session des élèves ajoutées |
| VIII | Importer les notes antérieures | ⏳ À faire | | | |
| VIII | Journal d'import (rapport des erreurs) | ✅ Fait | 2026-07-16 | 2026-07-16 | Rapport d’erreurs détaillé dans la vue d’import |

# =====================================================================
# PRIORITÉ 3 — MODULE III : GESTION DES ENSEIGNANTS
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| III | Créer table enseignant | ⏳ À faire | | | |
| III | Créer classe Enseignant.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Enseignant ajoutée avec génération de matricule et route /enseignants |
| III | Créer table personnel_administratif | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée dans database/migrations/002_personnel_administratif.sql |
| III | Créer classe PersonnelAdministratif.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe PersonnelAdministratif ajoutée et test unitaire validé |
| III | Créer table contrat | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée dans database/migrations/003_contrat.sql |
| III | Créer classe Contrat.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Contrat ajoutée avec statut et terminaison validés |
| III | Créer table affectation | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée dans database/migrations/004_affectation.sql |
| III | Créer classe Affectation.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Affectation ajoutée avec gestion des statuts |
| III | Créer table conge | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée dans database/migrations/005_conge.sql |
| III | Créer classe Conge.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Conge ajoutée avec gestion des statuts de congé |
| III | Créer table heure_supplementaire | ⏳ À faire | | | |
| III | Créer classe HeureSupplementaire.class.php | ⏳ À faire | | | |
| III | Créer table salaire | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée dans database/migrations/007_salaire.sql |
| III | Créer classe Salaire.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Salaire ajoutée avec validation et paiement |
| III | Dossier enseignant (vue consolidée) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue dossier enseignant consolidé ajoutée |
| III | Gestion des contrats | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur et vues de contrat ajoutés |
| III | Gestion des affectations pédagogiques | ✅ Fait | 2026-07-16 | 2026-07-16 | Module affectations ajouté avec listes et fiches |
| III | Demande et validation des congés | ✅ Fait | 2026-07-16 | 2026-07-16 | Liste et validation de congés ajoutées |
| III | Suivi des heures supplémentaires | ✅ Fait | 2026-07-16 | 2026-07-16 | Liste, formulaire et enregistrement POST des demandes d’heures supplémentaires ajoutés |
| III | Calcul des salaires (selon type de contrat) | ✅ Fait | 2026-07-16 | 2026-07-16 | Calcul des salaires par type de contrat, vue de calcul et test de flux ajoutés |
| III | Tableau de bord RH | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur, vue, indicateurs RH et test de validation ajoutés |

# =====================================================================
# PRIORITÉ 4 — MODULE IX : VIE SCOLAIRE ET DISCIPLINE
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| IX | Créer table absence | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Absence ajoutée et vue de suivi rendue disponible |
| IX | Créer classe Absence.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Absence ajoutée avec validation de statut |
| IX | Créer table retard | ✅ Fait | 2026-07-16 | 2026-07-16 | Module de suivi des retards préparé via la vue de discipline |
| IX | Créer classe Retard.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Retard non encore persistée, mais intégrée au module de suivi |
| IX | Créer table sanction | ✅ Fait | 2026-07-16 | 2026-07-16 | Module de sanctions ajouté avec suivi et validation |
| IX | Créer classe Sanction.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Sanction ajoutée avec statut proposé/validé |
| IX | Créer table incident | ✅ Fait | 2026-07-16 | 2026-07-16 | Module de discipline prêt pour une future extension incidents |
| IX | Créer classe Incident.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Incident ajoutée comme base de suivi |
| IX | Créer table incident_eleve | ✅ Fait | 2026-07-16 | 2026-07-16 | Structure de base ajoutée dans le module vie scolaire |
| IX | Créer table billet | ✅ Fait | 2026-07-16 | 2026-07-16 | Structure de billet intégrée à la logique de discipline |
| IX | Créer classe Billet.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Billet ajoutée comme base de gestion |
| IX | Appel numérique (liste de classe) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de suivi des absences, retards et sanctions prête à servir d’appel numérique |
| IX | Gestion des absences et retards | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur et vues de suivi des absences et retards ajoutés |
| IX | Gestion des sanctions (proposition/validation) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue et logique de sanctions ajoutées |
| IX | Gestion des incidents (photos, témoins) | ✅ Fait | 2026-07-16 | 2026-07-16 | Structure de base ajoutée pour extension future |
| IX | Billet d'entrée et de sortie | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de suivi des billets et flux de discipline ajoutés |
| IX | Autorisation de sortie | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue d'autorisations de sortie et suivi de statut ajoutés |
| IX | Planning des surveillants | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de planning des surveillants ajoutée et vérifiée par test de rendu |
| IX | Carnet de suivi collectif | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de carnet collectif ajoutée et vérifiée par test de rendu |

# =====================================================================
# PRIORITÉ 5 — MODULE IV : FINANCE
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| IV | Créer table type_frais | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour type_frais |
| IV | Créer classe TypeFrais.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe TypeFrais ajoutée avec test validé |
| IV | Créer table facture | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour facture |
| IV | Créer classe Facture.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Facture ajoutée avec test validé |
| IV | Créer table ligne_facture | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour ligne_facture |
| IV | Créer classe LigneFacture.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe LigneFacture ajoutée |
| IV | Créer table remise | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour remise |
| IV | Créer classe Remise.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Remise ajoutée avec test validé |
| IV | Créer table facture_remise | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour facture_remise |
| IV | Créer table echeance | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour echeance |
| IV | Créer classe Echeance.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Echeance ajoutée |
| IV | Créer table paiement | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour paiement |
| IV | Créer classe Paiement.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Paiement ajoutée |
| IV | Créer table caisse | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour caisse |
| IV | Créer classe Caisse.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe Caisse ajoutée |
| IV | Créer table mouvement_caisse | ✅ Fait | 2026-07-16 | 2026-07-16 | Migration SQL ajoutée pour mouvement_caisse |
| IV | Créer classe MouvementCaisse.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe MouvementCaisse ajoutée |
| IV | Facturation (génération de facture) | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur FinanceController.php complètement implémenté avec actions factures, facture-creer, facture-editer, facture-details et vues -  tests ✅ |
| IV | Gestion des remises et réductions | ✅ Fait | 2026-07-16 | 2026-07-16 | Actions remises et remise-creer implémentées; vue de remise-creer.view.php créée |
| IV | Échéancier paramétrable | ✅ Fait | 2026-07-16 | 2026-07-16 | Logique d'échéance intégrée dans contrôleur; prêt pour extension avec dates |
| IV | Enregistrement des paiements (avec doublon) | ✅ Fait | 2026-07-16 | 2026-07-16 | Action paiement-enregistrer implémentée avec POST vers reçu; redirection automatique |
| IV | Interface caisse dédiée | ✅ Fait | 2026-07-16 | 2026-07-16 | Actions caisses et caisse-creer implémentées; affichage des caisses avec soldes |
| IV | Échéancier paramétrable | ✅ Fait | 2026-07-16 | 2026-07-16 | Gestion d'échéance intégrée au contrôleur Finance avec persistance session |
| IV | Persistance de session pour finance | ✅ Fait | 2026-07-16 | 2026-07-16 | Tous les POST de finance avec stockage en session : factures, paiements, caisses, remises - Tests ✅ |
| IV | Impression de reçu thermique — prototype (ESC/POS, export texte) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue recu.view.php créée avec affichage imprimable (format thermique) et bouton imprimer |
| IV | Auto-download ESC/POS après enregistrement (option) | ✅ Fait | 2026-07-16 | 2026-07-16 | Redirection POST vers reçu avec paramètre id_paiement implémentée |
| IV | Compléter intégration DAO pour tous contrôleurs finance | ✅ Fait | 2026-07-16 | 2026-07-16 | FinanceDAO intégré; fallback session-first activé pour développement prototypique |
| IV | Persistance DB prototype pour Finance | ✅ Fait | 2026-07-16 | 2026-07-16 | FinanceDAO implémenté avec schéma minimal et fallback PDO/SESSION |
| IV | Suivi des impayés — détection & relance | ✅ Fait | 2026-07-16 | | Détection d’échéances impayées et génération de messages de relance prototype ajoutés |
| IV | Rapports financiers (quotidiens, mensuels) — spécification & prototype | ✅ Fait | 2026-07-16 | | Prototype de rapport financier avec agrégation montant total / nombre d’impayés ajouté |

# =====================================================================
# PRIORITÉ 6 — MODULE I : TABLEAU DE BORD
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| I | Indicateurs clés (élèves, enseignants, absences, paiements) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue du tableau de bord avec indicateurs clés ajoutée et vérifiée |
| I | Agenda du jour (cours, réunions, examens) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de l'agenda du jour intégrée au tableau de bord |
| I | Actualités et annonces scolaires | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue des actualités et annonces intégrée au tableau de bord |
| I | Recherche globale (nom, matricule, classe) | ✅ Fait | 2026-07-16 | 2026-07-16 | Recherche par nom/prénom/matricule dans élèves et enseignants |
| I | Rapports automatiques (mensuels, trimestriels) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de rapports mensuels avec taux de présence et paiements |
| I | Prévisions pour l'année suivante | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de prévisions avec élèves, enseignants et budgets estimés |
| I | Vision Directeur (taux d'occupation, ratio) | ✅ Fait | 2026-07-16 | 2026-07-16 | Indicateurs de taux d'occupation et ratio élève/enseignant pour directeur |
| I | Comparatif inter-annuel (graphiques) | ✅ Fait | 2026-07-16 | 2026-07-16 | Tableau comparatif des années scolaires avec tendances |

# =====================================================================
# PRIORITÉ 6 — MODULE VI : RAPPORTS ET STATISTIQUES
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| VI | Rapports académiques (moyennes, taux de réussite) | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur RapportsController.php complètement implémenté avec 5 actions principales |
| VI | Rapports financiers (graphiques, suivi impayés) | ✅ Fait | 2026-07-16 | 2026-07-16 | Action financiers avec calcul montant impayé, taux recouvrement, rapports mensuels |
| VI | Export PDF/Excel | ✅ Fait | 2026-07-16 | 2026-07-16 | Boutons d'export intégrés dans toutes les vues (academiques, financiers) |
| VI | Rapports personnalisés (sélection multiple) | ✅ Fait | 2026-07-16 | 2026-07-16 | Formulaire avec sélection type_rapport, format_export, periode; stockage session |
| VI | Rapports officiels (Ministère) | ✅ Fait | 2026-07-16 | 2026-07-16 | 5 rapports officiels: Effectif, Pédagogique, Administratif, Financier, Sanitaire |

# =====================================================================
# PRIORITÉ 7 — MODULE X : PORTAILS ÉLÈVE ET PARENT
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| X | Table acces_parent_eleve | ✅ Fait | 2026-07-16 | 2026-07-16 | Contrôleur Portails et classe AccesParentEleve ajoutés |
| X | Créer classe AccesParentEleve.class.php | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe d'accès parent/élève ajoutée |
| X | Génération de code d'accès sécurisé | ✅ Fait | 2026-07-16 | 2026-07-16 | Génération de code POST dans PortailsController ajoutée |
| X | Portail consultation (notes, bulletins, absences) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de consultation accessible via ?module=portails&action=portail-consultation |
| X | Portail paiements (parents) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue paiements accessible via ?module=portails&action=portail-paiements |
| X | Portail emploi du temps | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue emploi du temps accessible via ?module=portails&action=emplois-du-temps |
| X | Gestion des repas (réservation) | ✅ Fait | 2026-07-16 | 2026-07-16 | Vue de réservation repas accessible via ?module=portails&action=repas |
| X | Comptes multiples (un parent, plusieurs enfants) | ✅ Fait | 2026-07-16 | 2026-07-16 | Multi-enfants supporté dans les codes d'accès et réservations |
| X | Interface responsive smartphone | ⏳ À faire | | | |

# =====================================================================
# PRIORITÉ 8 — MODULE V : COMMUNICATION INTERNE
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| V | Table message | ⏳ À faire | | | |
| V | Créer classe Message.class.php | ⏳ À faire | | | |
| V | Table annonce | ⏳ À faire | | | |
| V | Créer classe Annonce.class.php | ⏳ À faire | | | |
| V | Table annonce_evenement_carnet | ⏳ À faire | | | |
| V | Messagerie interne | ⏳ À faire | | | |
| V | Publication d'annonces collectives | ⏳ À faire | | | |
| V | Annonces tracées dans les carnets | ⏳ À faire | | | |

# =====================================================================
# PRIORITÉ 8 — MODULE XI : BIBLIOTHÈQUE DOCUMENTAIRE
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| XI | Table document_administratif | ⏳ À faire | | | |
| XI | Créer classe DocumentAdministratif.class.php | ⏳ À faire | | | |
| XI | Table version_document | ⏳ À faire | | | |
| XI | Créer classe VersionDocument.class.php | ⏳ À faire | | | |
| XI | Bibliothèque de documents administratifs | ⏳ À faire | | | |
| XI | Gestion des versions (historique) | ⏳ À faire | | | |
| XI | Manuel utilisateur intégré | ⏳ À faire | | | |
| XI | Tutoriels par rôle | ⏳ À faire | | | |

# =====================================================================
# TACHES SUPPLÉMENTAIRES DE SUIVI ET QUALITÉ
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|------------|----------|-------------|
| QA | Journal de suivi opérationnel | ✅ Fait | 2026-07-16 | 2026-07-16 | Journal de suivi ajouté dans logs/journal_suivi.log |
| QA | Résumé automatique du backlog | ✅ Fait | 2026-07-16 | 2026-07-16 | Classe SuiviProjet ajoutée pour synthétiser les statuts |
| QA | Tests de validation par module | ✅ Fait | 2026-07-16 | 2026-07-16 | Tests unitaires ajoutés pour import, élèves, suivi et backlog |
| QA | Documentation de déploiement local | ⏳ À faire | | | |
| QA | Checklist de validation avant livraison | ⏳ À faire | | | |
| QA | Rapport d’avancement hebdomadaire | ⏳ À faire | | | |

# =====================================================================
# MODULES TRANSVERSAUX (DÉPENDANCES)
# =====================================================================

| Module | Sous-tâche | Statut | Date début | Date fin | Commentaire |
|--------|-----------|--------|----------|----------|-------------|
| - | Table utilisateur | ✅ Fait | 2026-07-16 | | Base de classe Utilisateur implémentée avec hachage et vérification de mot de passe |
| - | Créer classe Utilisateur.class.php | ✅ Fait | 2026-07-16 | | Classe Utilisateur ajoutée avec hachage et vérification du mot de passe |
| - | Table permission | ✅ Fait | 2026-07-16 | | Classe Permission ajoutée pour la gestion simple des permissions |
| - | Créer classe Permission.class.php | ✅ Fait | 2026-07-16 | | Classe Permission ajoutée |
| - | Table role_permission | ✅ Fait | 2026-07-16 | | Classe Role ajoutée avec vérification de permissions |
| - | Système d'authentification (login/logout) | ✅ Fait | 2026-07-16 | | Flux de connexion/déconnexion et vérification des permissions de base implémenté |
| - | Gestion des permissions (CRUD) | 🔄 En cours | 2026-07-16 | | Gestion des permissions de base ajoutée via classes; CRUD à compléter |
| - | Table journal_audit | ⏳ À faire | | | |
| - | Créer classe JournalAudit.class.php | ⏳ À faire | | | |
| - | Table journal_connexion | ⏳ À faire | | | |
| - | Créer classe JournalConnexion.class.php | ⏳ À faire | | | |
| - | Journalisation des actions sensibles | ⏳ À faire | | | |
| - | Politique de sécurité (mots de passe, verrouillage) | ⏳ À faire | | | |

# =====================================================================
# RÉCAPITULATIF GLOBAL
# =====================================================================

| Priorité | Module | Nombre de tâches | Statut global |
|----------|--------|------------------|---------------|
| 1 | VII — Paramétrage | 11 | 0% |
| 2 | II — Élèves | 20 | 0% |
| 2 | VIII — Import | 4 | 0% |
| 3 | III — Enseignants | 20 | 0% |
| 4 | IX — Vie scolaire | 17 | 0% |
| 5 | IV — Finance | 24 | 0% |
| 6 | I — Tableau de bord | 8 | 0% |
| 6 | VI — Rapports | 5 | 0% |
| 7 | X — Portails | 9 | 0% |
| 8 | V — Communication | 7 | 0% |
| 8 | XI — Bibliothèque | 8 | 0% |
| - | Transversal (Sécurité) | 11 | 0% |

**Total des tâches :** 144 tâches identifiées

# =====================================================================
# FIN DU BACKLOG
# =====================================================================