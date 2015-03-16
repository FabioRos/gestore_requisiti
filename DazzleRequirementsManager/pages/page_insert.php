<?php

/* 
 * Inserisco solo il frammento di codice che andrà visualizzato.
 */
?>
<form method="POST" action="http://localhost/roomrose/wp-admin/admin.php?page=inserimento_slug" id="nuovoRequisito">

    <label for="descrizione" class="form_item_DW">Descrizione:</label>
    <textarea class="form_item_DW" rows="4" cols="50" name="descrizione" id="descrizione" placeholder="Scrivi qui la descrizione del requisito."></textarea>

    <label  class="form_item_DW" for="importanza">Importanza:</label>
    <select class="form_item_DW"  name="importanza" id="importanza">
        <option value="0">Obbligatorio</option>
        <option value="1">Desiderabile</option>
        <option value="2">Opzionale</option>
    </select>
    <label  class="form_item_DW" for="tipo">Tipo:</label>
   <select class="form_item_DW"  name="tipo" id="tipo">
        <option value="F">Funzionale</option>
        <option value="Q">Qualitativo</option>
        <option value="D">Prestazionale</option>
    </select>

    <!--
    <label class="form_item_DW"  for="phone">Phone:</label>
    <input class="form_item_DW"  name="phone" type="text" />
    -->


    <input type="hidden" name="action" value="addCustomer"/>
    <input type="submit" id="btn_aggiungi_requisito" class="form_item_DW">
</form>
<?php ?>