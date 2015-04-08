<?php
require_once('../../../../wp-load.php');
include_once 'controller_requisito.php';



echo "<td> {$_POST['id']} </td>";
echo "<td> {$_POST['tipo']} </td>";
echo "<td> {$_POST['importanza']} </td>";
echo "<td> {$_POST['descrizione']} </td>";
echo "<td> {$_POST['soddisfatto']} </td>";
?>