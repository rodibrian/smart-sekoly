# AUDIT BACKLOG v3 — Smart-Sekoly

## 1. Objet
Ce document constitue l'audit initial de `BACKLOG_v3_COMPLET.md` face à l'état réel du dépôt. Il porte spécialement sur la Phase 0 (fondations) afin de confirmer si les tâches de base sont présentes ou absentes avant toute implémentation métier.

## 2. Méthode
- Lecture de `docs/Smart-Sekoly_Cahier_des_Charges.md`, `.cursor/rules` et `BACKLOG_v3_COMPLET.md`.
- Inspection du dépôt réel : structure de fichiers, dossiers, scripts de données, layout et front-end.
- Vérification de l'existence des ressources attendues pour les tâches 0.A et 0.B.
- Rédaction des constats avec preuves factuelles.

## 3. Constat global Phase 0
Le dépôt contient des structures de base (contrôleurs, classes, templates) mais les fondations réelles identifiées par `BACKLOG_v3_COMPLET.md` sont absentes ou incomplètes.

### 3.1 0.A — Données de test / seeds
- `database/migrations/` existe et contient de nombreuses migrations.
- Le dossier `database/seeds/` est absent ou inexistant dans le dépôt.
- `README.md` mentionne pourtant un dossier `database/seeds/` et des scripts de seed.
- Aucune création de données de test réelle n'existe dans le code actuellement.

**Conclusion** : la tâche `0.A — Jeu de données de test (seed)` est confirmée comme **absente**.

### 3.2 0.B — Design system réel
- `package.json`, `tailwind.config.js`, `postcss.config.js`, `vite.config.js`, `webpack.mix.js` sont absents du dépôt.
- `assets/js/` est vide.
- `assets/css/` contient uniquement `responsive.css`.
- `templates/layout/` est vide : il n'y a pas de `header.php` ni de `footer.php`.
- Le code front-end ne contient aucune référence à Tailwind, Chart.js, Alpine.js, SweetAlert2, Notyf, Flatpickr, Tom Select, SheetJS ou PhpSpreadsheet.
- `README.md` indique un dossier de seeds, mais rien quant au build front-end ou à un système de design Tailwind.

**Conclusion** : la tâche `0.B — Design system réel` est confirmée comme **absente** et le dépôt ne contient pas les configurations front-end structurelles attendues.

## 4. Preuves et éléments vérifiés
- `config/database.php` : connexion PDO MySQL correcte, mais aucun seed automatique.
- `config/constants.php` : constantes définies, pas de système de thème Tailwind.
- `includes/fonctions.php` : utilitaires simples, pas de génération de données structurées ni de seed.
- `templates/layout/` : dossier vide, manque le layout commun.
- `assets/js/` : dossier vide.
- `assets/css/responsive.css` : existe, mais c'est un patch ponctuel, pas un design system complet.
- Recherche globale sur les fichiers : aucune occurrence de `package.json`, `tailwind.config.js`, `Chart.js`, `Alpine`, `SweetAlert2`, `Notyf`, `Flatpickr`, `Tom Select`, `SheetJS`, `PhpSpreadsheet`.

## 5. Impacts immédiats
- Sans seeds, aucune tâche de module ne peut être vérifiée sur des données réelles.
- Sans layout et front-end build, le design system requis par la section 18 du CDC est inexistant.
- Le backlog v3 reste valide dans sa formulation sur les tâches 0.A et 0.B : elles doivent être traitées en premier.

## 6. Recommandations Phase 0
1. Créer immédiatement les scripts de seed dans `database/seeds/` pour :
   - établissement + année scolaire active
   - cycles/niveaux/séries/classes
   - matières/programmes
   - élèves, enseignants, affectations, notes, frais, paiements, absences, sanctions
   - utilisateurs et rôles
   - script de réinitialisation `database/seeds/reset.php`
2. Mettre en place la base front-end :
   - `package.json` + dépendances Tailwind/DaisyUI/Alpine/Chart.js/Flatpickr/Tom Select/SweetAlert2/Notyf/SheetJS
   - `tailwind.config.js` + `postcss.config.js` ou équivalent
   - `templates/layout/header.php` et `templates/layout/footer.php`
   - `assets/js/app.js` avec initialisation du design system
   - `assets/css/app.css` généré depuis Tailwind et les variables de thème
3. Ne pas démarrer d'implémentation métier avant que ces fondations ne soient en place.

## 7. Prochaine étape
- Étendre l'audit aux modules 1–10 en ouvrant chaque fichier de classe, contrôleur, vue et migration concerné.
- Mettre à jour `BACKLOG_v3_COMPLET.md` si des tâches manquantes ou mal formulées sont détectées.
- Identifier les écarts exacts entre la structure déclarée et le comportement réel.

---

*Audit initial réalisé le 17 juillet 2026.*
