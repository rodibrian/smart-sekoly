<?php
/**
 * Point d'entrée unique de l'application Smart-Sekoly.
 *
 * @package Smart-Sekoly
 * @subpackage FrontController
 * @author Baia Creative Solutions
 * @version 1.0
 */
declare(strict_types=1);

session_start();

define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
define('CLASSES_PATH', ROOT_PATH . 'classes' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers' . DIRECTORY_SEPARATOR);
define('MODULES_PATH', ROOT_PATH . 'modules' . DIRECTORY_SEPARATOR);
define('TEMPLATES_PATH', ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);
define('INCLUDES_PATH', ROOT_PATH . 'includes' . DIRECTORY_SEPARATOR);
define('ASSETS_PATH', ROOT_PATH . 'assets' . DIRECTORY_SEPARATOR);
define('LOGS_PATH', ROOT_PATH . 'logs' . DIRECTORY_SEPARATOR);
define('DOCUMENTS_PATH', ROOT_PATH . 'documents' . DIRECTORY_SEPARATOR);

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', LOGS_PATH . 'php-errors.log');

require_once CONFIG_PATH . 'constants.php';
require_once CONFIG_PATH . 'database.php';
require_once INCLUDES_PATH . 'fonctions.php';

spl_autoload_register(function (string $nom_classe): void {
    $candidats = [];

    if (substr($nom_classe, -10) === 'Controller') {
        $candidats[] = substr($nom_classe, 0, -10);
    }

    $candidats[] = $nom_classe;

    foreach ($candidats as $candidat) {
        $fichiers = [
            CLASSES_PATH . $candidat . '.class.php',
            CLASSES_PATH . $candidat . '.php',
            CONTROLLERS_PATH . $candidat . '.controller.php',
            CONTROLLERS_PATH . $candidat . '.php',
        ];

        foreach ($fichiers as $fichier) {
            if (is_file($fichier)) {
                require_once $fichier;
                return;
            }
        }
    }
});

class Routeur
{
    public static function traiter(): void
    {
        // Vérifier d'abord les paramètres GET
        $module = $_GET['module'] ?? null;
        $action = $_GET['action'] ?? null;
        $parametre = $_GET['parametre'] ?? null;

        // Si pas de module en GET, utiliser l'URI
        if ($module === null) {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $uri = strtok($uri, '?');
            $base_url = rtrim(BASE_URL, '/');
            $chemin = $uri;

            if ($base_url !== '') {
                while (strpos($chemin, $base_url) === 0) {
                    $chemin = substr($chemin, strlen($base_url));
                }
            }

            $segments = array_values(array_filter(explode('/', trim($chemin, '/')), function ($segment): bool {
                return $segment !== '';
            }));

            $module = $segments[0] ?? 'installation';
            $action = $segments[1] ?? 'index';
            $parametre = $segments[2] ?? null;
        } else {
            // Par défaut, action est 'index' si non spécifiée
            $action = $action ?? 'index';
        }

        $mots_module = explode('-', $module);
        $module_pascal = implode('', array_map('ucfirst', $mots_module));
        $module_pascal_singulier = implode('', array_map(function ($mot): string {
            return ucfirst(rtrim($mot, 's'));
        }, $mots_module));

        $nom_controleur = $module_pascal . 'Controller';
        $nom_controleur_singulier = $module_pascal_singulier . 'Controller';
        $nom_base = $module_pascal_singulier;

        $fichiers_controleur = [
            CONTROLLERS_PATH . $nom_controleur . '.php',
            CONTROLLERS_PATH . $nom_controleur . '.controller.php',
            CONTROLLERS_PATH . str_replace('Controller', '', $nom_controleur) . '.controller.php',
            CONTROLLERS_PATH . $nom_controleur_singulier . '.php',
            CONTROLLERS_PATH . $nom_controleur_singulier . '.controller.php',
            CONTROLLERS_PATH . str_replace('Controller', '', $nom_controleur_singulier) . '.controller.php',
            CONTROLLERS_PATH . $nom_base . '.php',
            CONTROLLERS_PATH . $nom_base . '.controller.php',
        ];

        foreach ($fichiers_controleur as $fichier_controleur) {
            if (is_file($fichier_controleur)) {
                require_once $fichier_controleur;
                break;
            }
        }

        if (!class_exists($nom_controleur_singulier) && !class_exists($nom_controleur)) {
            $nom_controleur = 'InstallationController';
            $module = 'installation';
            $action = 'index';
            $fichiers_controleur = [
                CONTROLLERS_PATH . $nom_controleur . '.php',
                CONTROLLERS_PATH . $nom_controleur . '.controller.php',
            ];

            foreach ($fichiers_controleur as $fichier_controleur) {
                if (is_file($fichier_controleur)) {
                    require_once $fichier_controleur;
                    break;
                }
            }
        }

        if (!class_exists($nom_controleur) && class_exists($nom_controleur_singulier)) {
            $nom_controleur = $nom_controleur_singulier;
        } elseif (!class_exists($nom_controleur)) {
            $nom_controleur = 'InstallationController';
            $module = 'installation';
            $action = 'index';
        }

        $controleur = new $nom_controleur($module, $action, $parametre);
        $controleur->executer();
    }
}

Routeur::traiter();
