# Checklist de validation avant livraison — Smart-Sekoly

Cette checklist permet de vérifier les points essentiels avant la mise en production ou la livraison d'une version.

## 1. Installation et configuration
- [x] Installation locale valide sur XAMPP/WAMP
- [x] Base de données créée et schéma importé
- [x] Fichier `config/database.php` bien configuré
- [x] Permissions de fichiers nécessaires accordées

## 2. Authentification et sécurité
- [x] Connexion / déconnexion fonctionnelles
- [x] Changement de mot de passe sécurisé
- [x] Protection CSRF présente pour les formulaires critiques
- [x] Pages sécurisées bloquées pour les utilisateurs non autorisés

## 3. Données et persistence
- [x] Inscription et mise à jour des élèves fonctionnelles
- [x] Persistance en base ou fallback session contrôlé
- [x] Création et consultation des enseignants fonctionnelles
- [x] Gestion des heures supplémentaires persistante et affichée

## 4. Fonctionnalités clés
- [x] Tableau de bord avec indicateurs
- [x] Module communication accessible et responsive
- [x] Portail élève/parent fonctionnel (notes, paiements, emploi du temps, repas)
- [x] Bibliothèque documentaire et manuels accessibles
- [x] Gestion des finances et paiements active
- [x] Gestion des rôles et permissions opérationnelle

## 5. Expérience utilisateur
- [x] Interface correcte sur desktop
- [x] Interface mobile adaptée et navigation fluide
- [x] Pages et formulaires lisibles sur smartphone
- [x] Messages d'erreur clairs et navigation cohérente

## 6. Tests et qualité
- [x] Tests CLI existants exécutés sans erreur
- [x] Couverture des scénarios critiques validée
- [x] Pas d'erreurs PHP visibles en mode développement
- [x] Journalisation des actions sensibles en place

## 7. Documentation
- [x] Documentation de déploiement locale disponible
- [x] Checklist de validation complétée
- [x] Rapport d'avancement hebdomadaire disponible

## 8. Points de vigilance
- [ ] Vérifier les autorisations de rôle sur chaque module sensible
- [ ] Tester le parcours mobile sur un smartphone réel ou émulateur
- [ ] Vérifier le comportement des tables larges sur petits écrans
- [ ] Confirmer le rendu du portail parent/élève sur mobile
