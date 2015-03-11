<?php

/**
 * Questa classe gestisce il menu del plugin nel back-end di wp
 */
/* * ******************************************************************************** */

class DWMenuPages {

    public function __construct() {
        add_action('admin_menu', array(&$this, 'register_pages'));
        add_action('admin_enqueue_scripts', array(&$this, 'adds_to_the_head'));
        //Aggiungo la favicon al front-end
        add_action('wp_head', array(&$this, 'my_favicon'));
        //Aggiungo la favicon al back-end
        add_action('admin_head', array(&$this, 'my_favicon'));
        $this->includes();
        $controller=new DB_controller();
        $controller->setup_db();
        
        
    }

    public function includes() {
        require_once 'DB_controller.php';
        //require ( plugins_url() .  'class/SLSBlogRolesClass.php');
        //require_once plugin_dir_path(__FILE__) . 'SLSBlogRolesClass.php';
        //require_once plugin_dir_path(__FILE__) . 'SLSRolesTableClass.php';
       // require_once plugin_dir_path(__FILE__) . 'classPossibilita.php';
      //  require_once plugin_dir_path(__FILE__) . 'classOspedale.php';
    }

    public function adds_to_the_head() {

     //   wp_register_script('aggiunta-main-js', plugin_dir_url(__DIR__) . 'js/mainScript.js', array('jquery'), '', true);
     //   wp_enqueue_script('aggiunta-main-js');
     //   wp_register_style('aggiunta-css', plugin_dir_url(__DIR__) . 'css/style.css', '', '', 'screen');
    //    wp_enqueue_style('aggiunta-css');
    }

    public function my_favicon() {
        echo "<link rel='shortcut icon' href='" . plugin_dir_url(__DIR__) . "images/Icon.png'/>";
    }

    /**
     * registra le pagine del plugin nel admin menu del plugin
     */
	  public function register_pages() {
        //NB: per associare gli handler stessa tecnica del costruttore ma senza '&' nel $this
        //perche' si riferisce ad un metodo membro di un'istanza della classe e non della classe
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); 


        /* add_menu_page(
          'SLS Roles Option', 'SLS Roles', 'manage_options', 'sls_roles_opt_slug', array($this, 'sls_roles_opt_handler'), plugin_dir_url(__DIR__) . 'images/menu-icon.png'
          );
         */
        // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
//    add_submenu_page(
//            'sls_roles_opt_slug', 'Ruoli Attualmente Previsti', 'Roles', 'manage_options', 'submenu_roles_slug', array($this, 'setting_roles_handler')
//    );

        add_menu_page(
                'DWRM_gestione_requisiti', 'REQUISITI ', 'manage_options', 'amministrazione_gestione_requisiti_slug', array($this, 'requisiti_handler'), plugin_dir_url(__DIR__) . 'images/Icon.png'
        );
		
		add_submenu_page(
			'amministrazione_gestione_requisiti_slug', 
			'Gestione Requisiti', 'Gestione', 'manage_options', 'gestione_slug', array($this, 'gestione_requisiti_handler')
		);
		add_submenu_page(
			'amministrazione_gestione_requisiti_slug', 
			'Verifica Requisiti', 'Verifica', 'manage_options', 'verifica_slug', array($this, 'verifica_requisiti_handler')
		);
    }
	
	public function requisiti_handler(){
		echo "<h1>REQUISITI DASHBOARD</h1>";
	
	}
	public function gestione_requisiti_handler(){
		echo "<h1>GESTIONE REQUISITI</h1>";
	
	}
	public function verifica_requisiti_handler(){
		echo "<h1>VERIFICA REQUISITI</h1>";
	
	}
	
}
//devo istanziare la classe
$aux = new DWMenuPages()
	
	
   ?>