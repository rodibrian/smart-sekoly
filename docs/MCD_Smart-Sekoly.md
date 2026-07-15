# MCD — SMART-SEKOLY
## Modèle Conceptuel de Données (méthode Merise)

Ce document est dérivé du cahier des charges validé (50 décisions de cadrage, 11 modules fonctionnels). Il liste, dans un ordre unique mais groupé par domaine fonctionnel, toutes les **entités** puis toutes les **associations** (relations) avec leurs cardinalités. Chaque entité ne présente que son identifiant et ses attributs clés (niveau essentiel), conformément à votre choix.

### Légende / notation utilisée

- **Entité** : `NOM_ENTITE` — commentaire — `Id_X` (identifiant), attributs clés.
- **Association** : `ENTITE_A (min,max) —VERBE— (min,max) ENTITE_B`
  - `(0,1)` : optionnel, un seul au plus
  - `(1,1)` : obligatoire, un seul exactement
  - `(0,n)` : optionnel, plusieurs possibles
  - `(1,n)` : obligatoire, plusieurs possibles
- Les associations porteuses de leurs propres attributs (ex. INSCRIPTION, AFFECTATION) sont des entités-associations classiques en Merise ; elles apparaissent dans les deux listes.

---

## A. LISTE DES ENTITÉS

### A.1 — Domaine Personnes, Rôles & Sécurité
*Cœur du système : toute personne physique (élève, enseignant, personnel) repose sur cette entité unique, conformément à la décision de cadrage sur le modèle PERSONNE → RÔLE → PROFIL.*

1. **PERSONNE** — identité commune à tout individu. `Id_Personne`, Nom, Prénom, DateNaissance, Sexe, Téléphone, **Email**, **Adresse**, Photo, **PieceIdentite**.
2. **ROLE** — rôle qu'une personne peut cumuler. `Id_Role`, Libellé (élève, enseignant, parent, directeur, secrétaire, comptable, surveillant, DRH, caissière…).
3. **ELEVE** — profil spécialisé. `Id_Eleve`, Matricule, DateEntrée, StatutScolaire (actif, ancien, transféré, diplômé).
4. **ENSEIGNANT** — profil spécialisé. `Id_Enseignant`, Matricule, DateEmbauche, StatutEnseignant (actif, en congé, sorti).
5. **PERSONNEL_ADMINISTRATIF** — profil spécialisé. `Id_Personnel`, Fonction.
6. **UTILISATEUR** — compte de connexion. `Id_Utilisateur`, Identifiant, MotDePasseHash, DateDerniereConnexion, StatutCompte (actif, verrouillé).
7. **PERMISSION** — droit granulaire. `Id_Permission`, Module, SousModule, Action (créer/lire/modifier/supprimer/exporter/valider).
8. **ACCES_PARENT_ELEVE** — code de consultation externe (décision #22). `Id_Acces`, CodeAcces, DateGeneration, Statut.
9. **JOURNAL_AUDIT** — traçabilité des actions sensibles (décision #26). `Id_Audit`, DateAction, TypeAction, AncienneValeur, NouvelleValeur.
10. **JOURNAL_CONNEXION** — sécurité (section 13.5). `Id_Connexion`, DateConnexion, AdresseIP, Navigateur.

### A.2 — Domaine Cadre temporel & Structure pédagogique
*Toute donnée importante est rattachée à une année scolaire (principe fondamental, section 4.1).*

11. **ANNEE_SCOLAIRE** — cadre de référence. `Id_Annee`, Libellé, DateDebut, DateFin, Etat (préparation/active/clôturée/archivée).
12. **JOUR_CALENDRIER** — jour non ouvré (décision #11). `Id_JourCalendrier`, Date, TypeJour (férié/vacances/journée pédagogique).
13. **CYCLE** — Primaire/Collège/Lycée. `Id_Cycle`, Libellé.
14. **NIVEAU** — ex. CM2, 6ème, Terminale. `Id_Niveau`, Libellé.
15. **SERIE** — ex. OSE (facultatif, lycée). `Id_Serie`, Libellé.
16. **CLASSE** — jamais un texte libre (section 7). `Id_Classe`, Libellé, EffectifMax.
17. **SALLE** — ressource physique (décision #17). `Id_Salle`, Libellé, Capacite.
18. **MATIERE** — `Id_Matiere`, Libellé.
19. **PROGRAMME** — matière rattachée à une classe pour une année (section 7.1). `Id_Programme`, Coefficient, VolumeHoraire, EstObligatoire.

### A.3 — Domaine Scolarité de l'élève
*L'élève n'est jamais rattaché directement à une classe : toujours via une inscription annuelle (section 6).*

20. **INSCRIPTION** — appartenance annuelle élève/classe. `Id_Inscription`, DateInscription, StatutInscription (actif/redoublant/transféré/diplômé).
21. **TRANSFERT** — décision #25. `Id_Transfert`, Motif, DateTransfert, EtablissementOrigineDestination.

### A.4 — Domaine Enseignants & Ressources humaines

22. **CONTRAT** — type de rémunération (section 8.2). `Id_Contrat`, TypeContrat (permanent/forfaitaire/horaire/vacataire/stagiaire/bénévole), DateDebut, DateFin, MontantOuTauxHoraire.
23. **AFFECTATION** — lien enseignant/matière/classe/année (section 8.3). `Id_Affectation`, DateDebut, DateFin.
24. **CONGE** — demande de congé (section 8.4, personnel enseignant et administratif). `Id_Conge`, TypeConge (payé/maladie/formation), DateDebut, DateFin, Statut.
25. **HEURE_SUPPLEMENTAIRE** — section 8.5. `Id_HeureSupp`, Date, NombreHeures, Statut.
26. **SALAIRE** — calcul selon type de contrat (section 11.7). `Id_Salaire`, Periode, MontantCalcule, DatePaiement.

### A.5 — Domaine Emploi du temps

27. **CRENEAU_HORAIRE** — plage horaire (section 9). `Id_Creneau`, JourSemaine, HeureDebut, HeureFin.
28. **EMPLOI_DU_TEMPS** — association classe/enseignant/matière/salle/créneau. `Id_EmploiDuTemps`.
29. **SEANCE_REELLE** — ce qui s'est réellement déroulé (décision #18). `Id_Seance`, Date, Statut (prévu/réalisé/annulé/reporté/remplacé).
30. **AGENDA_EVENEMENT** — agenda partagé de l'établissement (décision #31). `Id_EvenementAgenda`, Titre, DateHeure, Lieu, PublicConcerne.
31. **PLANNING_SURVEILLANT** — décision #50. `Id_PlanningSurveillant`, DateHeure, TypeSurveillance (récréation/étude/permanence).

### A.6 — Domaine Évaluations & Résultats

32. **PERIODE** — trimestre/semestre/bimestre, paramétrable (section 10.2). `Id_Periode`, Libellé, TypePeriode.
33. **EVALUATION** — activité notée (section 10.1). `Id_Evaluation`, Date, Coefficient.
34. **NOTE** — jamais isolée, toujours rattachée à une évaluation. `Id_Note`, Valeur, Appreciation.
35. **BULLETIN** — photographie officielle d'une période (section 10.4). `Id_Bulletin`, MoyenneGenerale, Rang, Decision (admis/redoublement/transfert).

### A.7 — Domaine Vie scolaire & Discipline

36. **ABSENCE** — `Id_Absence`, Date, Justifiee.
37. **RETARD** — `Id_Retard`, Date, DureeMinutes.
38. **SANCTION** — paramétrable (section 10.5). `Id_Sanction`, TypeSanction, NiveauGravite, Motif, Statut (proposée/validée).
39. **INCIDENT** — décision #4/module IX.4. `Id_Incident`, Date, Description, Photos, Temoins.
40. **CARNET_SUIVI** — journal chronologique d'un élève (décision #43), un par élève. `Id_Carnet`.
41. **EVENEMENT_CARNET** — entrée du carnet. `Id_EvenementCarnet`, DateEvenement, TypeEvenement, Description.
42. **BILLET** — entrée/sortie/autorisation (section 11.9). `Id_Billet`, TypeBillet, DateHeure, Motif.

### A.8 — Domaine Finance

43. **TYPE_FRAIS** — paramétrable (section 11.2). `Id_TypeFrais`, Libellé, MontantDefaut.
44. **FACTURE** — dette de l'élève (section 11.3). `Id_Facture`, NumeroSequentiel, DateEmission, MontantTotal.
45. **LIGNE_FACTURE** — détail par type de frais. `Id_LigneFacture`, MontantLigne.
46. **REMISE** — bourse/réduction (décision #13). `Id_Remise`, TypeRemise (pourcentage/montant fixe), ValeurRemise, Motif.
47. **ECHEANCE** — paiement échelonné (décision #23). `Id_Echeance`, DateEcheance, MontantPrevu, StatutEcheance (payée/partielle/en retard).
48. **PAIEMENT** — encaissement réel (section 11.4). `Id_Paiement`, NumeroRecu, DatePaiement, Montant, ModePaiement (espèce/banque/mobile money).
49. **CAISSE** — état financier journalier (section 11.6). `Id_Caisse`, Date, FondDeCaisse.
50. **MOUVEMENT_CAISSE** — entrée/sortie. `Id_Mouvement`, TypeMouvement, Montant.

### A.9 — Domaine Inventaire & Prêts

51. **MATERIEL** — section 11.8. `Id_Materiel`, Libellé, QuantiteStock, SeuilAlerte.
52. **MOUVEMENT_STOCK** — `Id_MouvementStock`, TypeMouvement (entrée/sortie), Quantite, Date.
53. **PRET_MATERIEL** — `Id_Pret`, DateSortie, DateRetourPrevue, DateRetourEffective.

### A.10 — Domaine Documents

54. **DOCUMENT_PERSONNE** — dossier personnel (section 12.2). `Id_DocumentPersonne`, TypeDocument, CheminFichier, DateAjout.
55. **TYPE_DOCUMENT_OBLIGATOIRE** — paramétrable par profil. `Id_TypeDocObligatoire`, Libellé, ProfilConcerne (élève/enseignant).
56. **DOCUMENT_ADMINISTRATIF** — bibliothèque interne (décision #30). `Id_DocAdmin`, Titre, Categorie, PublicVise.
57. **VERSION_DOCUMENT** — historique de versionnage (partagé DOCUMENT_PERSONNE et DOCUMENT_ADMINISTRATIF). `Id_Version`, DateVersion, Auteur, Commentaire.
58. **MODELE_DOCUMENT** — modèles paramétrables (bulletin, reçu, attestation, billet). `Id_Modele`, TypeModele, ContenuParametrable.

### A.11 — Domaine Paramétrage & Configuration

59. **PARAMETRAGE_ETABLISSEMENT** — table unique pour cette version (décision #2). `Id_Parametrage`, NomEtablissement, Logo, Monnaie, LangueParDefaut, ThemeParDefaut, CheminStockageDocuments.
60. **SEQUENCE_NUMEROTATION** — compteurs séquentiels par année (décisions #14, #16). `Id_Sequence`, TypeDocument, AnneeScolaire, DernierNumero, Format.
61. **SEUIL_ALERTE** — seuils configurables (notes, absences). `Id_Seuil`, TypeSeuil, ValeurSeuil.

### A.12 — Domaine Communication & Services annexes

62. **MESSAGE** — messagerie interne (module V.1). `Id_Message`, DateEnvoi, Contenu.
63. **ANNONCE** — annonces collectives (module V.2). `Id_Annonce`, Titre, Contenu, DatePublication.
64. **REPAS** — module cantine (décision #45). `Id_Repas`, DateRepas, Menu.
65. **RESERVATION_REPAS** — `Id_Reservation`, StatutReservation.
66. **EXAMEN_BLANC** — décision #47. `Id_ExamenBlanc`, Libellé, Date.
67. **RESULTAT_EXAMEN_BLANC** — `Id_ResultatExamen`, Note.

---

## B. LISTE DES ASSOCIATIONS

### B.1 — Personnes, Rôles & Sécurité

- `PERSONNE (1,1) —EST_UN— (0,n) ROLE` *(association porteuse : PERSONNE_ROLE, permet le cumul de rôles)*
- `PERSONNE (1,1) —SE_SPECIALISE_EN— (0,1) ELEVE` *(héritage exclusif)*
- `PERSONNE (1,1) —SE_SPECIALISE_EN— (0,1) ENSEIGNANT`
- `PERSONNE (1,1) —SE_SPECIALISE_EN— (0,1) PERSONNEL_ADMINISTRATIF`
- `PERSONNE (0,1) —POSSEDE— (1,1) UTILISATEUR`
- `ROLE (1,n) —ATTRIBUE— (0,n) PERMISSION`
- `ELEVE (1,1) —DISPOSE_DE— (0,n) ACCES_PARENT_ELEVE`
- `UTILISATEUR (1,1) —GENERE— (0,n) JOURNAL_AUDIT`
- `UTILISATEUR (1,1) —GENERE— (0,n) JOURNAL_CONNEXION`

### B.2 — Cadre temporel & Structure pédagogique

- `NIVEAU (1,1) —APPARTIENT_A— (1,n) CYCLE`
- `CLASSE (1,1) —RELEVE_DE— (1,n) NIVEAU`
- `CLASSE (0,1) —RATTACHEE_A— (0,n) SERIE`
- `ANNEE_SCOLAIRE (1,1) —DEFINIT— (1,n) JOUR_CALENDRIER`
- `CLASSE (1,1) —COMPORTE— (0,n) PROGRAMME`
- `PROGRAMME (0,n) —PORTE_SUR— (1,1) MATIERE`

### B.3 — Scolarité de l'élève

- `ELEVE (1,1) —FAIT— (1,n) INSCRIPTION`
- `INSCRIPTION (0,n) —CONCERNE— (1,1) CLASSE`
- `INSCRIPTION (0,n) —RATTACHEE_A— (1,1) ANNEE_SCOLAIRE`
- `INSCRIPTION (0,1) —DONNE_LIEU_A— (0,1) TRANSFERT`

### B.4 — Enseignants & RH

- `ENSEIGNANT (1,1) —SIGNE— (1,n) CONTRAT` *(historique des contrats successifs)*
- `ENSEIGNANT (1,1) —FAIT_OBJET_DE— (0,n) AFFECTATION`
- `AFFECTATION (0,n) —PORTE_SUR— (1,1) MATIERE`
- `AFFECTATION (0,n) —CONCERNE— (1,1) CLASSE`
- `AFFECTATION (0,n) —RATTACHEE_A— (1,1) ANNEE_SCOLAIRE`
- `PERSONNE (1,1) —DEMANDE— (0,n) CONGE` *(enseignant ou personnel administratif)*
- `ENSEIGNANT (1,1) —EFFECTUE— (0,n) HEURE_SUPPLEMENTAIRE`
- `ENSEIGNANT (1,1) —PERCOIT— (0,n) SALAIRE`

### B.5 — Emploi du temps

- `EMPLOI_DU_TEMPS (0,n) —CONCERNE— (1,1) CLASSE`
- `EMPLOI_DU_TEMPS (0,n) —ASSURE_PAR— (1,1) ENSEIGNANT`
- `EMPLOI_DU_TEMPS (0,n) —PORTE_SUR— (1,1) MATIERE`
- `EMPLOI_DU_TEMPS (0,n) —OCCUPE— (1,1) SALLE`
- `EMPLOI_DU_TEMPS (0,n) —SUR— (1,1) CRENEAU_HORAIRE`
- `EMPLOI_DU_TEMPS (1,1) —GENERE— (0,n) SEANCE_REELLE`
- `SEANCE_REELLE (0,1) —REMPLACEE_PAR— (0,1) ENSEIGNANT` *(enseignant remplaçant ponctuel, décision #18)*
- `AGENDA_EVENEMENT (0,n) —CONCERNE— (0,1) CLASSE`
- `UTILISATEUR (1,1) —ASSURE— (0,n) PLANNING_SURVEILLANT`

### B.6 — Évaluations & Résultats

- `ANNEE_SCOLAIRE (1,1) —DECOUPEE_EN— (1,n) PERIODE`
- `MATIERE (1,1) —FAIT_OBJET_DE— (0,n) EVALUATION`
- `EVALUATION (0,n) —CONCERNE— (1,1) CLASSE`
- `EVALUATION (0,n) —SITUEE_DANS— (1,1) PERIODE`
- `EVALUATION (0,n) —REALISEE_PAR— (1,1) ENSEIGNANT`
- `ELEVE (1,1) —OBTIENT— (0,n) NOTE`
- `NOTE (0,n) —RATTACHEE_A— (1,1) EVALUATION`
- `ELEVE (1,1) —RECOIT— (0,n) BULLETIN`
- `BULLETIN (0,n) —CORRESPOND_A— (1,1) PERIODE`
- `BULLETIN (0,n) —ISSU_DE— (1,1) INSCRIPTION`

### B.7 — Vie scolaire & Discipline

- `ELEVE (1,1) —SUBIT— (0,n) ABSENCE`
- `ELEVE (1,1) —SUBIT— (0,n) RETARD`
- `ELEVE (1,1) —FAIT_OBJET_DE— (0,n) SANCTION`
- `SANCTION (0,n) —VALIDEE_PAR— (1,1) UTILISATEUR`
- `INCIDENT (0,n) —IMPLIQUE— (1,n) ELEVE` *(association many-to-many : un incident peut impliquer plusieurs élèves)*
- `ELEVE (1,1) —POSSEDE— (1,1) CARNET_SUIVI`
- `CARNET_SUIVI (1,1) —CONTIENT— (0,n) EVENEMENT_CARNET`
- `ELEVE (1,1) —RECOIT— (0,n) BILLET`

### B.8 — Finance

- `ELEVE (1,1) —EST_FACTURE_PAR— (0,n) FACTURE`
- `FACTURE (1,n) —DETAILLEE_PAR— (1,1) LIGNE_FACTURE`
- `LIGNE_FACTURE (0,n) —PORTE_SUR— (1,1) TYPE_FRAIS`
- `FACTURE (0,n) —BENEFICIE_DE— (0,n) REMISE`
- `REMISE (0,n) —VALIDEE_PAR— (1,1) UTILISATEUR`
- `FACTURE (1,1) —DECOUPEE_EN— (1,n) ECHEANCE`
- `ECHEANCE (1,1) —REGLEE_PAR— (0,n) PAIEMENT`
- `PAIEMENT (0,n) —ENREGISTRE_PAR— (1,1) UTILISATEUR`
- `PAIEMENT (0,n) —ENCAISSE_DANS— (1,1) CAISSE`
- `CAISSE (1,1) —GENERE— (0,n) MOUVEMENT_CAISSE`

### B.9 — Inventaire & Prêts

- `MATERIEL (1,1) —SUBIT— (0,n) MOUVEMENT_STOCK`
- `MATERIEL (1,1) —FAIT_OBJET_DE— (0,n) PRET_MATERIEL`
- `PRET_MATERIEL (0,n) —EMPRUNTE_PAR— (1,1) PERSONNE` *(élève ou enseignant)*

### B.10 — Documents

- `PERSONNE (1,1) —POSSEDE— (0,n) DOCUMENT_PERSONNE`
- `DOCUMENT_PERSONNE (0,n) —CORRESPOND_A— (0,1) TYPE_DOCUMENT_OBLIGATOIRE`
- `DOCUMENT_PERSONNE (1,1) —HISTORISE_PAR— (0,n) VERSION_DOCUMENT`
- `DOCUMENT_ADMINISTRATIF (1,1) —HISTORISE_PAR— (0,n) VERSION_DOCUMENT`
- `PARAMETRAGE_ETABLISSEMENT (1,1) —DEFINIT— (0,n) MODELE_DOCUMENT`

### B.11 — Paramétrage

- `PARAMETRAGE_ETABLISSEMENT (1,1) —DEFINIT— (1,n) SEQUENCE_NUMEROTATION`
- `PARAMETRAGE_ETABLISSEMENT (1,1) —DEFINIT— (1,n) SEUIL_ALERTE`

### B.12 — Communication & Services annexes

- `UTILISATEUR (1,1) —ENVOIE— (0,n) MESSAGE`
- `UTILISATEUR (1,1) —PUBLIE— (0,n) ANNONCE`
- `ANNONCE (0,n) —TRACEE_DANS— (0,n) EVENEMENT_CARNET`
- `ELEVE (0,n) —RESERVE— (0,n) REPAS` *(association porteuse : RESERVATION_REPAS)*
- `ELEVE (0,n) —PARTICIPE_A— (0,n) EXAMEN_BLANC` *(association porteuse : RESULTAT_EXAMEN_BLANC)*

---

## C. SCHÉMA DE SYNTHÈSE (chaînes structurantes)

```
PERSONNE ─┬→ RÔLE ─→ PERMISSION
          ├→ ELEVE ─→ INSCRIPTION ─→ CLASSE ─→ NIVEAU ─→ CYCLE
          │              │             └→ ANNEE_SCOLAIRE ─→ PERIODE ─→ CALENDRIER
          │              ├→ FACTURE ─→ LIGNE_FACTURE ─→ TYPE_FRAIS
          │              │      └→ ECHEANCE ─→ PAIEMENT ─→ CAISSE
          │              ├→ NOTE ─→ EVALUATION ─→ MATIERE
          │              ├→ BULLETIN
          │              ├→ CARNET_SUIVI ─→ EVENEMENT_CARNET
          │              └→ ABSENCE / RETARD / SANCTION / BILLET
          ├→ ENSEIGNANT ─→ CONTRAT
          │              ├→ AFFECTATION ─→ MATIERE / CLASSE / ANNEE_SCOLAIRE
          │              ├→ EMPLOI_DU_TEMPS ─→ SALLE / CRENEAU / SEANCE_REELLE
          │              ├→ CONGE / HEURE_SUPPLEMENTAIRE / SALAIRE
          │              └→ EVALUATION (saisie des notes)
          ├→ PERSONNEL_ADMINISTRATIF ─→ CONGE
          ├→ UTILISATEUR ─→ JOURNAL_AUDIT / JOURNAL_CONNEXION
          └→ DOCUMENT_PERSONNE ─→ VERSION_DOCUMENT

PARAMETRAGE_ETABLISSEMENT ─→ SEQUENCE_NUMEROTATION / SEUIL_ALERTE / MODELE_DOCUMENT
```

---

## D. Points de vigilance à valider avec vous avant le passage au schéma physique MySQL

1. **CONGE** est rattaché à `PERSONNE` (et non uniquement `ENSEIGNANT`) puisque le personnel administratif en bénéficie aussi (section 8.4). À confirmer.
2. **INCIDENT ↔ ELEVE** et **ELEVE ↔ REPAS/EXAMEN_BLANC** sont des associations plusieurs-à-plusieurs : elles nécessiteront chacune une table de liaison au niveau physique.
3. **REMISE ↔ FACTURE** : le CDC ne précise pas si une remise peut s'appliquer à plusieurs factures (ex. remise fratrie récurrente) ou est ressaisie à chaque facture — actuellement modélisé en (0,n)–(0,n) par prudence.
4. **CARNET_SUIVI** est modélisé comme une entité à part (1,1 avec ELEVE) plutôt que fusionné dans ELEVE, pour isoler le journal chronologique du profil ; une fusion est possible si vous préférez simplifier.
5. **BILLET, SANCTION, INCIDENT** génèrent chacun une entrée dans `EVENEMENT_CARNET` — ce lien n'est pas détaillé en association séparée ici pour ne pas alourdir la liste ; à clarifier au MPD (clé étrangère polymorphe ou table de jonction par type d'événement).

---

*MCD conceptuel — 67 entités, groupées en 12 domaines — prêt pour transformation en schéma physique MySQL (MPD).*
