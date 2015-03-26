<?php 
/**
	author: Valerio Burlin
*/


class controller_immagineuc {
	public function __construct() {
	}
	
    public function get_id($percorso) { 
        global $wpdb;
        $sql = $wpdb->prepare("SELECT IdImg FROM " . T_IMG_USE_CASE . " WHERE percorso=%s;",$percorso);
        echo $sql;
        return $wpdb->get_var($sql);
    }
        
	public function insert($idImg,$titolo,$percorso) {
		global $wpdb;
		
		$sql = $wpdb->prepare("INSERT INTO " . T_IMG_USE_CASE . " VALUES(%d,%s,%s);",$idImg,$titolo,$percorso);
		$wpdb->query($sql);
	}
	
	public function delete($idImg) {
		global $wpdb;
		$sql = $wpdb->prepare("DELETE FROM " . T_IMG_USE_CASE . " WHERE idImg=$idImg",$idImg);
		$wpdb->query($sql);
	}	
}

?> 