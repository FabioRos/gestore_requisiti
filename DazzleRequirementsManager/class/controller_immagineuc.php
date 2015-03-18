<?php 
/**
	author: Valerio Burlin
*/


class controller_immagineuc {
	public function __construct() {
	}
	
        public function get_id($percorso) {
            //...
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