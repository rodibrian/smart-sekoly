<?php
/**
 * DAO simplifié pour la persistance des élèves.
 */
class EleveDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function creerEleve(array $donnees): int
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

                $stmtEleve = $this->pdo->prepare('INSERT INTO eleve (id_personne, matricule, date_entree, statut_scolaire, date_creation, date_modification) VALUES (:id_personne, :matricule, :date_entree, :statut_scolaire, NOW(), NOW())');
                $stmtEleve->execute([
                    ':id_personne' => $idPersonne,
                    ':matricule' => $donnees['matricule'],
                    ':date_entree' => $donnees['date_entree'] ?? date('Y-m-d'),
                    ':statut_scolaire' => $donnees['statut_scolaire'] ?? 'actif',
                ]);

                $this->pdo->commit();
                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                error_log('EleveDAO creerEleve failed: ' . $exception->getMessage());
            }
        }

        $eleves = $_SESSION['eleves'] ?? [];
        $id = generer_identifiant($eleves, 'id');
        $eleves[$id] = array_merge(['id' => $id], $donnees);
        $_SESSION['eleves'] = $eleves;

        return $id;
    }

    public function listerEleves(): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->query('SELECT p.id_personne AS id, p.nom, p.prenom, p.email, p.date_naissance, e.matricule, e.statut_scolaire AS statut FROM personne p JOIN eleve e ON e.id_personne = p.id_personne ORDER BY p.nom, p.prenom');
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('EleveDAO listerEleves failed: ' . $exception->getMessage());
            }
        }

        return $_SESSION['eleves'] ?? [];
    }

    public function trouverParId(int $id): ?array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT p.id_personne AS id, p.nom, p.prenom, p.email, p.date_naissance, e.matricule, e.statut_scolaire AS statut FROM personne p JOIN eleve e ON e.id_personne = p.id_personne WHERE p.id_personne = :id');
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();
                return $row ?: null;
            } catch (Throwable $exception) {
                error_log('EleveDAO trouverParId failed: ' . $exception->getMessage());
            }
        }

        $eleves = $_SESSION['eleves'] ?? [];
        return $eleves[$id] ?? null;
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

                $stmtEleve = $this->pdo->prepare('UPDATE eleve SET matricule = :matricule, statut_scolaire = :statut_scolaire, date_modification = NOW() WHERE id_personne = :id');
                $stmtEleve->execute([
                    ':matricule' => $donnees['matricule'],
                    ':statut_scolaire' => $donnees['statut_scolaire'] ?? 'actif',
                    ':id' => $id,
                ]);

                $this->pdo->commit();
                return true;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                error_log('EleveDAO mettreAJour failed: ' . $exception->getMessage());
            }
        }

        $eleves = $_SESSION['eleves'] ?? [];
        if (!isset($eleves[$id])) {
            return false;
        }

        $eleves[$id] = array_merge($eleves[$id], $donnees);
        $_SESSION['eleves'] = $eleves;

        return true;
    }
}
