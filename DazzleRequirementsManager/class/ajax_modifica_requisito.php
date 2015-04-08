<?php
require_once('../../../../wp-load.php');
include_once 'controller_requisito.php';

$class_soddisfatto="requisito_soddisfatto";
$sodd_value=1;
if($_POST['soddisfatto']=='No'){
    $class_soddisfatto="requisito_non_soddisfatto";
    $sodd_value=0;
}

$controller = new controller_requisito();
$controller -> aggiornaReq($_POST['id'],$_POST['tipo'],$_POST['importanza'],$_POST['descrizione'],$sodd_value);

echo "<td id='idReq{$_POST['indice']}'> {$_POST['id']} </td>";
echo "<td id='Tipo{$_POST['indice']}'> {$_POST['tipo']} </td>";
echo "<td id='Importanza{$_POST['indice']}'> {$_POST['importanza']} </td>";
echo "<td id='Descrizione{$_POST['indice']}'> {$_POST['descrizione']} </td>";
echo "<td id='Soddisfatto{$_POST['indice']}' class='{$class_soddisfatto}'> {$sodd_value} </td>";
?>