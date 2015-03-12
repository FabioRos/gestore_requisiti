<?php

/**
 * @author Fabio Ros
 */
class controller_requisito {

    public function __construct() {
        //$this->inserisci("id", "tipo", 'a', "descrizione");
        $this->modifica_descrizione("id", "mario");
        $this->soddisfatto("id", FALSE);
        //$this->rimuovi("id");
    }

    public function inserisci($idReq, $tipo, $importanza, $descrizione) {
        global $wpdb;
        $sql = $wpdb->prepare("INSERT INTO " . T_REQUISITO . " VALUES(%s,%s,%d,%s,FALSE);", $idReq, $tipo, $importanza, $descrizione);
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
}
