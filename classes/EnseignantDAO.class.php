<?php

class EnseignantDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function creerEnseignant(array $donnees): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $this->pdo->beginTransaction();

                $stmtPersonne = $this->pdo->prepare('INSERT INTO personne (nom, prenom, date_naissance, email, date_creation, date_modification) VALUES (:nom, :prenom, :date_naissance, :email, NOW(), NOW())');
                $stmtPersonne->execute([
                    ':nom' => $donnees['nom'],
                    ':prenom' => $donnees['prenom'],
                    ':date_naissance' => $donnees['date_naissance'] ?? null,
                    ':email' => $donnees['email'] ?? null,
                ]);

                $idPersonne = (int) $this->pdo->lastInsertId();

                $stmtEnseignant = $this->pdo->prepare('INSERT INTO enseignant (id_personne, matricule, date_embauche, statut_enseignant, date_creation, date_modification) VALUES (:id_personne, :matricule, :date_embauche, :statut_enseignant, NOW(), NOW())');
                $stmtEnseignant->execute([
                    ':id_personne' => $idPersonne,
                    ':matricule' => $donnees['matricule'],
                    ':date_embauche' => $donnees['date_embauche'] ?? date('Y-m-d'),
                    ':statut_enseignant' => $donnees['statut_enseignant'] ?? 'actif',
                ]);

                $this->pdo->commit();
                return $idPersonne;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                error_log('EnseignantDAO creerEnseignant failed: ' . $exception->getMessage());
            }
        }

        $enseignants = $_SESSION['enseignants'] ?? [];
        $id = generer_identifiant($enseignants, 'id_personne');
        $enseignants[$id] = array_merge(['id_personne' => $id], $donnees);
        $_SESSION['enseignants'] = $enseignants;

        return $id;
    }

    public function listerEnseignants(): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->query('SELECT p.id_personne AS id, p.nom, p.prenom, p.email, p.date_naissance, e.matricule, e.statut_enseignant AS statut FROM personne p JOIN enseignant e ON e.id_personne = p.id_personne ORDER BY p.nom, p.prenom');
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('EnseignantDAO listerEnseignants failed: ' . $exception->getMessage());
            }
        }

        return array_values($_SESSION['enseignants'] ?? []);
    }

    public function trouverParId(int $id): ?array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT p.id_personne AS id, p.nom, p.prenom, p.email, p.date_naissance, e.matricule, e.date_embauche, e.statut_enseignant AS statut FROM personne p JOIN enseignant e ON e.id_personne = p.id_personne WHERE p.id_personne = :id');
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();
                return $row ?: null;
            } catch (Throwable $exception) {
                error_log('EnseignantDAO trouverParId failed: ' . $exception->getMessage());
            }
        }

        $enseignants = $_SESSION['enseignants'] ?? [];
        return $enseignants[$id] ?? null;
    }

    public function mettreAJour(int $id, array $donnees): bool
    {
        if ($this->pdo instanceof PDO) {
            try {
                $this->pdo->beginTransaction();
                $stmtPersonne = $this->pdo->prepare('UPDATE personne SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, email = :email, date_modification = NOW() WHERE id_personne = :id');
                $stmtPersonne->execute([
                    ':nom' => $donnees['nom'],
                    ':prenom' => $donnees['prenom'],
                    ':date_naissance' => $donnees['date_naissance'] ?? null,
                    ':email' => $donnees['email'] ?? null,
                    ':id' => $id,
                ]);

                $stmtEnseignant = $this->pdo->prepare('UPDATE enseignant SET matricule = :matricule, date_embauche = :date_embauche, statut_enseignant = :statut_enseignant, date_modification = NOW() WHERE id_personne = :id');
                $stmtEnseignant->execute([
                    ':matricule' => $donnees['matricule'],
                    ':date_embauche' => $donnees['date_embauche'] ?? date('Y-m-d'),
                    ':statut_enseignant' => $donnees['statut_enseignant'] ?? 'actif',
                    ':id' => $id,
                ]);

                $this->pdo->commit();
                return true;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                error_log('EnseignantDAO mettreAJour failed: ' . $exception->getMessage());
            }
        }

        $enseignants = $_SESSION['enseignants'] ?? [];
        if (!isset($enseignants[$id])) {
            return false;
        }

        $enseignants[$id] = array_merge($enseignants[$id], $donnees);
        $_SESSION['enseignants'] = $enseignants;
        return true;
    }
}
