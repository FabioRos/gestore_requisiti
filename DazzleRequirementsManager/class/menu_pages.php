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
        $controller = new DB_controller();
        $controller->setup_db();
    }

    public function includes() {
        //require_once '../includes/basic.php';
        require_once 'DB_controller.php';
        //require_once "controller_requisito.php";
        //require ( plugins_url() .  'class/SLSBlogRolesClass.php');
        //require_once plugin_dir_path(__FILE__) . 'SLSBlogRolesClass.php';
        //require_once plugin_dir_path(__FILE__) . 'SLSRolesTableClass.php';
        // require_once plugin_dir_path(__FILE__) . 'classPossibilita.php';
        //  require_once plugin_dir_path(__FILE__) . 'classOspedale.php';
    }

    public function adds_to_the_head() {
        wp_enqueue_script('jquery');
        wp_register_script('main_script', plugin_dir_url(__DIR__) . 'js/mainScript.js', array('jquery'), '', true);
        wp_enqueue_script('main_script');
        wp_register_style('aggiunta-css', plugin_dir_url(__DIR__) . 'css/style.css', '', '', 'screen');
        wp_enqueue_style('aggiunta-css');
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
                'amministrazione_gestione_requisiti_slug', 'Inserimento Requisiti', 'Inserimento', 'manage_options', 'inserimento_slug', array($this, 'inserimento_requisiti_handler')
        );
        add_submenu_page(
                'amministrazione_gestione_requisiti_slug', 'Modifica Requisiti', 'Modifica', 'manage_options', 'modifica_slug', array($this, 'modifica_requisiti_handler')
        );
        add_submenu_page(
                'amministrazione_gestione_requisiti_slug', 'Verifica Requisiti', 'Verifica', 'manage_options', 'verifica_slug', array($this, 'verifica_requisiti_handler')
        );
    }

    public function requisiti_handler() {
        ?><h1>REQUISITI DASHBOARD</h1>
        <form id="form_dashboard">
            <h2>Funzionalità accessorie</h2>
            <input type="button" value="Scarica Requisiti.tex" />
        </form>
            
            <?php        
    }

    public function inserimento_requisiti_handler() {
        echo "<h1>INSERIMENTO REQUISITI</h1>";
        include PLUGIN_BASE_URL . '/pages/page_insert.php';

        //BUSINESS LOGIC
        
        if (isset($_POST['descrizione']) && $_POST['descrizione'] != "" &&
                isset($_POST['tipo']) && $_POST['tipo'] != "" &&
                isset($_POST['importanza']) && $_POST['importanza'] != "") {
            $d = $_POST['descrizione'];
            $t = $_POST['tipo'];
            $i = $_POST['importanza'];

            $controller = new controller_requisito ();
            $controller->inserisci("xxx", $t, $im, $d);

            unset($_POST['descrizione']);
            unset($_POST['tipo']);
            unset($_POST['importanza']);
        }
    }
    public function modifica_requisiti_handler() {
        echo "<h1>MODIFICA REQUISITI</h1>";
        include PLUGIN_BASE_URL . '/pages/page_modifica.php';
        
        $controller = new controller_requisito ();
        echo " <br />";
        $json_data=json_encode($controller->get_all());
        ?>
        <script type='text/javascript'>
        
        function drawTable(data) {
            for (var i = 0; i < data.length; i++) {
                drawRow(data[i]);
            }
        }

        function drawRow(rowData) {
            var row = jQuery("<tr />")
            jQuery("#requirements_render").append(row); //this will append tr element to table... keep its reference for a while since we will add cels into it
            row.append(jQuery("<td id='idReq'>" + rowData.IdReq + "</td>"));
            row.append(jQuery("<td id='Tipo'>" + rowData.Tipo + "</td>"));
            row.append(jQuery("<td id='Importanza'>" + rowData.Importanza + "</td>"));
            row.append(jQuery("<td id='Descrizione'>" + rowData.Descrizione + "</td>"));
            row.append(jQuery("<td id='Soddisfatto'>" + rowData.Soddisfatto + "</td>"));
            row.append(jQuery("<td id='btn_modifica'> <input type='button' value='modifica'/></td>"));
        }
        
        jQuery(document).ready(function() {
            var lista_requisiti_json = <?php echo $json_data; ?>;
            drawTable(lista_requisiti_json);
        });  

        </script>
        <?php
        
        
        
    }
    public function verifica_requisiti_handler() {
        echo "<h1>VERIFICA REQUISITI</h1>";
    }

    
    public static function mysql_to_json($sth){
        $rows = array();
        while ($r = mysqli_fetch_assoc($sth)) {
            $rows[] = $r;
        }
        print json_encode($rows);
    
        
        }
}//CLASS END

//devo istanziare la classe
$aux = new DWMenuPages()
?>