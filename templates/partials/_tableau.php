<?php
/**
 * Partiel de tableau réutilisable.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
e
 */
function afficher_tableau_simple(array $colonnes, array $lignes): string
{
    $html = '<table border="1" cellpadding="6" cellspacing="0">';
    $html .= '<thead><tr>';
    foreach ($colonnes as $colonne) {
        $html .= '<th>' . e($colonne) . '</th>';
    }
    $html .= '</tr></thead><tbody>';

    foreach ($lignes as $ligne) {
        $html .= '<tr>';
        foreach ($ligne as $valeur) {
            $html .= '<td>' . e($valeur) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}
