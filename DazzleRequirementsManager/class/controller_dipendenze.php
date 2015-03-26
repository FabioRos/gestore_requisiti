<?php 
/**
	author: Valerio Burlin
*/


class controller_dipendenze {
	public function __construct() {
	}
	
	public function insert($idReq,$idDip) {
		global $wpdb;
		if(isset($idDip) && $idDip!='')
			$sql = $wpdb->prepare("INSERT INTO " . T_DIPENDENZE . " VALUES(%s,%s);",$idReq,$idDip);
		
		$wpdb->query($sql);
	}
	
	public function delete($idReq,$idDip) {
		global $wpdb;
		$sql = $wpdb->prepare("DELETE FROM " . T_DIPENDENZE . " WHERE IdReq=%s AND IdDip=%s;",$idReq,$idDip);
		$wpdb->query($sql);
	}	
        
        public function conta_figli($idRequisitoPadre) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT COUNT(*) FROM " . T_DIPENDENZE . " WHERE IdDip=%s;",$idRequisitoPadre);
            return $wpdb->get_var($sql);
        }
}

?> 