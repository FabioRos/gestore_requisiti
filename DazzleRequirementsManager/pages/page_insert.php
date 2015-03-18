<?php
$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= $_SERVER['REQUEST_URI'];
$_SESSION['url_plugin_dir']=dirname(dirname($url));
require_once $_SESSION['url_plugin_dir'].'/class/DB_controller.php';
 //echo $_SESSION['url_plugin_dir']=dirname(dirname($url)).'/class/DB_controller.php';
//echo dirname(dirname($url));
 //$listaRequisiti=$controller_requisiti->get_all();
// var_dump($listaRequisiti);
/* 
 * Inserisco solo il frammento di codice che andrà visualizzato.
 */
?>

   
    
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
    <label for='img1' class="form_item_DW"><strong>Path di partenza:</strong>  <?php echo $_SESSION['url_plugin_dir'].'/images/Requisiti/'; ?> </label>
    
    <?php 
    
    $numero_foto=5;
    
    for ($i=1;$i<=$numero_foto;$i++){?>
    <input type="text" name="titolo_img<?php echo $i; ?>" class=" form_item_DW img_title" placeholder="<Titolo per immagine<?php echo $i; ?>.jpg>" />
    <input type="text" name="img<?php echo $i; ?>" class=" form_item_DW img_path" placeholder="<immagine<?php echo $i; ?>.jpg>" />
    <?php }?>

<!-- 
    INSERIMENTO IMMAGINI

    <label for="image_req" class="form_item_DW">scegli immagine</label>
    <input type="file"  name="image_req" class="form_item_DW"/>
--> 
    
    <!-- <input type="hidden" name="action" value="addCustomer"/> -->
    
   <input type="hidden" name="procedi" value="procedi"/> 
   <input type="hidden" name="base_path_img" value="<?php echo $_SESSION['url_plugin_dir'].'/images/Requisiti/'; ?>"/>
    <input type="submit"  name="submit" id="btn_aggiungi_requisito" class="form_item_DW">
</form>
<?php
//echo $_SESSION['url_plugin_dir'].'/images/Requisiti/';
?>