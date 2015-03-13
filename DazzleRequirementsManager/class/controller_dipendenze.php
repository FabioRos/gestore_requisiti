<?php 
/**
	author: Valerio Burlin
*/


class controller_dipendenze {
	public function __construct() {
	}
	
	public function insert($idReq,$idDip) {
		global $wpdb;
		
		$sql = $wpdb->prepare("INSERT INTO " . T_DIPENDENZE . " VALUES(%s,%s);",$idReq,$idDip);
		$wpdb->query($sql);
	}
	
	public function delete($idReq,$idDip) {
		global $wpdb;
		$sql = $wpdb->prepare("DELETE FROM " . T_DIPENDENZE . " WHERE idReq=$idReq AND idDip=$idDip;",$idReq,$idDip);
		$wpdb->query($sql);
	}	
}

?> 