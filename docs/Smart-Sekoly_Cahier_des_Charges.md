# CAHIER DES CHARGES — VERSION FINALE
## SMART-SEKOLY
### Logiciel de Gestion Scolaire — Version Locale

Solution complète de gestion administrative, pédagogique et financière pour les établissements Primaire, Secondaire (Collège) et Lycée à Madagascar.

| | |
|---|---|
| **Projet** | Smart-Sekoly |
| **Développeur** | Baia Creative Solutions |
| **Date** | Juillet 2026 |

---

## Sommaire

1. Présentation du projet
2. Choix technologiques
3. Architecture du projet
4. Principes de conception du modèle de données
5. Gestion de l'année scolaire
6. Gestion des inscriptions scolaires
7. Structure pédagogique
8. Gestion des enseignants
9. Gestion des emplois du temps
10. Gestion des évaluations et des notes
11. Gestion administrative et financière
12. Gestion documentaire et fichiers
13. Sécurité, utilisateurs et journalisation
14. Paramétrage de l'établissement
15. Modules fonctionnels de l'application
16. Synthèse de l'architecture globale
17. Gestion des cas particuliers et scénarios exceptionnels
18. Conception de l'interface utilisateur
19. Exigences non-fonctionnelles
20. Critères de validation d'un module
21. Spécifications des documents générés
22. Guide de paramétrage initial recommandé
23. Scénarios de test clés
24. Conclusion et prochaines étapes
25. Feuille de route des évolutions futures
26. Glossaire

---

## 1. Présentation du projet

### 1.1 Contexte

Le présent document constitue le cahier des charges technique et fonctionnel du projet Smart-Sekoly, développé par Baia Creative Solutions : un logiciel complet de gestion scolaire, destiné aux établissements malgaches couvrant le primaire, le secondaire cycle 1 (collège) et le secondaire cycle 2 (lycée).

Le logiciel a vocation à être générique et paramétrable, afin de s'adapter aux besoins et aux règles de fonctionnement propres à chaque établissement, sans nécessiter de modification du code source.

### 1.2 Objectifs

- Centraliser la gestion administrative, pédagogique et financière d'un établissement scolaire.
- Couvrir l'intégralité du cycle de vie d'un élève : inscription, scolarité, évaluation, passage de classe, jusqu'à la sortie.
- Offrir un outil configurable (système de notation, périodes scolaires, types de contrats, frais, etc.) plutôt qu'un outil figé.
- Fournir une solution fonctionnant en local, sans dépendance à une connexion internet, avec une architecture qui pourra évoluer vers une version en ligne dans une phase ultérieure.

### 1.3 Périmètre de la version actuelle

- Déploiement local uniquement (pas de version en ligne dans cette phase).
- Gestion d'un établissement unique par installation.
- Couverture des cycles primaire, collège et lycée avec leurs spécificités respectives (séries, matières, coefficients, etc.).

### 1.4 Journal des décisions de cadrage validées

50 décisions de cadrage ont été validées avec le client. Elles constituent la référence contractuelle pour le développement.

| # | Sujet | Décision retenue |
|---|---|---|
| 1 | Système de notation | Notation sur 20, configurable par cycle ; possibilité d'appréciations qualitatives pour le primaire au lieu de notes chiffrées. |
| 2 | Multi-établissement | Un seul établissement par installation dans cette version. La table de paramétrage établissement est prévue pour ne pas bloquer une évolution future. |
| 3 | Redoublement / passage de classe | Proposition automatique selon un seuil configurable, avec validation manuelle obligatoire par le directeur/conseil de classe avant création de la nouvelle inscription. |
| 4 | Notifications aux parents | Notifications internes à l'application uniquement pour cette version (consultables sur place). L'envoi SMS/email réel est repoussé à une phase ultérieure. |
| 5 | Mobile money | Enregistrement manuel des paiements reçus par mobile money (Mvola, Orange Money, Airtel Money). Pas d'intégration API dans cette version. |
| 6 | Multi-postes en local | Fonctionnement prévu en réseau local (LAN) : un poste serveur (XAMPP/WAMP) et des postes clients via navigateur sur le réseau interne de l'établissement. |
| 7 | Sauvegardes | Sauvegarde automatique quotidienne, à une heure précise par défaut, avec fréquence paramétrable (quotidienne, hebdomadaire, mensuelle, ou délai personnalisé). |
| 8 | Langues | Interface développée uniquement en français pour cette version, mais la structure des données (tables de traduction) est conçue pour supporter l'ajout ultérieur du malgache et/ou de l'anglais sans refonte. |
| 9 | Import de données existantes | Module d'import de données prévu (élèves, enseignants, notes). L'utilisateur télécharge d'abord un modèle de fichier, le remplit, puis l'importe. Le système vérifie la conformité et gère les erreurs avec des messages clairs. Approche valable pour tous les types d'importation. |
| 10 | Interface graphique / template | Abandon de la piste Edulearn (template LMS, question de licence). Le front-end est conçu comme un système de design SaaS Dashboard sur-mesure, basé sur Tailwind CSS, détaillé en section 18. |
| 11 | Calendrier scolaire | Entité CALENDRIER_SCOLAIRE paramétrable par année scolaire (jours fériés, vacances, journées pédagogiques), réutilisée par les absences, l'emploi du temps et le calcul des salaires horaires. |
| 12 | Discipline et sanctions | Entité SANCTION paramétrable (avertissement, retenue, exclusion temporaire, exclusion définitive), avec niveau de gravité configurable et validation par un responsable avant application. |
| 13 | Remises, bourses, réductions | Entité REMISE paramétrable (pourcentage ou montant fixe), applicable à une facture, avec motif et responsable de validation. |
| 14 | Numérotation des reçus/factures | Numérotation séquentielle automatique et paramétrable par année scolaire (ex. REC-2026-000123), non réutilisable et non modifiable après émission. |
| 15 | Format du bulletin officiel | Modèle de bulletin paramétrable par établissement, avec un modèle par défaut proche des standards du Ministère de l'Éducation Nationale malgache, personnalisable (logo, mentions, ordre des rubriques). |
| 16 | Format des matricules | Format paramétrable par établissement, avec motif configurable (ex. `{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}`), généré automatiquement à l'inscription/au recrutement. |
| 17 | Effectif de classe et salles | Effectif maximum configurable par classe ; entité SALLE avec capacité, utilisée pour les inscriptions et pour l'emploi du temps (évite les doubles réservations). |
| 18 | Remplacement ponctuel d'enseignant | Ajout d'un champ « enseignant remplaçant » au niveau de la séance réelle, pour un remplacement d'un jour/cours sans modifier l'affectation pédagogique globale. |
| 19 | Sécurité des comptes utilisateurs | Règles configurables : longueur minimale du mot de passe, verrouillage après un nombre d'essais échoués défini, changement obligatoire du mot de passe par défaut à la première connexion. |
| 20 | Archivage et rétention des données | Conservation illimitée par défaut en local, avec export/archivage manuel d'une année scolaire vers un fichier externe ; pas de suppression automatique. |
| 21 | Bibliothèque / ressources pédagogiques | Hors périmètre de cette version (non demandé) ; l'architecture modulaire permet de l'ajouter ultérieurement sans refonte. |
| 22 | Portails Élève et Parent | Consultation uniquement via un accès sécurisé par matricule et code généré par l'administration (pas de compte utilisateur). Accessibles depuis le réseau local de l'établissement. L'accès depuis l'extérieur est hors périmètre. |
| 23 | Échéancier de paiement | Une facture peut être réglée en plusieurs échéances paramétrables (dates et montants), avec suivi individualisé de chaque échéance (payée, partielle, en retard). |
| 24 | Contrôle de doublon de paiement | Alerte automatique à la saisie si un paiement similaire (même élève, même montant, même type de frais) a déjà été enregistré le même jour, avec confirmation obligatoire avant validation. |
| 25 | Transfert d'élève (départ/arrivée) | Statut d'inscription dédié « transféré », avec motif, date, établissement d'origine/destination (texte libre) et génération d'une attestation de transfert. Le dossier académique de l'élève reste conservé. |
| 26 | Suppression accidentelle de données | Pas de suppression physique pour les données sensibles (notes, paiements, inscriptions) : suppression logique (statut « annulé/archivé ») avec traçabilité dans le journal d'audit (voir 13.4), permettant une restauration. |
| 27 | Remplacement et disponibilité des enseignants (RH) | En complément du remplacement ponctuel de séance (décision #18), le module RH conserve un historique des remplacements et un indicateur de disponibilité par enseignant (charge horaire actuelle vs. contractuelle). |
| 28 | Recherche globale | Moteur de recherche rapide transverse (nom, matricule, classe) accessible depuis le tableau de bord, couvrant élèves, enseignants et dossiers administratifs. |
| 29 | Consultation des données archivées | Les bulletins, paiements et dossiers des années archivées et des anciens élèves restent consultables en lecture seule depuis le dossier élève et les modules concernés, sans réactivation de l'année scolaire. |
| 30 | Bibliothèque de documents administratifs | Ajout d'un espace documentaire interne (règlements intérieurs, circulaires, programmes officiels) distinct de la bibliothèque pédagogique élève. Chaque document est versionné (historique des mises à jour, date, auteur). |
| 31 | Agenda partagé de l'établissement | Ajout d'un agenda scolaire partagé (examens, réunions, événements), distinct du calendrier scolaire structurel, avec rappels internes et lien avec l'emploi du temps. |
| 32 | Suivi pédagogique comparatif | Le suivi personnalisé de l'élève est complété par des tableaux comparatifs par classe et par matière, pour objectiver le repérage des élèves en difficulté. |
| 33 | Prêt de matériel | L'inventaire du matériel est complété par un suivi des prêts (élève/enseignant, date de sortie/retour) et des alertes de stock bas ou de matériel défectueux. |
| 34 | Rapports personnalisés | En complément des rapports standards, ajout d'un générateur de rapports sur mesure (choix des critères : classe, matière, période, filtre), exportable en PDF/Excel. |
| 35 | Accessibilité de l'interface | L'interface intègre des réglages d'accessibilité (contraste renforcé, taille de police ajustable) et reste utilisable sur du matériel modeste. |
| 36 | Audit de cohérence des données | Le journal d'audit est complété par des contrôles de cohérence exécutables par l'administrateur (ex. facture sans paiement associé, note orpheline). |
| 37 | Formation et support intégrés | Ajout d'un manuel utilisateur intégré et de tutoriels courts par rôle, accessibles depuis l'application ; l'assistance reste locale. |
| 38 | Feuille de route des évolutions futures | Les pistes déjà identifiées sont regroupées et formalisées dans une feuille de route dédiée (voir section 25). |
| 39 | Thème d'affichage personnalisable | Ajout d'un paramétrage de thème utilisateur (Clair, Sombre, autres), appliqué automatiquement et instantanément à l'ensemble de l'interface. |
| 40 | Standardisation des tableaux de données | Tous les tableaux respectent un socle commun obligatoire : recherche intelligente, filtres et tri par défaut, bouton d'export Excel. |
| 41 | Gestion des congés du personnel | Module de demande, validation et suivi des congés (payés, maladie) avec solde automatique. Synchronisation avec les emplois du temps : un enseignant en congé ne peut pas être affecté à des cours. |
| 42 | Gestion documentaire des personnes | Chaque élève et enseignant possède un dossier personnel stocké sur le serveur local (chemin paramétrable) contenant tous les documents nécessaires (certificats médicaux, photos, copies, CIN, diplômes). L'établissement peut paramétrer des documents obligatoires. |
| 43 | Carnet de suivi de l'élève | Journal chronologique de tous les événements marquants de la scolarité de l'élève : absences, retards, sanctions, incidents, billetterie, relations parents-établissement, annonces collectives. |
| 44 | Gestion du stock et des prêts | Suivi des entrées/sorties de stock, gestion des prêts de matériel aux élèves et enseignants, avec historique tracé. |
| 45 | Gestion des repas (cantines) | Module de réservation et suivi des repas (pris en compte dans le module associé). |
| 46 | Rapports dynamiques | Générateur de rapports avec sélection multiple (élèves, années, classes, matières, filtres, dates) permettant d'obtenir des statistiques personnalisées. |
| 47 | Gestion des examens blancs | Module dédié au suivi des examens blancs et concours avec instructions claires intégrées. |
| 48 | Interface caisse simplifiée | Interface dédiée avec gestion des espèces (calcul de monnaie) et impression de reçu thermique. |
| 49 | Gestion des billetteries | Génération de billets d'entrée, de sortie, d'autorisation avec impression et traçage dans le carnet de suivi. |
| 50 | Planning des surveillants | Gestion des plannings de surveillance (récréation, étude, permanence) avec rappels. |

---

## 2. Choix technologiques

| Composant | Technologie retenue |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (ES6+), Tailwind CSS (design responsive) |
| Interface graphique (thème) | Système de design SaaS Dashboard sur-mesure, inspiré des standards Tailwind CSS Dashboard UI, entièrement en français (voir section 18) |
| Backend | PHP orienté objet (POO) |
| Base de données | MySQL (choix retenu et définitif pour ce projet) |
| Serveur local | XAMPP ou WAMP |
| Visualisation de données | Chart.js, ApexCharts (graphiques avancés optionnels) |
| Génération de documents | Bibliothèques PHP de génération PDF (bulletins, reçus, attestations, billets) |
| Export Excel | PhpSpreadsheet (génération de fichiers .xlsx côté serveur) |
| Thématisation de l'interface | Variables CSS (custom properties) + Tailwind CSS en mode class/data-theme, pour un changement de thème instantané côté client |
| Gestion des fichiers | Stockage local sur serveur dans un répertoire paramétrable, organisation par dossiers utilisateurs |

### 2.1 Bonnes pratiques de développement

- Code organisé par modules fonctionnels (élèves, enseignants, paiements, etc.).
- Nomenclature entièrement en français : noms de classes, de fonctions, de variables et de champs explicites et cohérents.
- Commentaires détaillés sur chaque bloc logique et chaque partie complexe du code.
- Séparation stricte entre la logique métier, l'interface et l'accès aux données.
- Centralisation des paramètres généraux dans des fichiers de configuration dédiés.

### 2.2 Interface graphique

L'interface utilisateur de Smart-Sekoly est conçue comme un SaaS Dashboard moderne, sur-mesure, construit avec Tailwind CSS et un ensemble de bibliothèques complémentaires (graphiques, tableaux, notifications, icônes). Elle n'est pas basée sur un template commercial existant, ce qui évite toute contrainte de licence et garantit une personnalisation complète.

L'interface est :

- entièrement en français (menus, libellés, boutons, messages) ;
- personnalisable graphiquement (charte de couleurs, logo, identité de l'établissement) ;
- adaptée fonctionnellement aux besoins réels de la gestion scolaire (tableau de bord, bulletins, paiements, emplois du temps, etc.) ;
- responsive pour une utilisation sur tous les types d'écrans (ordinateurs, tablettes, smartphones).

---

## 3. Architecture du projet

### 3.1 Arborescence recommandée

```
/smart-sekoly
│
├── index.php               # Page d'accueil / tableau de bord
├── config/                 # Fichiers de configuration (connexion BD, constantes)
├── classes/                # Classes PHP (Élève, Enseignant, Paiement, etc.)
├── modules/                # Modules fonctionnels (élèves, enseignants, finance…)
├── assets/                 # CSS, JS, images
├── templates/               # Fichiers HTML réutilisables
├── includes/                # Fonctions utilitaires et scripts communs
├── database/                # Scripts SQL et sauvegardes
└── documents/                # Dossier de stockage des fichiers (paramétrable)
    ├── eleves/
    │   └── {ID_ELEVE}/
    │       ├── photo.jpg
    │       ├── certificat_medical.pdf
    │       └── ...
    ├── enseignants/
    │   └── {ID_ENSEIGNANT}/
    │       ├── photo.jpg
    │       ├── cin.pdf
    │       └── diplomes/
    └── etablissement/
        ├── reglement_interieur.pdf
        └── ...
```

### 3.2 Convention de codage

Chaque classe métier respecte une structure claire, avec attributs privés, constructeur, et méthodes explicitement nommées en français.

```php
<?php
// Classe Eleve – Gestion des informations d'un élève
class Eleve {
    private $id;
    private $nom;
    private $prenom;
    private $dateNaissance;
    private $matricule;
    private $dossierDocuments; // Chemin vers le dossier personnel

    public function __construct($nom, $prenom, $dateNaissance) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->dossierDocuments = $this->creerDossierPersonnel();
    }

    public function getDossierDocuments() {
        return $this->dossierDocuments;
    }

    private function creerDossierPersonnel() { // Crée le dossier de l'élève sur le serveur
    }
}
?>
```

---

## 4. Principes de conception du modèle de données

### 4.1 Principes fondamentaux

- Séparation claire entre données d'identité et données historiques.
- Conservation de l'historique de toutes les opérations importantes.
- Paramétrage maximal des règles métier (notation, périodes, contrats, frais).
- Séparation entre données administratives, pédagogiques et financières.
- Gestion systématique par année scolaire.

### 4.2 Modèle général des personnes

Toutes les personnes du système reposent sur une entité centrale unique, PERSONNE, afin d'éviter la duplication d'informations et de permettre à un même individu de cumuler plusieurs rôles.

```
PERSONNE → RÔLE → PROFIL SPÉCIFIQUE
```

- **Profils spécialisés** : Élève, Enseignant, Personnel administratif.
- **Entité Personne** (informations communes à tout individu) : nom, prénom, date de naissance, sexe, téléphone, email, adresse, photo, pièce d'identité.
- **Rôles possibles** : élève, enseignant, parent, directeur, secrétaire, comptable, surveillant. Une même personne peut cumuler plusieurs rôles simultanément.
- **Profil Élève** (spécifique) : numéro matricule, date d'entrée, statut scolaire (actif, ancien élève, transféré, diplômé). Un élève n'est jamais directement rattaché à une classe : il passe obligatoirement par une inscription annuelle.
- **Profil Enseignant** (spécifique) : numéro matricule, date d'embauche, statut (actif, en congé, sorti), type de contrat.

**Gestion documentaire des personnes**

Chaque personne (élève, enseignant) dispose d'un dossier personnel physique sur le serveur local. Le chemin de stockage est paramétrable par l'administrateur. L'organisation est la suivante :

- `documents/eleves/{ID_ELEVE}/` pour les élèves
- `documents/enseignants/{ID_ENSEIGNANT}/` pour les enseignants

L'établissement peut définir des documents obligatoires par type de personne (ex. pour les élèves : certificat médical, photo, copie d'acte de naissance ; pour les enseignants : CIN, diplômes, photo). Lors de la création ou de la mise à jour d'une personne, le système vérifie la présence de ces documents et alerte si certains manquent. Une liste permet de visualiser facilement les personnes n'ayant pas fourni tous les documents obligatoires.

---

## 5. Gestion de l'année scolaire

L'année scolaire constitue le cadre temporel de référence du système. Toute donnée importante (inscriptions, classes, affectations, emplois du temps, évaluations, paiements, contrats) y est rattachée.

**Attributs d'une année scolaire :**

- libellé (ex. : 2026-2027)
- date de début
- date de fin
- état : préparation, active, clôturée, archivée

Ce mécanisme permet de conserver plusieurs années historiques dans la même base de données et d'en limiter les modifications une fois clôturées.

### 5.1 Calendrier scolaire

Chaque année scolaire possède un calendrier scolaire paramétrable définissant les jours non ouvrés : jours fériés, périodes de vacances, journées pédagogiques. Ce calendrier est une référence commune utilisée par plusieurs modules : calcul des absences, génération de l'emploi du temps, calcul des heures effectives pour les enseignants rémunérés à l'heure.

---

## 6. Gestion des inscriptions scolaires

L'inscription représente l'appartenance d'un élève à une classe pour une année scolaire donnée :

```
ÉLÈVE → INSCRIPTION → CLASSE → ANNÉE SCOLAIRE
```

Cette conception permet de gérer nativement : le passage de classe, le redoublement, les changements de classe en cours d'année et l'historique scolaire complet de chaque élève (l'élève reste unique, seule son inscription évolue d'une année à l'autre).

---

## 7. Structure pédagogique : Cycle → Niveau → Série → Classe

La classe n'est jamais stockée comme un simple champ texte ; elle repose sur une hiérarchie structurée :

```
Cycle → Niveau → Série → Classe
```

**Exemples :**

| Cycle | Niveau | Série | Classe(s) |
|---|---|---|---|
| Primaire | CM2 | – | CM2 A |
| Collège | 6ème | – | 6ème A, 6ème B |
| Lycée | Terminale | OSE | Tle OSE1, Tle OSE2 |

Cette organisation permet des statistiques par cycle, par niveau, une gestion fine des séries, et une évolutivité future sans refonte du modèle.

Chaque classe possède un effectif maximum configurable, contrôlé lors des inscriptions afin d'éviter le dépassement de capacité.

### 7.1 Matières et programmes

```
CLASSE → PROGRAMME → MATIÈRE
```

Le programme pédagogique définit, pour chaque matière : coefficient, volume horaire, et caractère obligatoire ou optionnel — évitant ainsi de coder les matières directement au niveau des classes.

---

## 8. Gestion des enseignants

### 8.1 Identité, contrat et affectation

La gestion des enseignants est structurée en trois volets distincts : identité, contrat, affectation pédagogique.

### 8.2 Types de contrats pris en charge

| Type de contrat | Mode de rémunération |
|---|---|
| Permanent | Salaire fixe mensuel |
| Forfaitaire | Montant fixe, indépendant du volume horaire exact |
| Horaire | Paiement au nombre d'heures réalisées |
| Vacataire | Paiement à l'intervention |
| Stagiaire | Indemnité éventuelle |
| Bénévole | Aucune rémunération (heures suivies à titre indicatif) |

### 8.3 Affectation pédagogique

L'affectation représente le lien entre enseignant, matière, classe et année scolaire, avec une période de validité (date de début / date de fin). Ce mécanisme permet de gérer les remplacements et changements d'enseignant en cours d'année tout en conservant l'historique complet des affectations.

### 8.4 Gestion des congés du personnel

Les enseignants et le personnel administratif peuvent demander des congés (payés, maladie, formation). Le système permet :

- **Demande de congé** : l'enseignant soumet une demande avec dates et motif.
- **Validation** : le responsable (directeur/DRH) valide ou refuse la demande.
- **Suivi du solde** : calcul automatique du solde de congés restant.
- **Synchronisation avec les emplois du temps** : un enseignant en congé ne peut pas être affecté à des cours sur la période concernée. Le système vérifie automatiquement les conflits et alerte lors de la planification.
- **Historique** : traçabilité complète de toutes les demandes et validations.

### 8.5 Gestion des heures supplémentaires

Le système permet l'enregistrement et le suivi des heures supplémentaires effectuées par les enseignants, avec validation par le responsable et calcul automatique de la rémunération associée selon le type de contrat.

### 8.6 Tableau de bord RH

Un tableau de bord dédié présente les indicateurs clés des ressources humaines : nombre d'enseignants actifs, taux d'absentéisme, ancienneté moyenne, pyramide des âges, répartition par type de contrat.

---

## 9. Gestion des emplois du temps

Le système distingue clairement deux notions :

- **Horaire enseignant** : charge de travail contractuelle (ex. : 18 heures/semaine).
- **Emploi du temps élève** : cours effectivement suivis par une classe.

**Éléments de structure :**

- **Créneau horaire** : plages définies (ex. 08h00-09h00).
- **Salle** : ressource physique avec une capacité définie, associée aux créneaux afin d'éviter les doubles réservations et de vérifier la compatibilité avec l'effectif de la classe.
- **Emploi du temps** : association classe / enseignant / matière / salle / créneau.
- **Séance réelle** : suivi de ce qui s'est effectivement déroulé, avec statuts prévu, réalisé, annulé, reporté, remplacé. En cas d'absence imprévue de l'enseignant titulaire, un enseignant remplaçant ponctuel peut être renseigné directement sur la séance réelle, sans modifier l'affectation pédagogique globale.

### 9.1 Agenda partagé de l'établissement

Distinct du calendrier scolaire structurel (jours fériés, vacances) et de l'emploi du temps des cours, l'agenda partagé recense les événements ponctuels de la vie de l'établissement : examens, réunions administratives ou pédagogiques, conseils de classe, sorties scolaires.

Chaque événement porte un titre, une date/heure, un lieu, un public concerné (direction, enseignants, une classe, tout l'établissement) et une description optionnelle.

Rappels internes consultables sur le tableau de bord, sans envoi SMS/email réel.

Les événements liés à des cours (examens sur une plage horaire donnée) peuvent être croisés avec l'emploi du temps pour repérer d'éventuels conflits de salle ou de créneau.

### 9.2 Planning des surveillants

Le système permet la gestion des plannings de surveillance :

- Affectation des surveillants (récréation, étude, permanence)
- Visualisation hebdomadaire des plannings
- Rappels automatiques avant les périodes de surveillance
- Gestion des remplacements en cas d'absence

---

## 10. Gestion des évaluations et des notes

### 10.1 Principe

Une note n'existe jamais isolément ; elle appartient toujours à une évaluation :

```
MATIÈRE → ÉVALUATION → NOTE → MOYENNE → BULLETIN
```

- **Évaluation** : activité notée (matière, classe, date, période, coefficient, enseignant).
- **Note** : rattachée à un élève et à une évaluation (valeur, appréciation).

### 10.2 Périodes configurables

Le système doit accepter plusieurs découpages, paramétrables selon l'établissement :

- Trimestres (T1, T2, T3)
- Semestres (S1, S2)
- Bimestres (B1 à B4)

### 10.3 Calcul des moyennes

Les règles de calcul sont paramétrables, par exemple :

- Moyenne annuelle simple : (T1 + T2 + T3) / 3
- Moyenne pondérée : (T1 + T2 + 2×T3) / 4
- Moyenne semestrielle : (S1 + S2) / 2

### 10.4 Bulletin scolaire

Le bulletin est une photographie officielle d'une période donnée. Il contient : matières, moyennes, coefficients, moyenne générale, rang, appréciations et décision. Le classement, calculé automatiquement, peut être figé lors de la validation officielle du bulletin.

Le modèle de bulletin est paramétrable par établissement : un modèle par défaut proche des standards du Ministère de l'Éducation Nationale malgache est fourni, personnalisable (logo, mentions, ordre des rubriques).

### 10.5 Discipline et sanctions

Le suivi disciplinaire d'un élève repose sur une entité SANCTION paramétrable : type (avertissement, retenue, exclusion temporaire, exclusion définitive), niveau de gravité configurable, motif, et validation obligatoire par un responsable habilité avant application effective. L'historique des sanctions est conservé dans le dossier académique de l'élève et tracé dans son carnet de suivi.

### 10.6 Suivi pédagogique personnalisé

**Fiche « Alerte »** : vue synthétique des élèves en difficulté :

- Moyenne inférieure à un seuil configurable
- Absences dépassant un seuil configurable
- Sanctions récentes
- Baisse de note significative (alertes automatiques)

**Historique des observations** : chronologie des observations faites par tous les enseignants sur un élève, permettant une coordination efficace pour un suivi cohérent.

---

## 11. Gestion administrative et financière

### 11.1 Architecture

```
TYPE_FRAIS → FACTURE → PAIEMENT → CAISSE
```

Le système distingue systématiquement ce qui doit être payé, ce qui est facturé, et ce qui est réellement encaissé.

### 11.2 Types de frais (paramétrables)

Scolarité, cantine, transport, uniforme, inscription, activités.

### 11.3 Facturation

La facture représente une dette de l'élève, détaillée par type de frais avec un total global.

Chaque facture peut intégrer une ou plusieurs remises (entité REMISE, paramétrable en pourcentage ou en montant fixe), applicables par exemple pour une réduction fratrie, une bourse au mérite ou un cas social. Chaque remise porte un motif et nécessite la validation d'un responsable avant application.

### 11.4 Paiement

Chaque paiement enregistre : date, montant, mode de paiement (espèce, banque, mobile money), et utilisateur ayant procédé à l'enregistrement.

Une facture peut être réglée en une seule fois ou selon un échéancier paramétrable (nombre d'échéances, dates et montants prévisionnels), avec suivi individualisé de l'état de chaque échéance : payée, partielle, en retard.

Un contrôle automatique de doublon est effectué à la saisie : si un paiement similaire (même élève, même type de frais, même montant, même jour) existe déjà, une alerte est affichée et une confirmation explicite est requise avant validation.

Gestion des paiements groupés : possibilité d'enregistrer des paiements pour plusieurs enfants d'une même famille en une seule transaction, ou pour un groupe d'élèves (ex. transport pour 20 élèves avec le même tarif).

### 11.5 Numérotation officielle des factures et reçus

Les factures et reçus sont numérotés de façon séquentielle, automatique et paramétrable par année scolaire (ex. REC-2026-000123). Un numéro émis n'est jamais réutilisé ni modifiable a posteriori, afin de garantir la traçabilité comptable.

### 11.6 Caisse

La caisse retrace l'ensemble des mouvements financiers : entrées (paiements élèves) et sorties (dépenses), avec suivi quotidien, historique complet et état financier consolidé.

**Interface caisse dédiée** : vue épurée optimisée pour les encaissements rapides :

- Saisie par numéro matricule ou recherche d'élève
- Calcul automatique de la monnaie à rendre
- Suivi du fond de caisse
- Gestion des différents modes de paiement (espèces, chèques, mobile money)
- Impression de reçu thermique : génération et impression de reçus au format adapté aux imprimantes thermiques de caisse.
- Historique rapide : affichage des derniers paiements d'un élève pour vérification rapide.

### 11.7 Gestion des salaires enseignants

Le calcul du salaire dépend du type de contrat :

- Contrat fixe : salaire contractuel.
- Contrat horaire : nombre d'heures réalisées × taux horaire.
- Forfait : montant défini à l'avance.

Le système assure également le suivi des heures réalisées, des heures supplémentaires, des absences, des congés et des paiements effectués.

### 11.8 Gestion de l'inventaire et des stocks

**Gestion des stocks :**

- Suivi des entrées et sorties de stock (fournitures, matériel)
- Alertes de stock bas configurable
- Historique des mouvements

**Gestion des prêts de matériel :**

- Prêt à un élève ou un enseignant
- Date de sortie, date de retour prévue
- Notification en cas de retard
- Historique complet des prêts par matériel et par personne

### 11.9 Billets et autorisations

- **Billet d'entrée** : génération d'un billet d'entrée (après une absence) avec impression. Le billet est tracé dans le carnet de suivi de l'élève et dans l'historique des présences.
- **Billet de sortie** : génération d'un billet de sortie autorisée. Le billet est tracé dans le carnet de suivi de l'élève.
- **Autorisations de sortie** : génération de documents d'autorisation de sortie avec traçage dans le carnet de suivi.

---

## 12. Gestion documentaire et fichiers

### 12.1 Génération de documents officiels

Le logiciel doit permettre la génération automatique de documents officiels : bulletins, certificats, reçus, attestations, billets — à partir de modèles paramétrables.

**Caractéristiques des documents générés :**

- Numéro unique et séquentiel
- Date de génération
- Signature (avec nom du responsable)
- Logo de l'établissement
- Contenu modifiable avant impression
- Historique des générations (tracé dans le carnet de suivi de l'élève)

**Modèles de courriers** : bibliothèque de modèles (certificat de scolarité, attestation d'assiduité, convocation, etc.) avec génération en un clic. Les contenus sont modifiables avant impression, le logo et les informations du responsable sont automatiquement intégrés.

### 12.2 Dossier documentaire des personnes

**Principe** : chaque personne (élève, enseignant) dispose d'un dossier personnel physique sur le serveur local.

```
documents/
├── eleves/
│   └── {ID_ELEVE}/
│       ├── photo.jpg
│       ├── certificat_medical_2026.pdf
│       ├── acte_naissance.pdf
│       └── ...
├── enseignants/
│   └── {ID_ENSEIGNANT}/
│       ├── photo.jpg
│       ├── cin.pdf
│       ├── diplomes/
│       │   ├── licence.pdf
│       │   └── master.pdf
│       └── ...
└── etablissement/
    ├── reglement_interieur.pdf
    ├── circulaires/
    └── ...
```

- **Chemin de stockage paramétrable** : l'administrateur peut configurer le chemin racine des documents.
- **Documents obligatoires paramétrables** : l'établissement peut définir des documents obligatoires par type de personne :
  - Pour les élèves : certificat médical, photo, acte de naissance, etc.
  - Pour les enseignants : CIN, diplômes, photo, etc.
- **Suivi des documents** : une liste permet de visualiser pour chaque personne les documents manquants ou périmés (ex. certificat médical à renouveler).
- **Versionnage** : chaque mise à jour d'un document conserve les versions précédentes, avec date, auteur et commentaire.

### 12.3 Bibliothèque de documents administratifs

Espace documentaire interne pour stocker et mettre à disposition les documents de référence de l'établissement : règlement intérieur, circulaires, programmes officiels, notes de service.

- **Organisation** : classement par catégorie et par public visé.
- **Versionnage** : chaque mise à jour conserve les versions précédentes.
- **Accès différencié** : selon le rôle de l'utilisateur.

---

## 13. Sécurité, utilisateurs et journalisation

### 13.1 Gestion des accès

```
UTILISATEUR → RÔLE → PERMISSION
```

**Paramétrage des permissions** : le système est conçu pour être entièrement paramétrable par l'administrateur. Ce dernier peut définir, pour chaque rôle, les accès à chaque module, sous-module, et jusqu'au niveau des actions (créer, lire, modifier, supprimer, exporter, valider, etc.). Une arborescence hiérarchique des permissions est fournie par défaut (Menu → Module → Sous-module → Bouton/Action), que l'administrateur peut activer/désactiver selon les besoins.

**Profils d'accès standards (modifiables) :**

- Directeur : accès complet à tous les modules.
- Comptable : accès au module financier uniquement.
- Enseignant : accès limité aux notes de ses propres classes.
- Secrétaire : accès à la gestion administrative et aux inscriptions.
- Surveillant : accès aux modules Vie scolaire et carnets de suivi.
- DRH : accès aux modules RH et gestion du personnel.
- Caissière : accès au module Caisse uniquement.

### 13.2 Accès Parent/Élève (consultation)

**Principe** : les parents et les élèves n'ont pas de compte utilisateur. L'accès se fait via un système sécurisé :

- L'administration génère un code d'accès pour chaque élève.
- Le parent ou l'élève utilise le matricule + code d'accès pour consulter les informations.
- Accès uniquement en consultation (pas de modification).
- Accessible uniquement depuis le réseau local de l'établissement.

**Informations consultables** : notes et bulletins, absences et retards, sanctions, paiements (pour les parents), emploi du temps, documents personnels (certificats, attestations).

### 13.3 Politique de sécurité des comptes

Règles configurables au niveau du paramétrage :

- Longueur minimale du mot de passe
- Verrouillage temporaire du compte après un nombre d'essais de connexion échoués défini
- Obligation de changer le mot de passe par défaut lors de la première connexion
- Complexité du mot de passe (majuscule, minuscule, chiffre, caractère spécial)

### 13.4 Journalisation et audit

Toute action sensible est enregistrée dans un journal d'audit : modification ou suppression de note, annulation de paiement, changement d'affectation pédagogique. Chaque entrée du journal contient : utilisateur, date, action réalisée, ancienne valeur et nouvelle valeur.

**Contrôles de cohérence** : l'administrateur peut exécuter des contrôles de cohérence pour détecter les anomalies :

- Facture sans paiement associé après échéance
- Note rattachée à une inscription clôturée
- Échéance impayée non signalée
- Document obligatoire manquant

### 13.5 Sauvegardes

Sauvegarde automatique locale (dump SQL horodaté), déclenchée quotidiennement à une heure précise par défaut. La fréquence et l'heure sont paramétrables par l'administrateur : quotidienne, hebdomadaire, mensuelle, ou selon un délai personnalisé.

**Journal des connexions** : historique détaillé des connexions (date, IP, utilisateur, navigateur) pour la sécurité et la détection d'intrusions.

### 13.6 Archivage et rétention des données

Les données sont conservées de façon illimitée par défaut en local (le volume reste faible à l'échelle d'un établissement). Une année scolaire au statut archivée reste consultable, sans suppression automatique. L'administrateur peut néanmoins exporter manuellement une année scolaire complète vers un fichier externe pour archivage hors système.

Pour un ancien élève (statut diplômé ou transféré), le dossier complet — bulletins, historique des paiements, attestations générées — reste accessible en lecture seule.

---

## 14. Paramétrage de l'établissement

Les éléments suivants doivent être configurables sans intervention sur le code source :

- Système de notation et mentions.
- Périodes scolaires (trimestre, semestre, bimestre).
- Cycles scolaires (primaire, secondaire cycle 1, secondaire cycle 2).
- Matières et barèmes personnalisables.
- Langues d'interface : français développé pour cette version ; structure de données prévue pour activer ultérieurement le malgache et/ou l'anglais sans refonte.
- Monnaie, logo et informations générales de l'établissement.
- Fréquence et heure des sauvegardes automatiques.
- Documents obligatoires par type de personne.
- Chemin de stockage des documents.
- Types de sanctions et niveaux de gravité.
- Seuils d'alerte (absences, notes).
- Modèles de documents (bulletins, reçus, attestations, billets).

---

## 15. Modules fonctionnels de l'application

L'application Smart-Sekoly est organisée en 11 modules fonctionnels.

### Module I – Tableau de bord

- I.1 Indicateurs clés : nombre d'élèves inscrits, nombre d'enseignants actifs, absences du jour, paiements en attente.
- I.2 Agenda du jour et agenda partagé : cours programmés, réunions administratives, examens planifiés, rappels internes.
- I.3 Actualités : annonces scolaires, nouveaux règlements, sorties et événements.
- I.4 Rapports automatiques : génération périodique (mensuelle, trimestrielle, annuelle) des rapports d'inscriptions, de paiements et de résultats scolaires.
- I.5 Prévisions simples : estimation indicative du nombre de classes/salles nécessaires pour l'année suivante.
- I.6 Recherche globale : moteur de recherche rapide (nom, matricule, classe).
- I.7 Rapports dynamiques : générateur de rapports avec sélection multiple (élèves, années, classes, matières, filtres, dates), exportable en PDF/Excel.
- I.8 Tableau de bord « Vision Directeur » : vue consolidée avec indicateurs stratégiques (taux d'occupation des classes, ratio élèves/enseignant, évolution des effectifs sur 3 ans, taux de réussite).
- I.9 Comparatif inter-annuel : graphiques comparant les performances, les effectifs, les taux de redoublement d'une année sur l'autre.

### Module II – Gestion des élèves

- II.1 Inscriptions : formulaire d'inscription (nom, classe, date de naissance, contacts), attribution d'un matricule unique.
- II.2 Dossier élève unique : fiche unique regroupant l'ensemble des informations de l'élève (identité, inscription en cours et historique, notes, absences, discipline, paiements et dettes, carnet de suivi, documents).
- II.3 Suivi personnalisé : statistiques de performance, alertes pour élèves en difficulté, tableaux comparatifs par classe et par matière.
- II.4 Génération automatique de documents : depuis le dossier élève, génération en un clic des documents courants (certificat de scolarité, relevé de notes, attestation, quittance).
- II.5 Cas particuliers : changement de classe en cours d'année, redoublement, transfert d'élève.
- II.6 Gestion des documents obligatoires : visualisation des documents manquants, alertes.
- II.7 Carnet de suivi de l'élève : journal chronologique de tous les événements marquants de la scolarité (absences, retards, sanctions, incidents, billets, relations parents-établissement, annonces collectives). Possibilité d'écrire dans le carnet d'un élève ou de plusieurs élèves simultanément.

### Module III – Gestion des enseignants

- III.1 Informations personnelles : dossier enseignant (nom, matières, contacts, documents).
- III.2 Planning : emploi du temps hebdomadaire, affectation des cours par classe.
- III.3 Évaluations : saisie des notes (interface rapide type grille/tableau, adaptée à une saisie en série pour toute une classe). Import de notes à partir d'un modèle Excel.
- III.4 Appel et absences depuis l'interface enseignant : marquage présent/absent directement depuis la liste de classe.
- III.5 Observations pédagogiques : remarques de l'enseignant sur un élève.
- III.6 Historique et disponibilité : suivi des remplacements effectués et charge horaire actuelle vs. contractuelle.
- III.7 Gestion des congés : demande, validation, suivi du solde, synchronisation avec l'emploi du temps.

### Module IV – Gestion administrative et financière

- IV.1 Paiements : facturation, reçus PDF automatiques, paiements partiels et échéanciers paramétrables, paiements groupés.
- IV.2 Caisse : interface dédiée, gestion des espèces (calcul de monnaie), impression de reçu thermique, historique rapide.
- IV.3 Inventaire et stocks : gestion du matériel, suivi des stocks, alertes de stock bas, gestion des prêts de matériel.
- IV.4 Ressources humaines : gestion des contrats, suivi des salaires, congés, heures supplémentaires.
- IV.5 Contrôles et rapports financiers : alerte de doublon de saisie, rapports de caisse quotidiens, mensuels et annuels, suivi des impayés.
- IV.6 Gestion des billets : billets d'entrée, de sortie, autorisations.
- IV.7 Gestion des repas (cantines) : réservation et suivi des repas.
- IV.8 Gestion des examens blancs : suivi des examens blancs et concours avec instructions claires intégrées.

### Module V – Communication interne

- V.1 Messagerie : messages entre enseignants et administration.
- V.2 Annonces : publication d'événements scolaires, diffusion de règlements, annonces collectives tracées dans les carnets des élèves.

### Module VI – Rapports et statistiques

- VI.1 Rapports académiques : moyenne des notes par classe, taux de réussite par matière.
- VI.2 Rapports financiers : graphiques des paiements mensuels, suivi des impayés.
- VI.3 Exportation : export des rapports au format PDF/Excel.
- VI.4 Rapports personnalisés : générateur de rapports sur mesure avec sélection multiple (élèves, années, classes, matières, filtres, dates).
- VI.5 Rapports officiels : génération des rapports pour le Ministère de l'Éducation (statistiques, effectifs, résultats), paramétrables selon les filtres sélectionnés.
- VI.6 Tableau de bord RH : indicateurs (turnover, absentéisme, ancienneté moyenne, pyramide des âges).

### Module VII – Paramétrage et configuration initiale

- VII.1 Assistant de configuration initiale : guide l'administrateur à travers tous les paramètres nécessaires (établissement, année scolaire, calendrier, cycles, classes, notation, périodes, matricules, frais, bulletins, contrats, sanctions, rôles, sécurité, sauvegardes, langue, thème, documents obligatoires, chemin de stockage).
- VII.2 Paramétrage courant : accès aux mêmes réglages après la mise en service, avec historique des modifications.
- VII.3 Sécurité : authentification par rôle, gestion fine des permissions, sauvegardes, politique de sécurité.

### Module VIII – Import et migration de données

- VIII.1 Import initial : import des élèves et enseignants existants à partir d'un fichier Excel/CSV structuré. Méthodologie : téléchargement d'un modèle, remplissage, import avec contrôle et validation. Gestion des erreurs avec messages clairs.
- VIII.2 Import des notes : import des notes à partir d'un modèle Excel.
- VIII.3 Journal d'import : rapport des lignes importées, ignorées ou en erreur.

### Module IX – Vie scolaire et discipline

- IX.1 Appel numérique : liste de classe avec marquage rapide présent/absent/retard, depuis l'interface enseignant ou le surveillant. Version responsive pour utilisation sur smartphone.
- IX.2 Gestion des absences et retards : historique par élève, gestion des justificatifs, cumul et seuils d'alerte configurables.
- IX.3 Suivi disciplinaire : remarques et sanctions, historique consultable dans le dossier élève et le carnet de suivi.
- IX.4 Gestion des incidents : enregistrement des incidents (bagarres, dégradations, comportements inappropriés) avec photos, témoins, actions menées, traçage dans les carnets des élèves concernés.
- IX.5 Planning des surveillants : affectation, visualisation, rappels.
- IX.6 Carnet de suivi collectif : possibilité d'écrire une annonce ou un événement dans le carnet de plusieurs élèves simultanément (ex. incident impliquant 3 élèves, annonce à toute une classe).

### Module X – Portails Élève et Parent (consultation locale)

- X.1 Accès sécurisé : via matricule + code généré par l'administration.
- X.2 Espace Élève/Parent : consultation des notes, bulletins, absences, retards, sanctions, paiements (parents), emploi du temps, documents personnels, annonces.
- X.3 Gestion des repas : réservation et suivi (pour les élèves).
- X.4 Comptes multiples : un parent peut consulter les informations de plusieurs enfants avec un seul code.
- X.5 Interface responsive : optimisée pour les smartphones.

### Module XI – Bibliothèque documentaire, formation et support

- XI.1 Bibliothèque de documents administratifs : stockage et consultation des règlements, circulaires, programmes officiels, avec classement, versionnage et accès différencié.
- XI.2 Manuel utilisateur intégré : documentation d'aide consultable directement dans l'application.
- XI.3 Tutoriels par rôle : guides courts et illustrés adaptés à chaque profil.
- XI.4 Assistance locale : les demandes d'aide sont traitées localement.

---

## 16. Synthèse de l'architecture globale

```
PERSONNE → RÔLE → PROFIL
ÉLÈVE → INSCRIPTION → CLASSE → ANNÉE SCOLAIRE
CLASSE → PROGRAMME → MATIÈRE
ENSEIGNANT → CONTRAT → AFFECTATION → EMPLOI DU TEMPS
MATIÈRE → ÉVALUATION → NOTE → BULLETIN
ÉLÈVE → FACTURE → PAIEMENT → CAISSE
ÉLÈVE → CARNET_DE_SUIVI → ÉVÉNEMENT
PERSONNE → DOSSIER_DOCUMENTS → FICHIER
```

---

## 17. Gestion des cas particuliers et scénarios exceptionnels

| Scénario | Traitement prévu |
|---|---|
| Changement de classe en cours d'année | Modification de l'inscription en cours (nouvelle classe), avec historique de l'ancienne affectation conservé. |
| Redoublement / passage de classe | Proposition automatique selon seuil, validation manuelle obligatoire. |
| Transfert d'élève (entrée/sortie) | Statut d'inscription dédié, attestation générée, dossier conservé. |
| Oubli ou retard de paiement | Suivi des échéances en retard, alerte visible dans le dossier élève et les rapports financiers. |
| Correction d'une note déjà saisie | Modification autorisée selon les droits de l'utilisateur, tracée dans le journal d'audit. |
| Remplacement ponctuel d'un enseignant | Champ « enseignant remplaçant » sur la séance réelle, sans modifier l'affectation pédagogique globale. |
| Suppression accidentelle de données sensibles | Suppression logique uniquement (statut annulé/archivé), restauration possible, traçabilité complète. |
| Fin d'année scolaire | Passage de l'année au statut clôturée puis archivée : plus aucune modification des données de l'année, consultation toujours possible. |
| Congé d'un enseignant | L'enseignant ne peut pas être affecté à des cours pendant la période de congé. Le système vérifie et bloque les conflits. |
| Incident impliquant plusieurs élèves | Écriture simultanée dans les carnets de tous les élèves concernés. |
| Annonce collective | Écriture dans le carnet de tous les élèves d'une classe ou de tout l'établissement. |

---

## 18. Conception de l'interface utilisateur et du système de design Front-End

### 18.1 Orientation générale

L'application Smart-Sekoly intègre une interface de type SaaS Dashboard moderne, conforme aux standards actuels des applications professionnelles de gestion. L'objectif est une expérience fluide, intuitive et visuellement cohérente pour tous les profils d'utilisateurs.

### 18.2 Frameworks et bibliothèques Front-End

| Bibliothèque / Outil | Rôle |
|---|---|
| Tailwind CSS | Framework CSS utilitaire pour une interface personnalisée, responsive et maintenable |
| DaisyUI / composants Tailwind | Accélère la création des éléments d'interface en conservant la cohérence graphique |
| JavaScript (ES6+) | Gestion des interactions dynamiques côté client |
| Alpine.js (ou équivalent léger) | Menus, modales, dropdowns, filtres, composants interactifs |
| Chart.js | Graphiques statistiques principaux |
| ApexCharts (optionnel) | Graphiques avancés nécessitant plus d'interactivité |
| Lucide Icons / Heroicons | Système d'icônes moderne |
| SweetAlert2 / Notyf | Notifications, alertes système, messages utilisateur |
| DataTables / Grid.js | Tableaux avancés : recherche, tri, pagination, export |
| SheetJS (xlsx.js) | Export Excel côté client |
| Flatpickr | Champs de date et calendriers interactifs |
| Tom Select | Champs de sélection avec recherche et autocomplétion |
| CSS Custom Properties + localStorage | Moteur de thématisation (Clair, Sombre, autres) appliqué en temps réel |

### 18.3 Architecture générale du tableau de bord

- Sidebar (barre latérale) moderne et rétractable, donnant accès aux modules.
- Header/Navbar : recherche globale, notifications, informations utilisateur, paramètres du compte, sélecteur de thème.
- Fil d'Ariane (Breadcrumb) pour situer l'utilisateur.
- Zone de contenu principale organisée en pages et composants réutilisables.
- Affichage responsive : ordinateurs, tablettes, smartphones.

### 18.4 Composants réutilisables

- Cartes statistiques — indicateurs clés (icône, titre, valeur, évolution, tendance).
- Graphiques et visualisation — courbes, barres, camemberts/anneaux, interactifs et animés.
- Tableaux de données avancés — recherche instantanée, filtrage multicritère, tri, pagination, export Excel/PDF.

**Composant de tableau standard (socle obligatoire) :**

- Recherche intelligente (insensible à la casse et aux accents)
- Filtres contextuels par colonne
- Tri par défaut pertinent
- Export Excel en un clic (coin supérieur droit du tableau)
- Pagination et sélection multiple
- Adaptatif au thème actif

### 18.5 Design system et règles visuelles

- Palette de couleurs professionnelle et charte graphique homogène.
- Typographie moderne et lisible, espacements réguliers.
- Coins arrondis (border radius) et ombres légères (soft shadows).
- Effets de survol (hover), transitions fluides, animations discrètes.

### 18.6 Thèmes d'interface et personnalisation visuelle

| Thème | Usage / ambiance |
|---|---|
| Clair (Light) | Thème par défaut, fond clair et texte foncé |
| Sombre (Dark) | Fond foncé et texte clair, réduit la fatigue visuelle |
| Contraste élevé (Accessibilité) | Contraste renforcé, tailles d'éléments agrandies |
| Thème établissement (optionnel) | Basé sur les couleurs de l'identité visuelle de l'école |

**Principes techniques :**

- Chaque thème est défini par un jeu unique de variables de couleurs.
- Le thème sélectionné est mémorisé par utilisateur.
- Un thème par défaut est paramétrable au niveau de l'établissement.

### 18.7 Animations et interactions

Animations d'apparition des composants, transitions entre pages, effets hover, chargement en Skeleton Loading, confirmations d'action via fenêtres modernes.

### 18.8 Accessibilité et compatibilité matérielle

- Réglages d'accessibilité (taille de police ajustable)
- Cibles tactiles/clic suffisamment grandes
- Compatible avec les tablettes bas de gamme et ordinateurs anciens
- Version responsive pour smartphone (pas d'application mobile dédiée)

### 18.9 Objectif final du Front-End

Une interface esthétique, ergonomique, responsive, performante, facilement maintenable, construite sur des composants réutilisables, adaptée à tous les types d'écrans (ordinateurs, tablettes, smartphones).

---

## 19. Exigences non-fonctionnelles

### 19.1 Performance

- L'application doit être rapide et fluide sur le réseau local de l'établissement.
- Temps de réponse cible : < 500 ms pour les requêtes courantes.
- Temps de chargement des pages : < 2 secondes.
- Les tableaux de données doivent supporter l'affichage de plusieurs centaines de lignes sans ralentissement.
- Support jusqu'à 1000 élèves et 50 enseignants sur du matériel modeste.

### 19.2 Compatibilité navigateurs

L'application doit être compatible avec l'ensemble des navigateurs modernes :

- Firefox (version 78 et ultérieures)
- Google Chrome (version 80 et ultérieures)
- Microsoft Edge (Chromium)
- Opera
- Safari (version 13 et ultérieures)

### 19.3 Responsive et mobilité

L'interface doit être entièrement responsive et s'adapter à tous les types d'écrans :

- Ordinateurs de bureau et portables
- Tablettes (iPad, tablettes Android)
- Smartphones (consultation uniquement)

---

## 20. Critères de validation d'un module

Chaque module est considéré comme « terminé » lorsque les critères suivants sont remplis :

### 20.1 Critères fonctionnels

- Toutes les fonctionnalités listées dans la description du module sont opérationnelles
- Les cas d'usage principaux sont testés et validés
- Les messages d'erreur sont clairs et explicites
- Les workflows critiques sont fonctionnels

### 20.2 Critères techniques

- Le code est documenté et commenté
- Les requêtes SQL sont optimisées
- Le module respecte les conventions de codage définies en section 3.2
- Aucune erreur PHP/MySQL en environnement de test

### 20.3 Critères d'interface

- Le module respecte le design system défini en section 18
- Le module est responsive (ordinateur, tablette, smartphone)
- Les tableaux respectent le socle commun (section 18.4)
- Le module est compatible avec les thèmes (clair, sombre)
- Les icônes et libellés sont cohérents avec le reste de l'application

### 20.4 Critères de sécurité

- Les permissions sont correctement appliquées
- Les actions sensibles sont tracées dans le journal d'audit
- Les données sont validées côté serveur (pas uniquement côté client)
- Les accès non autorisés sont bloqués

### 20.5 Critères de documentation

- La documentation utilisateur du module est rédigée
- Les tutoriels courts pour le module sont prêts

---

## 21. Spécifications des documents générés

### 21.1 Bulletin scolaire

**Contenu :**

- En-tête : logo, nom de l'établissement, adresse, contacts
- Période : trimestre/semestre, année scolaire
- Informations élève : nom, prénom, classe, matricule
- Tableau des notes : matières, coefficients, notes, moyennes, appréciations
- Moyenne générale
- Rang dans la classe
- Décision : admis/redoublement/transfert
- Date et signature (directeur)

**Format :** A4, PDF, orientation portrait. **Modèle :** modifiable par l'établissement (section 14).

### 21.2 Reçu de paiement

**Contenu :** en-tête (logo, nom de l'établissement), numéro de reçu (format paramétrable, section 14), date, informations élève (nom, prénom, classe), détails du paiement (type de frais, montant), mode de paiement, total payé, date et signature (caissier).

**Format :** impression thermique (80mm) ou A4, PDF. **Modèle :** modifiable par l'établissement.

### 21.3 Attestation de scolarité

**Contenu :** en-tête officiel, nom/prénom/date de naissance de l'élève, classe et année scolaire, période de scolarité, objet (certification de scolarité), date et signature (directeur).

**Format :** A4, PDF.

### 21.4 Billet d'entrée

**Contenu :** nom de l'établissement, nom/prénom/classe de l'élève, date et heure, motif (retour d'absence), signature (surveillant/responsable).

**Format :** petit format (environ 10x15cm), recto uniquement. **Tracé :** dans le carnet de suivi de l'élève.

### 21.5 Billet de sortie

**Contenu :** nom de l'établissement, nom/prénom/classe de l'élève, date et heure de sortie, motif, signature (surveillant/responsable), signature du parent (si applicable).

**Format :** petit format (environ 10x15cm), recto uniquement. **Tracé :** dans le carnet de suivi de l'élève.

### 21.6 Autorisation de sortie

**Contenu :** en-tête officiel, nom/prénom/classe de l'élève, date et heure de sortie prévue, motif, nom du parent autorisant, signature du parent, signature du responsable.

**Format :** A5, PDF. **Tracé :** dans le carnet de suivi de l'élève.

### 21.7 Certificat de scolarité

**Contenu :** en-tête officiel, nom/prénom/date de naissance de l'élève, année(s) de scolarité, classe(s) fréquentée(s), attestation de fréquentation, date et signature (directeur).

**Format :** A4, PDF.

---

## 22. Guide de paramétrage initial recommandé

### 22.1 Ordre recommandé de configuration

| Ordre | Action | Détails |
|---|---|---|
| 1 | Informations générales | Nom, logo, adresse, contacts, monnaie |
| 2 | Année scolaire | Création de l'année en cours (libellé, dates, état) |
| 3 | Calendrier scolaire | Jours fériés, vacances, journées pédagogiques |
| 4 | Cycles, niveaux, séries | Structure pédagogique complète |
| 5 | Classes | Création des classes avec effectif maximum |
| 6 | Salles | Création des salles avec capacité |
| 7 | Matières et programmes | Par classe ou par niveau |
| 8 | Périodes d'évaluation | Trimestres, semestres, bimestres |
| 9 | Système de notation | Barème, appréciations qualitatives |
| 10 | Types de frais | Scolarité, cantine, transport, etc. |
| 11 | Types de contrats enseignants | Permanent, horaire, etc. |
| 12 | Types de sanctions | Avertissement, retenue, etc. |
| 13 | Documents obligatoires | Par type de personne (élève, enseignant) |
| 14 | Chemin de stockage | Dossier des documents sur le serveur |
| 15 | Modèles de documents | Bulletins, reçus, attestations |
| 16 | Rôles et permissions | Création des comptes utilisateurs |
| 17 | Politique de sécurité | Mots de passe, verrouillage |
| 18 | Sauvegardes | Fréquence et heure |
| 19 | Thème par défaut | Clair, sombre, ou autre |

### 22.2 Recommandations pour la première année

| Action | Recommandation |
|---|---|
| Importer les élèves existants | Utiliser le module d'import avec le modèle fourni (section VIII.1) |
| Importer les enseignants existants | Utiliser le module d'import avec le modèle fourni (section VIII.1) |
| Importer les notes antérieures | Utiliser le module d'import des notes (section VIII.2) |
| Saisir les paiements en cours | Enregistrer les paiements récents et les dettes existantes |
| Configurer les carnets de suivi | Vérifier que les carnets sont actifs pour tous les élèves |

### 22.3 Erreurs fréquentes à éviter

| Erreur | Solution |
|---|---|
| Oublier de créer l'année scolaire | L'année scolaire est obligatoire avant toute inscription |
| Ne pas paramétrer les documents obligatoires | Les alertes de documents manquants ne fonctionneront pas |
| Oublier la sauvegarde automatique | Configurer la sauvegarde dès le premier jour |
| Ne pas tester les permissions | Vérifier que chaque rôle a les droits nécessaires |

---

## 23. Scénarios de test clés

### 23.1 Scénarios par module

| Module | Scénario | Résultat attendu |
|---|---|---|
| Inscription | Inscrire un nouvel élève | Matricule généré, dossier créé, documents obligatoires listés |
| Notes | Saisir des notes pour une classe | Calcul automatique des moyennes, bulletin générable |
| Paiement | Enregistrer un paiement | Reçu généré, solde mis à jour, doublon vérifié |
| Absence | Marquer un élève absent | Carnet de suivi mis à jour, billet généré si applicable |
| Congé | Demander un congé enseignant | Solde mis à jour, emploi du temps bloqué sur la période |
| Incident | Enregistrer un incident | Carnet des élèves concernés mis à jour |
| Import | Importer des élèves depuis Excel | Contrôle des données, rapport des erreurs |
| Export | Exporter un tableau | Fichier Excel avec mise en forme |
| Documents | Ajouter un document à un élève | Fichier stocké, document marqué comme présent |
| Carnet | Écrire une annonce collective | Tous les carnets des élèves concernés mis à jour |

### 23.2 Scénarios critiques

| Scénario | Description | Critère de succès |
|---|---|---|
| Fin d'année | Clôture d'une année scolaire | Année archivée, nouvelle année créée, consultation possible |
| Panne serveur | Simulation d'une panne | Sauvegarde disponible, restauration possible en < 30 min |
| Doublon de paiement | Saisie d'un paiement déjà effectué | Alerte affichée, confirmation requise |
| Congé enseignant | Tentative d'affectation d'un enseignant en congé | Bloqué par le système avec message explicite |
| Document obligatoire manquant | Tentative de clôture du dossier d'un élève | Alerte affichée, blocage possible selon paramétrage |
| Suppression logique | Suppression d'une note ou d'un paiement | Donnée marquée « annulée », traçable dans l'audit |

### 23.3 Tests d'intégration

| Test | Description | Critère de succès |
|---|---|---|
| Note → Bulletin | Saisie de notes → Génération de bulletin | Bulletin cohérent avec les notes saisies |
| Paiement → Caisse | Enregistrement d'un paiement → Mise à jour de la caisse | Solde de caisse correct |
| Absence → Billet | Absence non justifiée → Billet d'entrée généré | Billet imprimé, tracé dans le carnet |
| Congé → Emploi du temps | Congé validé → Vérification de l'emploi du temps | Enseignant non affecté sur la période |
| Import → Base de données | Import d'élèves → Vérification en base | Données correctement insérées |

---

## 24. Conclusion et prochaines étapes

Ce cahier des charges décrit Smart-Sekoly, une solution professionnelle, évolutive et maintenable, développée par Baia Creative Solutions, couvrant les cycles primaire, collège et lycée, avec une approche paramétrable et centrée sur les besoins réels des établissements malgaches.

Le document intègre :

- 50 décisions de cadrage validées
- 11 modules fonctionnels détaillés
- Des spécifications techniques précises
- Des critères de validation pour chaque module
- Des scénarios de test pour valider le bon fonctionnement
- Un guide de paramétrage pour faciliter la mise en service

### Phases suivantes recommandées

1. Conception du Modèle Conceptuel de Données (MCD) selon la méthode Merise
2. Transformation du MCD en schéma physique MySQL
3. Mise en place de l'architecture PHP en couches (MVC)
4. Mise en place du système de design Front-End (Tailwind CSS)
5. Définition des fiches techniques d'import (format exact des fichiers)
6. Planification détaillée des modules de développement

### Ordre de développement recommandé

| Priorité | Module | Durée estimée |
|---|---|---|
| 1 | Module VII (Paramétrage) | 3 semaines |
| 2 | Module II (Élèves) + Module VIII (Import) | 4 semaines |
| 3 | Module III (Enseignants) | 3 semaines |
| 4 | Module IX (Vie scolaire) | 3 semaines |
| 5 | Module IV (Finance) | 4 semaines |
| 6 | Module I (Tableau de bord) + Module VI (Rapports) | 3 semaines |
| 7 | Module X (Portails) | 2 semaines |
| 8 | Module V (Communication) + Module XI (Bibliothèque) | 2 semaines |

---

## 25. Feuille de route des évolutions futures

| Évolution envisagée | Renvoi | Statut |
|---|---|---|
| Version en ligne / accès à distance | Décisions #6, #22, Module X.3 | Hors périmètre actuel |
| Application mobile (élèves, parents, direction) | — | Hors périmètre actuel |
| Notifications SMS/email réelles | Décision #4, Module IX.4 | Hors périmètre actuel |
| Intégration API mobile money | Décision #5 | Hors périmètre actuel |
| Multi-établissement | Décision #2 | Hors périmètre actuel |
| Bibliothèque de ressources pédagogiques | Décision #21 | Hors périmètre actuel |
| Ajout des langues malgache/anglais | Décision #8, section 14 | Structure de données prête |

---

## 26. Glossaire

| Terme / Acronyme | Définition |
|---|---|
| API | Application Programming Interface — interface de programmation permettant à deux logiciels de communiquer. |
| CDC | Cahier Des Charges — document définissant les besoins et spécifications d'un projet. |
| CRUD | Create, Read, Update, Delete — les quatre opérations de base sur les données. |
| CSV | Comma-Separated Values — format de fichier texte où les valeurs sont séparées par des virgules. |
| Échéancier | Plan de paiement échelonné dans le temps, définissant les dates et montants des versements. |
| LAN | Local Area Network — réseau local, interne à un bâtiment ou un établissement. |
| MCD | Modèle Conceptuel de Données — schéma représentant les entités, leurs attributs et leurs relations. |
| Matricule | Numéro d'identification unique attribué à un élève ou un enseignant. |
| MVC | Modèle-Vue-Contrôleur — architecture logicielle séparant la logique métier, l'interface et la gestion des requêtes. |
| PDF | Portable Document Format — format de fichier permettant la visualisation et l'impression de documents. |
| POO | Programmation Orientée Objet — paradigme de programmation basé sur des objets et classes. |
| Responsive | Adaptation automatique de l'interface à la taille de l'écran de l'utilisateur. |
| SaaS | Software as a Service — modèle de distribution de logiciels via internet. |
| SQL | Structured Query Language — langage de requête pour les bases de données relationnelles. |
| Tailwind CSS | Framework CSS utilitaire permettant de créer des interfaces rapidement. |
| UI | User Interface — interface utilisateur. |
| UX | User Experience — expérience utilisateur. |
| WCAG | Web Content Accessibility Guidelines — normes internationales pour l'accessibilité du web. |
| XAMPP/WAMP | Piles logicielles pour le développement local : Apache, MySQL, PHP. |

---

*— Document finalisé — prêt pour la phase de conception et de développement —*

**Smart-Sekoly · Baia Creative Solutions**
