<?php
/** @var array $donnees */
?>
<style>
/* Impression : cacher éléments non essentiels et optimiser largeur */
@media print {
    body * { visibility: hidden; }
    .recu-print-area, .recu-print-area * { visibility: visible; }
    .recu-print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 0; margin: 0; }
    .no-print { display: none !important; }
}
.recu-print-area { font-family: monospace; white-space: pre; border:1px solid #ccc; padding:12px; max-width:420px; }
</style>

<div class="recu-print-area">
    <?php echo htmlspecialchars($donnees['recu_text'] ?? ''); ?>
</div>
<p class="no-print">
    <button type="button" onclick="window.print();">Imprimer</button>
    &nbsp;|&nbsp;
    <a href="<?php echo BASE_URL . '/' . $donnees['module'] . '/recu/' . ($donnees['paiement_raw']['id_paiement'] ?? $donnees['paiement_raw']['id'] ?? ''); ?>?download=1">Télécharger le reçu (.txt)</a>
    &nbsp;|&nbsp;
    <button type="button" onclick="openPreviewPopup();">Ouvrir dans une nouvelle fenêtre</button>
</p>

<p class="no-print">
    <a href="<?php echo BASE_URL . '/' . $donnees['module'] . '/recu/' . ($donnees['paiement_raw']['id_paiement'] ?? $donnees['paiement_raw']['id'] ?? '') . '?format=escpos'; ?>">Télécharger ESC/POS (.escpos)</a>
</p>

<script>
function openPreviewPopup() {
    var content = document.querySelector('.recu-print-area').innerText;
    var w = 480, h = 640;
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    var popup = window.open('', '_blank', 'toolbar=0,location=0,menubar=0,width='+w+',height='+h+',top='+top+',left='+left);
    if (!popup) { alert('Impossible d\'ouvrir la fenêtre. Autorisez les popups.'); return; }
    var html = '<!doctype html><html><head><meta charset="utf-8"><title>Aperçu reçu</title>'+
        '<style>body{font-family:monospace;padding:12px;} @media print{button{display:none}}</style>'+
        '</head><body>'+
        '<pre>'+ escapeHtml(content) +'</pre>'+
        '<p><button onclick="window.print();">Imprimer</button></p>'+
        '</body></html>';
    popup.document.open();
    popup.document.write(html);
    popup.document.close();
}
function escapeHtml(str){
    return str.replace(/&/g, '&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
