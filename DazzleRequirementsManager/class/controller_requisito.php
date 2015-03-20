<?php

/**
 * @author Fabio Ros
 */
class controller_requisito {

    public function __construct() {
        //$this->inserisci("id", "tipo", 'a', "descrizione");
       // $this->modifica_descrizione("id", "mario");
        //$this->soddisfatto("id", FALSE);
        //$this->rimuovi("id");
    }

    public function get_all() {
        global $wpdb;
        $sql = ("SELECT * FROM " . T_REQUISITO . ";");
        //echo $sql;
        return $wpdb->get_results($sql);
        
    }
    
     public function get_all_Id() {
        global $wpdb;
        $sql = ("SELECT idReq FROM " . T_REQUISITO . ";");
        //echo $sql;
        return $wpdb->get_results($sql);
        
    }
    
    
    public function inserisci($idReq, $tipo, $importanza, $descrizione) {
        global $wpdb;
        $sql = $wpdb->prepare("INSERT INTO " . T_REQUISITO . " VALUES(%s,%s,%d,%s,FALSE);", $idReq, $tipo, $importanza, $descrizione);
       echo "<br />".$sql;
        $wpdb->query($sql);
    }
    public function rimuovi($idReq) {
        global $wpdb;
        $sql = $wpdb->prepare("DELETE FROM  " . T_REQUISITO . "  WHERE idReq=%s;", $idReq);
        $wpdb->query($sql);
    }
    public function modifica_immagine($idReq) {
        global $wpdb;
       // $sql = $wpdb->prepare("INSERT INTO " . T_REQUISITO . " VALUES(%s,%s,%d,%s,FALSE);", $idReq, $tipo, $importanza, $descrizione);
        $wpdb->query($sql);
    }
    
    public function modifica_descrizione($idReq,$descrizione) {
        global $wpdb; 
        $sql = $wpdb->prepare("UPDATE " . T_REQUISITO . " SET Descr=%s WHERE idReq=%s;",$descrizione,$idReq);
        $wpdb->query($sql);
    }
    
     public function soddisfatto($idReq,$soddisfatto) {
        global $wpdb; 
        $sql = $wpdb->prepare("UPDATE " . T_REQUISITO . " SET Soddisfatto=%d WHERE idReq=%s;",$soddisfatto,$idReq);
        $wpdb->query($sql);
    }
    public function get_max_top_level_number() {
        $array_id_req=$this->get_all_Id();
        $max=0;
        foreach ($array_id_req as $id_requisito) {
            //esplosione e prendo il primo numero
            $id_top_level=  explode('.', ''.$id_requisito->idReq);
            $int_r = (int)$id_top_level[0];
            if($int_r>$max){
                $max=$int_r;
            }
        }
        return $max;
    }
}
