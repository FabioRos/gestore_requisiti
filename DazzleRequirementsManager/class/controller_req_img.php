<?php
/**
 * @author Fabio Ros
 */
class controller_req_img {
    public function __construct() {}
    public function insert($idReq,$idImg) {
        global $wpdb;
        $sql=$wpdb->prepare("INSERT INTO ". T_IMG_REQUISITO." VALUES(%s,%d);",$idReq,$idImg);
        echo $sql;
        $wpdb->query($sql);
    }
    public function delete($idReq,$idImg) {
        global $wpdb;
        $sql=$wpdb->prepare("DELETE FROM ". T_IMG_REQUISITO." WHERE idReq=%s AND idImg=%d);",$idReq,$idImg);
        $wpdb->query($sql);
    }
}