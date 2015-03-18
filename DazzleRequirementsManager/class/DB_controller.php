<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB_controller
 *
 * @author Fabio
 */
class DB_controller {
    
    public function __construct() {
        $this->definisci();
        $this->includes();
        $cntrl = new controller_requisito();
        
    }
    
    public function  includes() {
        require_once 'controller_requisito.php';
        require_once 'controller_req_img.php';
        require_once 'controller_dipendenze.php';
        require_once 'controller_immagineuc.php';
    }
    
    
    
    public function definisci() {
        define('T_REQUISITO','Requisito');
        define('T_IMG_USE_CASE','ImmagineUC');
        define('T_IMG_REQUISITO','ReqImg');
        define('T_DIPENDENZE','Dipendenze');

    }
    public function setup_db() {
        global $wpdb;
        $sql ="CREATE TABLE IF NOT EXISTS " . T_REQUISITO . " (
	IdReq			VARCHAR(10) PRIMARY KEY,
	Tipo			CHAR(1) NOT NULL,
	Imp				INT NOT NULL,
	Descr			VARCHAR(50) NOT NULL, 
	Soddisfatto		TINYINT(1)
        ) ENGINE=InnoDB;";
        $res1 = $wpdb->query($sql);
        $sql ="CREATE TABLE IF NOT EXISTS " . T_IMG_USE_CASE . " (
	IdImg		INT AUTO_INCREMENT PRIMARY KEY,
	Titolo		VARCHAR(10) NOT NULL,
	Percorso	VARCHAR(300) NOT NULL
        ) ENGINE=InnoDB;";
        $res2 = $wpdb->query($sql);
        $sql ="CREATE TABLE IF NOT EXISTS " . T_IMG_REQUISITO . " (
	IdReq		VARCHAR(10),
	IdImg 		INT,
	PRIMARY KEY (IdReq,IdImg),
	FOREIGN KEY (IdReq) REFERENCES Requisito(IdReq) ON DELETE CASCADE,
	FOREIGN KEY (IdImg) REFERENCES ImmagineUC(IdImg) ON DELETE CASCADE	
        ) ENGINE=InnoDB;";
        $res3 = $wpdb->query($sql);
        $sql ="CREATE TABLE IF NOT EXISTS " . T_DIPENDENZE . " (
	IdReq 		VARCHAR(10),
	IdDip		VARCHAR(10),
	PRIMARY KEY (IdReq,IdDip),
	FOREIGN KEY (IdReq) REFERENCES Requisito(IdReq) ON DELETE CASCADE,
	FOREIGN KEY (IdDip) REFERENCES Requisito(IdReq) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        $res4 = $wpdb->query($sql);
    }
    
    //put your code here
}

