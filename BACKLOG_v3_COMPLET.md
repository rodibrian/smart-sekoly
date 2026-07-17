# BACKLOG v3 — SMART-SEKOLY
# Phase : IMPLÉMENTATION RÉELLE, conforme au Cahier des Charges à 100%
# Projet : Smart-Sekoly — Logiciel de Gestion Scolaire
# Développeur : Baia Creative Solutions
# Date de création : 17 juillet 2026
# Remplace/complète BACKLOG.md et BACKLOG_v2.md

# =====================================================================
# CONSTAT — À LIRE PAR L'IA AVANT TOUTE TÂCHE
# =====================================================================
#
# L'état actuel du dépôt (BACKLOG.md v1, 144 tâches "✅ Fait") est
# TROMPEUR. L'audit du code montre que :
#
#   1. STRUCTURE SANS LOGIQUE : les classes PHP, tables SQL et routes
#      existent, mais la plupart des méthodes métier sont vides,
#      simplifiées à l'excès, ou ne font que retourner des valeurs
#      statiques/fictives.
#   2. BASE DE DONNÉES VIDE : aucune donnée réelle n'est persistée ;
#      les vues affichent des tableaux/indicateurs à partir de
#      variables codées en dur ou de $_SESSION, jamais de vraies
#      requêtes SQL sur une base peuplée.
#   3. AUCUN DESIGN RÉEL : le "SaaS Dashboard" décrit en section 18
#      du CDC (Tailwind CSS complet, DaisyUI, sidebar rétractable,
#      cartes statistiques, thèmes clair/sombre fonctionnels,
#      Chart.js, tableaux avec recherche/tri/export) n'existe pas.
#      Le rendu actuel est du HTML/CSS basique sans charte graphique,
#      sans composants réutilisables, sans responsive réel.
#   4. TESTS SUPERFICIELS : les fichiers tests/*.php existent mais
#      valident probablement la présence de routes/classes plutôt
#      que le comportement métier réel (calculs, règles, sécurité).
#
# CE BACKLOG v3 REPART DU CAHIER DES CHARGES COMPLET (26 sections,
# 50 décisions de cadrage, 11 modules fonctionnels) et liste TOUT ce
# qui doit être réellement construit, vérifié, et rendu fonctionnel
# de bout en bout avec de vraies données.
#
# RÈGLES NON NÉGOCIABLES POUR CHAQUE TÂCHE :
#   - Aucune donnée fictive/codée en dur dans les vues : tout doit
#     provenir d'une requête SQL réelle sur une base peuplée (via
#     seed ou saisie manuelle testée).
#   - Aucune méthode métier "coquille vide" : chaque règle du CDC
#     (calculs, seuils, validations, blocages) doit être codée et
#     testée, pas simulée.
#   - Le design system (Priorité 0 ci-dessous) doit être posé AVANT
#     de considérer une vue de module comme terminée — retoucher les
#     vues déjà "faites" en v1 fait partie du travail.
#   - Un module n'est "✅ Fait" que s'il remplit les 5 catégories de
#     critères de la section 20 du CDC (fonctionnel, technique,
#     interface, sécurité, documentation) — pas avant.
#
# =====================================================================
# LÉGENDE DES STATUTS
# =====================================================================
# ⏳ À faire | 🔄 En cours | ✅ Fait (vérifié avec données réelles) | ⚠️ Bloqué | ❌ Annulé

# =====================================================================
# PRIORITÉ 0 — FONDATIONS : DONNÉES DE TEST + DESIGN SYSTEM RÉEL
# =====================================================================
# Rien en aval n'est vérifiable sans ces deux fondations. À traiter
# en premier, avant de retoucher le moindre module métier.
#
# NOTE : les tâches de Phase 0 sont référencées ici dans BACKLOG_v3_COMPLET.md.
# BACKLOG.md conserve les 144 tâches historiques et n’inclut plus ces éléments de fondation.

## 0.A — Jeu de données de test (seed)

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Script `database/seeds/seed_etablissement.php` : établissement, année scolaire active, calendrier scolaire | ✅ Fait (validé) | §5, §5.1 | Script testé après `reset.php` et données pédagogiques générées. |
| Seed structure pédagogique : cycles → niveaux → séries → classes (10-15 classes avec effectif max et salle) | ⏳ À faire | §7 | Primaire + Collège + Lycée avec séries |
| Seed matières et programmes par classe (coefficients, volume horaire) | ⏳ À faire | §7.1 | |
| Seed 50-100 élèves réalistes avec inscriptions actives | ⏳ À faire | §6, Module II | Matricules générés via le format paramétré, pas codés en dur |
| Seed 15-20 enseignants avec contrats variés (permanent, horaire, vacataire) | ⏳ À faire | §8.2 | |
| Seed affectations pédagogiques (enseignant × matière × classe × année) | ⏳ À faire | §8.3 | |
| Seed évaluations et notes sur au moins 2 périodes pour toutes les classes | ⏳ À faire | §10.1, §10.2 | Nécessaire pour tester calcul de moyennes et bulletins |
| Seed types de frais, factures, paiements partiels et impayés | ⏳ À faire | §11.2, §11.3, §11.4 | Inclure des cas de doublon volontaires pour tester la détection |
| Seed absences, retards, sanctions, incidents sur un échantillon d'élèves | ⏳ À faire | Module IX | Pour peupler carnets de suivi et tableau de bord |
| Seed utilisateurs et rôles (un compte par profil : Directeur, Comptable, Enseignant, Secrétaire, Surveillant, DRH, Caissière) | ⏳ À faire | §13.1 | Nécessaire pour tester les permissions réellement |
| Script de réinitialisation complète de la base (`database/seeds/reset.php`) | ✅ Fait (validé) | — | Import du schéma et suppression/recréation testés et automatisés. |

## 0.B — Design system réel (CDC section 18, intégralement absent)

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Définir la charte graphique (3-4 couleurs, typographie, logo temporaire) | ⏳ À faire | §18.5 | Prérequis mentionné dans le guide de développement §1 — non fait |
| Intégrer Tailwind CSS via build réel (pas de CDN statique seul) + config `tailwind.config.js` avec les couleurs de la charte | ✅ Fait (build validé) | §2, §18.2 | `assets/css/app.css` généré via `npm run build:css`. |
| Mettre en place les variables CSS (custom properties) pour thèmes Clair/Sombre + Contraste élevé | ⏳ À faire | §18.6, décision #39 | Actuellement configurable en session (v1) mais sans variables CSS ni rendu visuel réel |
| Construire le layout SaaS Dashboard : sidebar rétractable + navbar (recherche globale, notifications, sélecteur de thème) + breadcrumb | 🔄 En cours (header/footer intégrés) | §18.3 | `templates/layout/header.php` et `footer.php` ajoutés et utilisés par les vues; design system à compléter. |
| Créer le composant "carte statistique" réutilisable (icône, titre, valeur, tendance) | ⏳ À faire | §18.4 | Utilisé par Tableau de bord (I.1) et Vision Directeur (I.8) |
| Créer le composant "tableau de données" socle commun (recherche insensible casse/accents, filtres par colonne, tri par défaut, pagination 20/50/100/tout, export Excel en un clic, sélection multiple, adaptatif au thème) | ⏳ À faire | §18.4, §40 | Actuellement `templates/partials/_tableau.php` existe mais sans ces fonctionnalités réelles — DataTables/Grid.js à intégrer |
| Intégrer Chart.js (+ ApexCharts pour graphiques avancés) sur données réelles | ⏳ À faire | §18.2 | Comparatif inter-annuel (I.9), rapports financiers (VI.2), tableau de bord RH |
| Intégrer Alpine.js pour menus, modales, dropdowns, filtres interactifs | ⏳ À faire | §18.2 | |
| Intégrer SweetAlert2/Notyf pour confirmations et notifications système | ⏳ À faire | §18.2 | Notamment la confirmation obligatoire sur doublon de paiement (décision #24) |
| Intégrer Flatpickr (dates) et Tom Select (sélection avec recherche) | ⏳ À faire | §18.2 | |
| Intégrer SheetJS pour export Excel côté client en complément de PhpSpreadsheet serveur | ⏳ À faire | §18.2, §2 | |
| Appliquer le design system aux 98 vues existantes (remplacement du HTML/CSS basique) | ⏳ À faire | §20.3 | Reprise complète, pas un ajout de classes CSS superficiel |
| Vérifier responsive réel sur les 3 tailles d'écran avec le nouveau layout | ⏳ À faire | §18.8, §19.3 | Le fichier `responsive.css` actuel est un patch, pas un design responsive natif Tailwind |
| Réglages d'accessibilité fonctionnels (taille de police ajustable, contraste WCAG AA) | ⏳ À faire | §18.8, décision #35 | |
| Animations et transitions (apparition composants, hover, skeleton loading, modales) | ⏳ À faire | §18.7 | |

# =====================================================================
# PRIORITÉ 1 — MODULE VII : PARAMÉTRAGE (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Vérifier que `ParametrageEtablissement` lit/écrit réellement en base (pas de valeur par défaut codée en dur) | ✅ Fait (validé) | §14, décision #16 | Lecture/écriture DB implémentées et testées (hardening des clés). |
| Assistant de configuration initiale : implémenter les 19 étapes réelles (§22.1), chacune persistée | ✅ Fait (backend) | §22.1, VII.1 | Étapes 1–19 persistées en base, audit d’étape créé et validé par `tests/integration/test_assistant_flow.php`. Reste l’UI d’assistant et l’automatisation complète de la sauvegarde. |
| Génération de matricule réellement paramétrable `{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}` testée avec changement de format à chaud | ✅ Fait (validé) | décision #16 | Format dynamique et padding opérationnels (tests CLI réussis). |
| Numérotation séquentielle des reçus/factures par année scolaire, non réutilisable après annulation | ✅ Fait (validé) | décision #14 | Initialisation transactionnelle des séquences, génération auto lors de création facture/paiement, tests POST CLI validés (FacturePostTest.php, PaiementPostTest.php). |
| Seuils d'alerte réellement configurables (absences, notes) et utilisés par le moteur d'alerte (§10.6) | 🔄 En cours | §14 | Persistance des clés `seuil_*` ajoutée et testée; intégration au moteur d'alerte à faire. |
| Modèles de documents (bulletins, reçus, attestations, billets) réellement paramétrables avec rendu dynamique | 🔄 En cours | §14, §21 | Persistance `modele_*` ajoutée (stockage JSON validé); rendu PDF dynamique à implémenter. |
| Historique des modifications de paramétrage (paramétrage courant) | 🔄 En cours | VII.2 | `JournalAudit` activé ; les entrées d'audit sont créées pour chaque étape et vérifiées par le test d'intégration. |
| Sauvegarde automatique réelle : dump SQL horodaté exécuté selon fréquence/heure paramétrées | 🔄 En cours | §13.5, décision #7 | Configuration de sauvegarde persistée (session/table) ; exécution/planification des dumps et tests de restauration à implémenter. |
| Test de restauration à partir d'une sauvegarde générée | ⏳ À faire | §23.2 (scénario panne serveur) | |
| Thème par défaut au niveau établissement appliqué réellement à tous les comptes sans préférence individuelle | ⏳ À faire | §18.6 | Application centrale du thème à valider et propager aux comptes. |

# =====================================================================
# PRIORITÉ 2 — MODULE II : GESTION DES ÉLÈVES (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Modèle PERSONNE → RÔLE → PROFIL réellement implémenté (pas de duplication entre Personne/Eleve) | ⏳ À faire | §4.2 | Vérifier que Eleve hérite bien de Personne en base, pas seulement en PHP |
| Inscription annuelle obligatoire : impossible de créer un élève sans passer par une inscription liée à l'année active | ⏳ À faire | §6, §4.2 | Contrôle serveur réel, pas juste un formulaire |
| Contrôle d'effectif maximum de classe à l'inscription (blocage réel si dépassement) | ⏳ À faire | §7, décision #17 | |
| Dossier élève unique : fiche consolidée réellement alimentée par toutes les données liées (notes, absences, discipline, paiements/dettes, carnet, documents) | ⏳ À faire | II.2 | v1 a une vue "dossier" — vérifier qu'elle interroge vraiment toutes les tables, pas des placeholders |
| Suivi personnalisé : calcul réel des statistiques de performance et tableaux comparatifs par classe/matière | ⏳ À faire | II.3, décision #32 | |
| Fiche "Alerte" : détection réelle des élèves sous le seuil configuré (moyenne, absences, sanctions, baisse de note) | ⏳ À faire | §10.6 | |
| Génération automatique de documents depuis le dossier élève (certificat de scolarité, relevé de notes, attestation, quittance) en PDF réel | ⏳ À faire | II.4, §21 | |
| Changement de classe en cours d'année : historique de l'ancienne affectation conservé et consultable | ⏳ À faire | §17, II.5 | |
| Redoublement : proposition AUTOMATIQUE selon seuil réel + validation manuelle bloquante avant nouvelle inscription | ⏳ À faire | décision #3 | v1 a la classe `Redoublement` — vérifier que la proposition est calculée, pas saisie manuellement |
| Transfert d'élève : statut dédié, attestation générée, dossier académique conservé et consultable en lecture seule | ⏳ À faire | décision #25 | |
| Gestion des documents obligatoires : alerte réelle si document manquant, liste des élèves incomplets | ⏳ À faire | §4.2, §12.2, II.6 | |
| Carnet de suivi : écriture réelle depuis chaque module source (absence, retard, sanction, incident, billet, annonce) — pas une simple table isolée | ⏳ À faire | décision #43, II.7 | Vérifier les liens carnet ↔ modules réellement câblés |
| Carnet de suivi collectif : écriture simultanée dans plusieurs carnets en une seule action | ⏳ À faire | II.7, §17 | |
| Dossier documentaire physique : upload réel de fichiers vers `documents/eleves/{ID_ELEVE}/` avec vérification des documents obligatoires | ⏳ À faire | §4.2, §12.2 | Actuellement probablement pas d'upload de fichier réel |
| Versionnage des documents élève (historique des mises à jour, date, auteur) | ⏳ À faire | §12.2, décision #30 (principe similaire) | |

# =====================================================================
# PRIORITÉ 2bis — MODULE VIII : IMPORT ET MIGRATION (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Téléchargement d'un modèle Excel/CSV réel (fichier généré, pas un lien mort) | ⏳ À faire | décision #9, VIII.1 | |
| Import élèves : contrôle de conformité réel avec messages d'erreur clairs par ligne | ⏳ À faire | VIII.1 | |
| Import enseignants : mêmes contrôles | ⏳ À faire | VIII.1 | |
| Import notes : import réel avec rattachement aux bonnes évaluations/périodes | ⏳ À faire | VIII.2 | |
| Journal d'import : rapport détaillé (lignes importées, ignorées, en erreur) persisté et consultable après coup | ⏳ À faire | VIII.3 | |
| Test avec fichier volontairement fautif (lignes manquantes, doublons, formats invalides) | ⏳ À faire | §23.1 (scénario Import) | |

# =====================================================================
# PRIORITÉ 3 — MODULE III : GESTION DES ENSEIGNANTS (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Dossier enseignant consolidé réellement alimenté (identité, matières, contacts, documents) | ⏳ À faire | III.1 | |
| Emploi du temps hebdomadaire réel : créneau, salle, matière, classe — avec détection de double réservation de salle | ⏳ À faire | §9, décision #17 | Actuellement probablement absent (pas de module Emploi du temps visible dans l'arborescence) |
| Séance réelle avec statuts (prévu/réalisé/annulé/reporté/remplacé) et champ enseignant remplaçant ponctuel | ⏳ À faire | §9, décision #18/#27 | |
| Saisie des notes en grille/tableau pour une classe entière, avec import Excel dédié | ⏳ À faire | III.3 | |
| Appel et absences depuis l'interface enseignant (marquage présent/absent/retard depuis la liste de classe) | ⏳ À faire | III.4, IX.1 | |
| Observations pédagogiques réellement enregistrées et visibles dans l'historique de l'élève | ⏳ À faire | III.5 | |
| Historique des remplacements et indicateur de disponibilité (charge horaire actuelle vs contractuelle) calculé réellement | ⏳ À faire | décision #27, III.6 | |
| Demande de congé → validation → synchronisation avec emploi du temps (blocage réel si conflit) | ⏳ À faire | §8.4, III.7 | Vérifier le blocage effectif, pas juste un statut "en congé" affiché |
| Solde de congés calculé automatiquement | ⏳ À faire | §8.4 | |
| Heures supplémentaires : validation par le responsable + calcul automatique de la rémunération selon le contrat | ⏳ À faire | §8.5 | |
| Calcul des salaires réel selon le type de contrat (fixe/horaire × taux/forfait), avec heures réalisées, absences, congés pris en compte | ⏳ À faire | §11.7, §8.2 | v1 a `Salaire.class.php` — vérifier que le calcul utilise des données réelles, pas un montant fixe |
| Tableau de bord RH avec indicateurs calculés sur données réelles (taux d'absentéisme, ancienneté moyenne, pyramide des âges, répartition par contrat) | ⏳ À faire | §8.6, VI.6 | |

# =====================================================================
# PRIORITÉ 4 — MODULE IX : VIE SCOLAIRE ET DISCIPLINE (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Appel numérique réel depuis une liste de classe active, version responsive smartphone testée en conditions réelles | ⏳ À faire | IX.1 | |
| Cumul des absences/retards et déclenchement d'alerte au seuil configuré | ⏳ À faire | IX.2 | Doit utiliser SeuilAlerte réellement |
| Gestion des justificatifs d'absence (upload, statut justifié/non justifié) | ⏳ À faire | IX.2 | |
| Sanctions : proposition → validation par un responsable habilité → application effective, tracée dans le dossier ET le carnet | ⏳ À faire | §10.5, décision #12 | |
| Incidents avec photos, témoins, actions menées, traçage dans les carnets de TOUS les élèves concernés | ⏳ À faire | IX.4 | Upload de photo réel à implémenter |
| Billet d'entrée généré après une absence, imprimable, tracé dans le carnet et l'historique de présence | ⏳ À faire | §11.9, §21.4 | Génération PDF réelle au format 10x15cm |
| Billet de sortie et autorisation de sortie, avec signature parent si applicable, tracés dans le carnet | ⏳ À faire | §11.9, §21.5, §21.6 | |
| Planning des surveillants avec affectation réelle, visualisation hebdomadaire, rappels | ⏳ À faire | IX.5 | |
| Carnet de suivi collectif : écriture d'un événement dans plusieurs carnets simultanément (incident multi-élèves, annonce de classe) | ⏳ À faire | IX.6, §17 | |

# =====================================================================
# PRIORITÉ 5 — MODULE IV : FINANCE (implémentation réelle — priorité critique)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Types de frais réellement paramétrables (scolarité, cantine, transport, uniforme, inscription, activités) | ✅ Fait (validé) | §11.2 | Implémentée via `TypeFraisDAO` avec CRUD complet, création/lecture/archivage, montant par défaut paramétrable, tests unitaires et intégration passants (TypeFraisDAOTest.php, TypeFraisIntegrationTest.php). |
| Facturation : facture réelle générée à partir des types de frais applicables à l'élève/sa classe | ✅ Fait (validé) | §11.3 | Implémentée via `FactureDAO::creerFacture()` : génère factures en base avec lignes associées aux types de frais, numérotation séquentielle via `SequenceNumerotation::getNext()`, montant total calculé automatiquement, persistence réelle en tables `facture` et `ligne_facture`. Tests passants : FactureDAOTest.php (CRUD + annulation), FactureIntegrationTest.php (end-to-end avec seed données). Migration schéma (`migrate_parametrage.php`) appliquée pour colonnes manquantes. |
| Remises : application réelle (pourcentage ou montant fixe) avec motif et validation obligatoire par un responsable AVANT application | ⏳ À faire | décision #13 | Vérifier le blocage tant que non validé |
| Échéancier paramétrable : facture réglable en plusieurs échéances (dates/montants), suivi individualisé (payée/partielle/en retard) calculé réellement | ⏳ À faire | décision #23, §11.4 | |
| Paiement : enregistrement réel avec date, montant, mode (espèce/banque/mobile money), utilisateur | ⏳ À faire | §11.4 | |
| Contrôle de doublon RÉEL à la saisie (même élève, même type de frais, même montant, même jour) avec confirmation explicite obligatoire | ⏳ À faire | décision #24 | Critère de test PHPUnit prioritaire — actuellement probablement absent en vrai |
| Paiements groupés (plusieurs enfants d'une famille, ou groupe d'élèves au même tarif) en une seule transaction | ⏳ À faire | §11.4 | |
| Numérotation séquentielle réelle des reçus/factures par année scolaire (REC-2026-000123), jamais réutilisée | ✅ Fait (validé) | décision #14 | Implémentée via `SequenceNumerotation::getNext()`, format paramétrable {PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}, intégré aux contrôleurs Facture et Paiement. |
| Caisse : entrées/sorties réelles, suivi quotidien, historique complet, état consolidé | ⏳ À faire | §11.6 | |
| Interface caisse dédiée : saisie par matricule, calcul automatique de la monnaie à rendre, suivi du fond de caisse, historique rapide des derniers paiements d'un élève | ⏳ À faire | §11.6, décision #48 | |
| Impression de reçu thermique réelle (format 80mm) en plus du PDF A4 | ⏳ À faire | §11.6, §21.2 | |
| Calcul des salaires enseignants intégré au flux Finance (paiement effectif des salaires suivi) | ⏳ À faire | §11.7 | |
| Gestion des stocks : entrées/sorties réelles, alertes de stock bas configurables | ⏳ À faire | §11.8, décision #33/#44 | Absent de l'arborescence actuelle — module à créer |
| Gestion des prêts de matériel (élève/enseignant, sortie/retour, notification de retard) | ⏳ À faire | §11.8, décision #33 | |
| Suivi des impayés avec détection et relance réelle (génération de message, pas simulation) | ⏳ À faire | IV.5 | v1 a un prototype — vérifier le calcul réel sur échéances en base |
| Rapports financiers quotidiens/mensuels/annuels calculés sur données réelles | ⏳ À faire | IV.5 | |
| Gestion des repas (cantine) : réservation réelle et suivi | ⏳ À faire | décision #45, IV.7 | |
| Gestion des examens blancs et concours avec instructions intégrées | ⏳ À faire | décision #47, IV.8 | Absent de l'arborescence actuelle — module à créer |

# =====================================================================
# PRIORITÉ 6 — MODULE I : TABLEAU DE BORD (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Indicateurs clés calculés en temps réel depuis la base (élèves inscrits, enseignants actifs, absences du jour, paiements en attente) | ⏳ À faire | I.1 | Aucune valeur codée en dur tolérée |
| Agenda du jour ET agenda partagé de l'établissement (distinct du calendrier scolaire) avec événements réels | ⏳ À faire | I.2, §9.1, décision #31 | Le module Agenda partagé est absent de l'arborescence — à créer |
| Actualités et annonces réellement publiées et affichées | ⏳ À faire | I.3 | |
| Rapports automatiques périodiques réellement générés (mensuel/trimestriel/annuel) | ⏳ À faire | I.4 | |
| Prévisions pour l'année suivante calculées à partir des effectifs réels actuels | ⏳ À faire | I.5 | |
| Recherche globale fonctionnelle (nom, matricule, classe) sur élèves + enseignants + dossiers administratifs | ⏳ À faire | I.6, décision #28 | |
| Rapports dynamiques avec sélection multiple réelle, export PDF/Excel fonctionnel | ⏳ À faire | I.7, décision #34/#46 | |
| Vision Directeur : taux d'occupation des classes, ratio élèves/enseignant, évolution sur 3 ans, taux de réussite — tous calculés sur données réelles | ⏳ À faire | I.8 | |
| Comparatif inter-annuel avec vrais graphiques (Chart.js) sur au moins 2 années de données | ⏳ À faire | I.9 | Nécessite le seed d'au moins 2 années scolaires |

# =====================================================================
# PRIORITÉ 6bis — MODULE VI : RAPPORTS ET STATISTIQUES (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Rapports académiques : moyennes par classe et taux de réussite par matière calculés sur notes réelles | ⏳ À faire | VI.1 | |
| Rapports financiers : graphiques réels des paiements mensuels, suivi des impayés | ⏳ À faire | VI.2 | |
| Export PDF/Excel réellement fonctionnel sur tous les rapports (pas un bouton décoratif) | ⏳ À faire | VI.3 | |
| Générateur de rapports personnalisés (critères classe/matière/période/filtre) produisant un résultat réel | ⏳ À faire | VI.4, décision #34 | |
| Rapports officiels Ministère (Effectif, Pédagogique, Administratif, Financier, Sanitaire) avec contenu réel conforme aux standards attendus | ⏳ À faire | VI.5 | |
| Tableau de bord RH avec indicateurs réels (turnover, absentéisme, ancienneté, pyramide des âges) | ⏳ À faire | VI.6 | Doublon volontaire avec III — vérifier cohérence des chiffres entre les deux vues |

# =====================================================================
# PRIORITÉ 7 — MODULE X : PORTAILS ÉLÈVE/PARENT (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Génération de code d'accès sécurisé réellement unique et lié à un élève en base | ⏳ À faire | décision #22, X.1 | |
| Authentification portail par matricule + code (sans compte utilisateur), accès consultation uniquement | ⏳ À faire | §13.2, X.1 | |
| Portail consultation : notes, bulletins, absences, retards, sanctions réellement affichés depuis la base pour l'élève concerné uniquement | ⏳ À faire | X.2 | Vérifier l'isolation des données entre élèves |
| Portail paiements (parents) : historique et soldes réels | ⏳ À faire | X.2 | |
| Portail emploi du temps réel de la classe de l'élève | ⏳ À faire | X.2 | Dépend du module Emploi du temps (Priorité 3) |
| Portail documents personnels (certificats, attestations générés) | ⏳ À faire | X.2 | |
| Gestion des repas (réservation élève) réellement fonctionnelle | ⏳ À faire | X.3 | |
| Comptes multiples : un parent avec un seul code consulte plusieurs enfants réels | ⏳ À faire | décision #22 (fratrie), X.4 | |
| Vérifier l'accès restreint au réseau local uniquement (hors périmètre externe) | ⏳ À faire | décision #22 | |
| Test responsive réel du portail sur smartphone (X.5) avec le nouveau design system | ⏳ À faire | X.5 | |

# =====================================================================
# PRIORITÉ 8 — MODULE V : COMMUNICATION (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Messagerie interne réellement fonctionnelle entre enseignants et administration | ⏳ À faire | V.1 | |
| Publication d'annonces collectives réellement diffusées | ⏳ À faire | V.2 | |
| Annonces tracées automatiquement dans les carnets de suivi des élèves concernés (classe entière ou établissement) | ⏳ À faire | V.2, §17 | Vérifier le câblage réel avec le module Carnet de suivi |

# =====================================================================
# PRIORITÉ 8bis — MODULE XI : BIBLIOTHÈQUE DOCUMENTAIRE (implémentation réelle)
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Bibliothèque de documents administratifs : upload et stockage réel des fichiers (règlement intérieur, circulaires, programmes) | ⏳ À faire | XI.1, décision #30 | |
| Classement par catégorie et public visé fonctionnel | ⏳ À faire | XI.1 | |
| Versionnage réel des documents administratifs (historique consultable) | ⏳ À faire | décision #30 | |
| Accès différencié selon le rôle réellement appliqué (pas juste visuel) | ⏳ À faire | XI.1 | |
| Manuel utilisateur intégré avec contenu réel et à jour (pas un texte de remplissage) | ⏳ À faire | XI.2, décision #37 | |
| Tutoriels courts par rôle réellement rédigés pour chaque profil (7 profils listés en §13.1) | ⏳ À faire | XI.3 | |

# =====================================================================
# PRIORITÉ 9 — TRANSVERSAL : SÉCURITÉ, UTILISATEURS, JOURNALISATION
# =====================================================================

| Tâche | Statut | Réf. CDC | Commentaire |
|-------|--------|----------|-------------|
| Authentification réelle avec hachage (`password_hash`) vérifié en conditions réelles (pas de mot de passe en clair résiduel) | ⏳ À faire | §13, .cursor/rules §6 | |
| Permissions réellement paramétrables par l'administrateur, arborescence Menu → Module → Sous-module → Action | ⏳ À faire | §13.1 | Vérifier que les 7 profils standards bloquent réellement les accès non autorisés |
| Politique de sécurité des comptes appliquée réellement : longueur mot de passe, verrouillage après échecs, changement obligatoire à la 1ère connexion, complexité | ⏳ À faire | §13.3, décision #19 | |
| Journal d'audit alimenté réellement par toute action sensible (modif/suppression note, annulation paiement, changement d'affectation) avec ancienne/nouvelle valeur | ⏳ À faire | §13.4 | |
| Contrôles de cohérence exécutables par l'admin (facture sans paiement, note orpheline, échéance impayée non signalée, document manquant) | ⏳ À faire | §13.4, décision #36 | |
| Journal des connexions réellement peuplé (date, IP, utilisateur, navigateur) | ⏳ À faire | §13.5 | |
| Suppression logique vérifiée sur TOUTES les données sensibles (notes, paiements, inscriptions, factures) — aucun DELETE physique résiduel dans le code | ⏳ À faire | décision #26, .cursor/rules §3 | Grep exhaustif sur `DELETE FROM` dans le code |
| Archivage manuel d'une année scolaire vers fichier externe fonctionnel | ⏳ À faire | §13.6, décision #20 | |
| Consultation en lecture seule des données archivées / anciens élèves sans réactivation de l'année | ⏳ À faire | décision #29 | |

# =====================================================================
# PRIORITÉ 10 — VÉRIFICATION FINALE (Critères de validation, CDC §20)
# =====================================================================
# À exécuter module par module APRÈS implémentation réelle, avant de
# marquer quoi que ce soit "✅ Fait" dans ce backlog.

| Tâche | Statut | Réf. CDC |
|-------|--------|----------|
| Tests de persévérance POST de finance (CLI) : FacturePostTest.php, PaiementPostTest.php, CaissePostTest.php validés | ✅ Fait (validé) | IV.1 | AccessControl blindée pour CLI, redirects désactivées en CLI pour inspection de session, DAO synchronisé avec $_SESSION. |
| Pour chaque module : toutes les fonctionnalités listées sont opérationnelles avec données réelles | ⏳ À faire | §20.1 |
| Pour chaque module : cas d'usage principaux testés et validés manuellement | ⏳ À faire | §20.1 |
| Pour chaque module : messages d'erreur clairs et explicites en français | ⏳ À faire | §20.1 |
| Pour chaque module : code documenté (PHPDoc), requêtes SQL optimisées, aucune erreur PHP/MySQL | ⏳ À faire | §20.2 |
| Pour chaque module : design system respecté, responsive réel, thèmes clair/sombre compatibles, tableaux conformes au socle commun | ⏳ À faire | §20.3 |
| Pour chaque module : permissions appliquées, actions sensibles auditées, validation serveur systématique, accès non autorisés bloqués | ⏳ À faire | §20.4 |
| Pour chaque module : documentation utilisateur et tutoriels rédigés | ⏳ À faire | §20.5 |
| Recette complète de bout en bout (guide de paramétrage §22 → premier bulletin généré) | ⏳ À faire | §22 |
| Rejouer intégralement les scénarios de test §23.1, §23.2, §23.3 avec données réelles et cocher chaque ligne | ⏳ À faire | §23 |

# =====================================================================
# RÉCAPITULATIF GLOBAL — BACKLOG v3
# =====================================================================

| Priorité | Thème | Nb tâches | Statut global |
|----------|-------|-----------|---------------|
| 0 | Fondations (seed + design system) | 25 | ⏳ 0% |
| 1 | VII — Paramétrage réel | 10 | ⏳ 0% |
| 2 | II — Élèves réel | 15 | ⏳ 0% |
| 2bis | VIII — Import réel | 6 | ⏳ 0% |
| 3 | III — Enseignants réel | 12 | ⏳ 0% |
| 4 | IX — Vie scolaire réel | 9 | ⏳ 0% |
| 5 | IV — Finance réel | 18 | ⏳ 0% |
| 6 | I — Tableau de bord réel | 9 | ⏳ 0% |
| 6bis | VI — Rapports réel | 6 | ⏳ 0% |
| 7 | X — Portails réel | 10 | ⏳ 0% |
| 8 | V — Communication réel | 3 | ⏳ 0% |
| 8bis | XI — Bibliothèque réel | 6 | ⏳ 0% |
| 9 | Transversal sécurité réel | 9 | ⏳ 0% |
| 10 | Vérification finale | 9 | ⏳ 0% |

**Total des tâches v3 :** 147 tâches identifiées

# =====================================================================
# FIN DU BACKLOG v3
# =====================================================================
