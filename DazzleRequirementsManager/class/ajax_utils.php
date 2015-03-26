<?php
require_once('../../../../wp-load.php');

$array= array();
//print_r($_GET);
include_once 'DB_controller.php';
if(isset($_GET['function_name']) && $_GET['function_name']=='ajax_get_images_url' &&
   isset($_GET['id_req'])){
    $id_Req=trim($_GET['id_req']);
    $array= ajax_get_images_url_and_txt($id_Req);
    
    foreach ($array as $item)
        echo "<p class='titolo_img_uc'> &#9733; ".$item->Titolo."</p><img class='img_uc' src='".$item->Path."' />";
    //return $array;
    //var_dump($array);
    
   }
//return $array;
//$array= ajax_get_images_url('4');
    


//************************************
function ajax_get_images_url($id_Req){
    //echo 'ci sono';
    $cntrl_db= new DB_controller();
    return  $cntrl_db->get_img_from_ReqID($id_Req);
}

function ajax_get_images_url_and_txt($id_Req){
    //echo 'ci sono';
    $cntrl_db= new DB_controller();
    return  $cntrl_db->get_img_url_and_txt_from_ReqID($id_Req);
}

