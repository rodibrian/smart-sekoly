# Guide de développement de Smart-Sekoly avec une IA (Cursor)

Ce guide complète le cahier des charges validé. Il répond à quatre questions pratiques : quels documents fournir, comment formuler les commandes, comment modulariser, comment suivre et tester.

---

## 1. Avant d'ouvrir Cursor : ce qu'il faut préparer

Le CDC (section 24) recommande déjà 6 étapes de cadrage. Concrètement, avant de commander la moindre ligne de code à l'IA, il faut avoir en main :

1. **Le MCD (Modèle Conceptuel de Données)** — méthode Merise. C'est le document le plus important à faire *avant* Cursor, pas *par* Cursor sans supervision : une IA qui invente son propre schéma de base au fil des modules va créer des incohérences (ex. l'entité PERSONNE centrale doit être posée dès le départ, elle est utilisée par tout le reste).
2. **Le schéma physique MySQL** dérivé du MCD (tables, clés, types).
3. **L'arborescence de fichiers** définitive (déjà donnée en section 3.1 du CDC — vous pouvez la fournir telle quelle).
4. **La charte graphique minimale** : 3-4 couleurs principales, police, et un exemple de composant (carte, tableau, bouton) pour que l'IA ne réinvente pas le design à chaque module.
5. Un environnement local fonctionnel (XAMPP/WAMP démarré, dépôt Git initialisé).

Si le MCD n'est pas encore fait, c'est la toute première tâche à donner à Cursor — mais en lui demandant un **document de conception** (texte/diagramme), pas du code, et en le validant vous-même avant de continuer.

---

## 2. Documents à fournir à Cursor

Cursor lit le contexte que vous lui donnez explicitement. Créez ces fichiers à la racine du projet, Cursor les indexera automatiquement :

| Fichier | Contenu | Pourquoi |
|---|---|---|
| `CAHIER_DES_CHARGES.md` | Votre CDC complet (celui que vous avez uploadé) | Référence contractuelle, à citer dans chaque prompt |
| `.cursor/rules` ou `AGENTS.md` | Règles permanentes de codage (voir §3) | Évite de répéter les mêmes contraintes à chaque commande |
| `MCD.md` ou `schema.sql` | Modèle de données validé | Empêche l'IA de réinventer les tables module après module |
| `BACKLOG.md` | Liste des modules/tâches avec statut | Suivi (voir §5) |
| `CHANGELOG.md` | Historique des décisions techniques prises en cours de route | Traçabilité, complète les 50 décisions de cadrage déjà actées |

**Le fichier `AGENTS.md` (ou `.cursor/rules`) est le plus rentable.** Il doit reprendre les points de la section 2.1 du CDC :
- Nomenclature entièrement en français (classes, variables, champs)
- PHP orienté objet, séparation stricte MVC
- Commentaires détaillés sur chaque bloc logique
- Aucune suppression physique des données sensibles (notes, paiements, inscriptions) → toujours suppression logique + entrée dans le journal d'audit
- Toute règle métier doit être paramétrable, jamais codée en dur (notation, périodes, frais, contrats)
- Un module ne doit jamais casser un module déjà validé (voir §5 sur les régressions)

Je peux vous générer ce fichier `AGENTS.md` tout de suite si vous voulez — dites-le-moi.

---

## 3. Comment formuler les commandes étape par étape

**Règle d'or : un prompt = une unité de travail testable, jamais "développe le module X" en bloc.** Une IA qui reçoit une commande trop large produit du code qu'elle ne peut pas elle-même vérifier, et vous ne pourrez pas le relire non plus.

### Structure de prompt recommandée

```
CONTEXTE : [référence au CDC — ex. "Module VII, section 14, décision de cadrage #16"]
OBJECTIF : [une seule fonctionnalité précise]
CONTRAINTES : [rappel des règles de AGENTS.md pertinentes ici]
LIVRABLE ATTENDU : [fichiers exacts à créer/modifier]
CRITÈRE D'ACCEPTATION : [comment on saura que c'est bon — reprendre section 20 du CDC]
NE PAS FAIRE : [ce qui est hors périmètre pour cette tâche précise]
```

### Exemple concret (premier module du planning, section 24)

> CONTEXTE : Module VII — Paramétrage de l'établissement (section 14 du CDC). Décision #16 : format de matricule paramétrable `{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}`.
> OBJECTIF : Créer la table `parametrage_etablissement` et la classe PHP `ParametrageEtablissement` permettant de définir et lire le format de matricule.
> CONTRAINTES : suivre AGENTS.md — POO, français, pas de valeur codée en dur.
> LIVRABLE : `database/migrations/001_parametrage.sql`, `classes/ParametrageEtablissement.php`.
> CRITÈRE D'ACCEPTATION : un test manuel doit pouvoir changer le format et générer un matricule d'exemple sans toucher au code.
> NE PAS FAIRE : ne pas encore créer l'interface d'inscription élève (module suivant).

Cette granularité vous permet de valider chaque brique avant de passer à la suivante, et de revenir en arrière facilement si l'IA se trompe.

---

## 4. Comment modulariser le développement

Le CDC vous donne déjà l'ordre (section 24) — gardez-le, il est cohérent (paramétrage d'abord, car tout en dépend) :

| Ordre | Module | Découpage interne suggéré |
|---|---|---|
| 1 | VII – Paramétrage | (a) tables config → (b) classes PHP → (c) écran admin |
| 2 | II – Élèves + VIII – Import | (a) entité PERSONNE/Élève → (b) inscriptions → (c) import CSV |
| 3 | III – Enseignants | (a) identité/contrat → (b) affectations → (c) congés |
| 4 | IX – Vie scolaire | (a) absences/sanctions → (b) carnet de suivi |
| 5 | IV – Finance | (a) facturation → (b) paiements/échéances → (c) caisse |
| 6 | I – Tableau de bord + VI – Rapports | après les autres, car il agrège leurs données |
| 7 | X – Portails | après finance/notes, car il les consulte |
| 8 | V – Communication + XI – Bibliothèque | en dernier, modules les moins couplés |

Pour chaque module, respectez systématiquement cette sous-séquence (elle correspond à votre architecture MVC en couches) :

1. **Modèle/données** — table SQL + classe PHP
2. **Logique métier** — règles, validations, calculs (ex. seuil de redoublement, alerte doublon de paiement)
3. **Vue** — écran Tailwind, en réutilisant les composants déjà validés
4. **Tests** — voir §6
5. **Revue** — vous relisez, vous validez, vous committez

**Un module = une branche Git.** Ne mergez sur la branche principale qu'après avoir testé le module en isolation. Cela évite qu'une IA qui "corrige" un module 3 casse silencieusement le module 1.

---

## 5. Comment faire le suivi

1. **`BACKLOG.md`** avec un tableau simple :

   | Module | Sous-tâche | Statut | Date | Commentaire |
   |---|---|---|---|---|
   | VII | Table parametrage | ✅ Fait | ... | ... |
   | VII | Écran admin matricule | 🔄 En cours | ... | ... |

2. **Git discipliné** : un commit = une tâche du backlog, message de commit qui référence le module (ex. `[Module VII] Ajout format matricule paramétrable`).

3. **Definition of Done par module** : reprenez tel quel les "Critères de validation d'un module" (section 20 du CDC) comme checklist de clôture — ne cochez un module "terminé" que si tous ces critères passent.

4. **Journal des décisions techniques** (`CHANGELOG.md`) : chaque fois que Cursor propose une solution non prévue dans le CDC (ex. une librairie PDF précise), notez-la — cela complète vos 50 décisions de cadrage côté technique.

5. **Revue de code systématique** : ne faites jamais confiance à un "ça marche" de l'IA sans relire le diff, en particulier sur : suppression logique vs physique, calculs financiers, et permissions/sécurité (section 13 du CDC).

---

## 6. Comment tester

Votre CDC contient déjà l'essentiel de ce qu'il faut (section 23 : scénarios clés, scénarios critiques, tests d'intégration) — la stratégie de test consiste à les opérationnaliser :

### a) Avant de commencer : des jeux de données de test
Demandez à Cursor de générer un script de "seed" (données fictives : quelques élèves, enseignants, classes) réutilisable à chaque module — sans cela, chaque test manuel repart de zéro.

### b) Tests unitaires (PHPUnit) — sur la logique métier critique
Ciblez en priorité ce qui touche à l'argent et aux notes, car une erreur y est coûteuse :
- Calcul de moyenne/coefficient et génération de bulletin
- Calcul des échéances de paiement et détection de doublon (décision #24)
- Numérotation séquentielle des reçus (décision #14)
- Seuil de passage/redoublement (décision #3)

### c) Tests manuels guidés — reprendre les tableaux de la section 23 telles quelles
La section 23.1/23.2/23.3 du CDC est déjà rédigée comme un plan de test (colonnes Scénario / Description / Critère de succès). Utilisez-la directement comme votre feuille de recette : cochez chaque ligne module par module.

### d) Tests d'intégration cross-module
Avant de clôturer un module qui dépend d'un autre (ex. Finance dépend de Paramétrage), rejouez les scénarios "Note → Bulletin", "Paiement → Caisse", etc. de la section 23.3.

### e) Recette finale avant mise en service
Reprenez le "Guide de paramétrage initial recommandé" (section 22) comme scénario de recette de bout en bout, du premier démarrage jusqu'à la génération d'un premier bulletin.

---

## Résumé express

1. MCD + schéma SQL + arborescence + charte graphique **avant** Cursor.
2. `AGENTS.md`/`.cursor/rules` avec vos règles de codage fixes.
3. Prompts découpés en unités testables, avec contexte + critère d'acceptation explicites.
4. Ordre des modules = celui du CDC (section 24), sous-découpé en modèle → logique → vue → tests → revue.
5. Suivi via `BACKLOG.md` + Git par branche/module + Definition of Done = section 20 du CDC.
6. Tests = jeux de données + PHPUnit sur le critique + scénarios de la section 23 comme feuille de recette.
