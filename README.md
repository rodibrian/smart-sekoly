# SMART-SEKOLY
## Logiciel de Gestion Scolaire — Version Locale

Solution complète de gestion administrative, pédagogique et financière pour les établissements scolaires (Primaire, Collège, Lycée) à Madagascar.

---

## 📋 DESCRIPTION

Smart-Sekoly est un logiciel de gestion scolaire développé par Baia Creative Solutions. Il couvre l'intégralité du cycle de vie d'un élève : inscription, scolarité, évaluation, passage de classe, jusqu'à la sortie.

**Caractéristiques principales :**
- Paramétrable sans modification du code source
- Fonctionnement en réseau local (LAN)
- Un établissement par installation
- Couverture des cycles primaire, collège et lycée

---

## 🚀 PRÉREQUIS

| Composant | Version minimale |
|-----------|------------------|
| PHP | 7.4 ou supérieur |
| MySQL | 5.7 ou supérieur |
| Apache | 2.4 ou supérieur |
| Navigateur | Firefox 78+, Chrome 80+, Edge Chromium, Safari 13+ |

---

## 📁 STRUCTURE DU PROJET

```
smart-sekoly/
├── index.php               # Point d'entrée unique
├── config/                 # Fichiers de configuration
│   ├── database.php        # Connexion MySQL
│   └── constants.php       # Constantes générales
├── classes/                # Classes PHP métier
│   ├── Eleve.class.php
│   ├── Enseignant.class.php
│   └── ...
├── controllers/            # Contrôleurs MVC
│   ├── Eleve.controller.php
│   └── ...
├── modules/                # Modules fonctionnels
│   ├── parametrage/
│   ├── eleves/
│   └── ...
├── templates/              # Vues et templates
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
│   └── partials/
├── assets/                 # Ressources statiques
│   ├── css/
│   ├── js/
│   └── images/
├── database/               # Scripts SQL
│   ├── migrations/
│   └── seeds/
├── includes/               # Fonctions utilitaires
├── documents/              # Dossiers personnels (paramétrable)
│   ├── eleves/
│   ├── enseignants/
│   └── etablissement/
├── logs/                   # Journaux d'erreurs
├── docs/                   # Documentation
│   ├── Cahier_Des_Charges.md
│   ├── MCD_Smart-Sekoly.md
│   └── Guide_Developpement_IA_Cursor.md
├── BACKLOG.md              # Suivi des tâches
├── CHANGELOG.md            # Décisions techniques
└── README.md               # Ce fichier
```

---

## 🛠️ INSTALLATION

### 1. Cloner ou télécharger le projet

```bash
git clone https://github.com/baia-creative/smart-sekoly.git
cd smart-sekoly
```

### 2. Configurer la base de données

- Démarrer XAMPP/WAMP
- Créer la base de données :

```sql
CREATE DATABASE smart_sekoly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

- Importer le schéma :

```bash
mysql -u root -p smart_sekoly < database/schema_smart_sekoly.sql
```

### 3. Configurer l'application

Copier et modifier le fichier de configuration :

```bash
cp config/database.example.php config/database.php
```

Adapter les paramètres de connexion :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_sekoly');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Droits d'écriture

Donner les droits d'écriture sur les dossiers suivants :

```bash
chmod -R 755 documents/
chmod -R 755 logs/
chmod -R 755 cache/
```

### 5. Accéder à l'application

- Ouvrir le navigateur : `http://localhost/smart-sekoly/`
- Identifiants par défaut :
  - Utilisateur : `admin`
  - Mot de passe : `admin` (à changer à la première connexion)

---

## 📦 MODULES FONCTIONNELS

| Module | Description | Priorité |
|--------|-------------|----------|
| VII | Paramétrage et configuration initiale | 1 |
| II | Gestion des élèves | 2 |
| VIII | Import et migration de données | 2 |
| III | Gestion des enseignants | 3 |
| IX | Vie scolaire et discipline | 4 |
| IV | Gestion administrative et financière | 5 |
| I | Tableau de bord | 6 |
| VI | Rapports et statistiques | 6 |
| X | Portails Élève et Parent | 7 |
| V | Communication interne | 8 |
| XI | Bibliothèque documentaire | 8 |

---

## 👥 PROFILS UTILISATEURS

| Profil | Accès |
|--------|-------|
| Directeur | Accès complet à tous les modules |
| Comptable | Module financier uniquement |
| Enseignant | Notes de ses propres classes |
| Secrétaire | Gestion administrative et inscriptions |
| Surveillant | Vie scolaire et carnets de suivi |
| DRH | Ressources humaines et personnel |
| Caissière | Module Caisse uniquement |

---

## 🗂️ DOCUMENTS GÉNÉRÉS

| Document | Format | Utilisation |
|----------|--------|-------------|
| Bulletin scolaire | PDF A4 | Périodique |
| Reçu de paiement | Thermique 80mm / PDF | À chaque paiement |
| Attestation de scolarité | PDF A4 | À la demande |
| Billet d'entrée/sortie | 10x15cm | Vie scolaire |
| Autorisation de sortie | PDF A5 | Vie scolaire |
| Certificat de scolarité | PDF A4 | À la demande |

---

## 🔧 PARAMÉTRAGE INITIAL

Suivre l'ordre recommandé (voir documentation complète) :

1. Informations générales (nom, logo, monnaie)
2. Année scolaire
3. Calendrier scolaire
4. Cycles, niveaux, séries
5. Classes et salles
6. Matières et programmes
7. Périodes d'évaluation
8. Types de frais
9. Types de contrats enseignants
10. Types de sanctions
11. Documents obligatoires
12. Modèles de documents
13. Rôles et permissions
14. Politique de sécurité
15. Sauvegardes

---

## 🧪 TESTS

### Tests unitaires (PHPUnit)

```bash
cd tests
phpunit
```

### Scénarios critiques à valider

| Scénario | Critère de succès |
|----------|-------------------|
| Fin d'année | Archivage réussi, consultation possible |
| Doublon de paiement | Alerte affichée, confirmation requise |
| Congé enseignant | Bloqué sur les affectations |
| Suppression logique | Donnée marquée annulée, traçable |
| Note → Bulletin | Génération cohérente |

---

## 📊 BASE DE DONNÉES

### Schéma principal

```
PERSONNE → RÔLE → PROFIL
ÉLÈVE → INSCRIPTION → CLASSE → ANNÉE SCOLAIRE
ENSEIGNANT → CONTRAT → AFFECTATION → EMPLOI DU TEMPS
MATIÈRE → ÉVALUATION → NOTE → BULLETIN
ÉLÈVE → FACTURE → PAIEMENT → CAISSE
ÉLÈVE → CARNET_DE_SUIVI → ÉVÉNEMENT
```

### Conventions

- Moteur : InnoDB
- Charset : utf8mb4
- Toute table métier : `date_creation` et `date_modification`
- Suppression logique uniquement (statut 'annulé' + audit)
- Aucune valeur métier codée en dur

---

## 🔒 SÉCURITÉ

- Mots de passe hashés (bcrypt)
- Protection CSRF sur tous les formulaires
- Protection XSS (htmlspecialchars)
- Requêtes SQL préparées (PDO)
- Validation serveur obligatoire
- Journal d'audit pour actions sensibles
- Verrouillage après échecs de connexion

---

## 📝 NORMES DE CODAGE

- PHP orienté objet (POO)
- Noms en français (classes, méthodes, variables, champs SQL)
- PascalCase pour les classes
- snake_case pour les méthodes et variables
- PHPDoc sur chaque classe et méthode publique
- Commentaires en français sur toute logique complexe

---

## 🗃️ SAUVEGARDE

- Sauvegarde automatique quotidienne (paramétrable)
- Dump SQL horodaté
- Fréquence paramétrable : quotidienne, hebdomadaire, mensuelle
- Restauration manuelle possible

---

## 📖 DOCUMENTATION

| Fichier | Description |
|---------|-------------|
| `docs/Cahier_Des_Charges.md` | Spécifications fonctionnelles et techniques |
| `docs/MCD_Smart-Sekoly.md` | Modèle Conceptuel de Données (Merise) |
| `docs/Guide_Developpement_IA_Cursor.md` | Guide pour le développement avec IA |

---

## 🤝 CONTRIBUTION

1. Consulter `BACKLOG.md` pour les tâches disponibles
2. Créer une branche : `git checkout -b module-X`
3. Développer selon les normes de codage
4. Tester et valider selon les critères du CDC
5. Commiter : `git commit -m "[Module X] Description"`
6. Mettre à jour `BACKLOG.md`
7. Demander une revue de code

---

## 📄 LICENCE

Propriétaire — Baia Creative Solutions
Tous droits réservés

---

## 📧 CONTACT

**Baia Creative Solutions**
- Développeur : Baia Creative Solutions
- Email : contact@baia-creative.mg
- Site : https://www.baia-creative.mg

---

*Smart-Sekoly — Juillet 2026 — Version 1.0*