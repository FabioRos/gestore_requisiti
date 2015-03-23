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
                'amministrazione_gestione_requisiti_slug', 'Gestione Immagini', 'Immagini', 'manage_options', 'img_requisiti_slug', array($this, 'img_requisiti_handler')
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
        ?>
        <form enctype="multipart/form-data" method="POST" action="http://localhost/roomrose/wp-admin/admin.php?page=inserimento_slug" id="nuovoRequisito" >
        <label for="parent" class="form_item_DW">Padre:</label>
        <?php
        $controller = new controller_requisito ();
       
        
        
        
        $json_data=json_encode($controller->get_all());
       // var_dump($elenco_requisiti);
       //  for($i=1;$i<6;$i++){?>
       <!-- <select class="form_item_DW"  name="parent" id="importanza">
              <!--
              <option value="<?php 
              //echo $elenco_requisiti[$i]['IdReq'];?>"><?php
             // echo $elenco_requisiti[$i]['IdReq'];?>
              </option>
             
              </select>
          -->     
        <?php
        //}
            echo "<select class=\"form_item_DW\" id='select_parent' name=\"parent\" id=\"importanza\"> "
                 . " <option value=\"NULL\">nessuno</option>"
                 . "</select>";
          ?>
        
         
       
        <?php
        include PLUGIN_BASE_URL . '/pages/page_insert.php';
        

        //BUSINESS LOGIC
        
        if (isset($_POST['descrizione']) && $_POST['descrizione'] != "" &&
                isset($_POST['tipo']) && $_POST['tipo'] != "" &&
                isset($_POST['importanza']) && $_POST['importanza'] != "") {
            $d = $_POST['descrizione'];
            $t = $_POST['tipo'];
            $i = $_POST['importanza'];

            $idReq='';
               //Dipendenze
            $parent_=$_POST["parent"];
            echo "PRINT ".$parent_."<br />";
            if (isset($parent_)&& $parent_!="NULL"){
                $controller_dipendenze=new controller_dipendenze();
                //genero IdReq
                $figli=$controller_dipendenze->conta_figli($parent_);
                echo "PRINT".$figli."<br />";
                $idReq="".$parent_.".".(($figli)+1);//aumentare di numero
                echo "PRINT".$figli."<br >";
                //unset($_POST["parent"]);
                }else{
               $idReq = $controller->get_max_top_level_number()+1;
            }
            
            //$controller_dip= new controller_dipendenze();
        
            
           // $idReq=$controller_dip->conta_figli("fydfxxx");
            
            
            /* per il calcolo dell'IdReq:
             * 
             * 1.prendo l'id del padre nel formato 1.2.....
             * 2.conto quanti figli ha nella tabella delle dipendenze
             * 3.l' IdReq sarà: <IdPadre>.<numero figli correnti+1>
             * 
             */
            
            
            $controller = new controller_requisito ();
            $controller->inserisci($idReq, $t, $i, $d);
            $controller_img=new controller_immagineuc();
            
            
           $controller_dipendenze->insert($idReq, $parent_);
            
            $controller_req_img=new controller_req_img();
            
            $base_path = $_POST["base_path_img"];
            for($i=1;$i<=5;$i++){
                
                $percorso = trim($base_path.$_POST["img$i"]);
                if (isset($_POST["img$i"]) && $_POST["img$i"]!=''){
                    $controller_img->insert('',$_POST["titolo_img$i"], $percorso);
                    $controller_req_img->insert($idReq, $controller_img->get_id($percorso));
                }
            }
                
            
            
         
            
            
            
            
            unset($_POST['descrizione']);
            unset($_POST['tipo']);
            unset($_POST['importanza']);
            
        }
         $json_data=json_encode($controller->get_all());
        ?>
          
           <script type='text/javascript'>
        
       // $("#select_parent").append(new Option(""+option text+"", ""+value+""));
        
        
        
        
        function popola_select(data) {
            for (var i = 0; i < data.length; i++) {
                aggiungi_opzione(data[i]);
            }
        }

        function aggiungi_opzione(rowData) {
            jQuery("#select_parent").append(new Option(""+rowData.IdReq+"", ""+rowData.IdReq+""));
        }
        
        jQuery(document).ready(function() {
            var lista_requisiti_json = <?php echo $json_data; ?>;
            popola_select(lista_requisiti_json);
        });  

        </script>
            
        <?php
    }
    public function modifica_requisiti_handler() {
        echo "<h1>MODIFICA REQUISITI</h1>";
        include PLUGIN_BASE_URL . '/pages/page_modifica.php';
        
        $controller = new controller_requisito ();
        $json_data=json_encode($controller->get_all());
        echo " <br />";//.$json_data;
        
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
            row.append(jQuery("<td id='Importanza'>" + rowData.Imp + "</td>"));
            row.append(jQuery("<td id='Descrizione'>" + rowData.Descr + "</td>"));
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
         include PLUGIN_BASE_URL . '/pages/page_verifica.php';
        
         
        $controller = new controller_requisito ();
        //$controller->set_soddisfatto("1", 1);
        
        
        $json_data=json_encode($controller->get_all());
        echo " <br />";//.$json_data;
        
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
            row.append(jQuery("<td id='idReq"+rowData.IdReq+"'>" + rowData.IdReq + "</td>"));
            row.append(jQuery("<td id='Tipo"+rowData.IdReq+"'>" + rowData.Tipo + "</td>"));
            row.append(jQuery("<td id='Importanza"+rowData.IdReq+"'>" + rowData.Imp + "</td>"));
            row.append(jQuery("<td id='Descrizione"+rowData.IdReq+"'>" + rowData.Descr + "</td>"));
             var s;
            if(rowData.Soddisfatto=="1"){
                s="requisito_soddisfatto";
            }else  if(rowData.Soddisfatto=="0"){
                s="requisito_non_soddisfatto";
            }
            row.append(jQuery("<td id='Soddisfatto"+rowData.IdReq+"' class='"+ s +"'>" + rowData.Soddisfatto + "</td>"));
           
        }
        
        
        jQuery(document).ready(function() {
            var lista_requisiti_json = <?php echo $json_data; ?>;
            drawTable(lista_requisiti_json);
        });  

        </script>
        
        <?php
        
    }
    
   public function img_requisiti_handler() {
        echo "<h1>ASSOCIAZIONI IMMAGINI REQUISITI</h1>";
        $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        $url .= $_SERVER['REQUEST_URI'];

        $controller_requisiti = new controller_requisito ();
        $json_data=json_encode($controller_requisiti->get_all());
        
          echo "<select class=\"form_item_DW\" id='select_requisiti' name=\"parent\" id=\"importanza\"> "
                 . " <option value=\"NULL\">nessuno</option>"
                 . "</select>";
         
          for($i=1;$i<=5;$i++){
              echo "<img id='img_$i'class='DW_images'>";
              
          }
          
          ?>
        
         
        <script type='text/javascript'>
        
       // $("#select_parent").append(new Option(""+option text+"", ""+value+""));
        
        
        
        
        function popola_select(data) {
            for (var i = 0; i < data.length; i++) {
                aggiungi_opzione(data[i]);
            }
        }

        function aggiungi_opzione(rowData) {
            jQuery("#select_requisiti").append(new Option(""+rowData.IdReq+"", ""+rowData.IdReq+""));
        }
        
        jQuery("#select_requisiti").change(function(){
                 var img = new Image();
                 img.src=<?php echo $_POST["base_path_img"]; ?>+jQuery("#select_requisiti option:selected").value;
                 if(img.height != 0)
                     alert('vuoto');
                     
             });

        jQuery(document).ready(function() {
            var lista_requisiti_json = <?php echo $json_data; ?>;
            popola_select(lista_requisiti_json);
            
//             
            
            
        });  

        </script>
        <?php
        
        
//echo(dirname(dirname($url))).'/wp-content/plugins/DazzleRequirementsManager/images/Requisiti/*.*';

//get all image files with a .jpg extension.
//$images = glob(dirname(dirname($url)).'/wp-content/plugins/DazzleRequirementsManager/images/Requisiti/*.jpg');
//echo dirname(dirname($url)).'/wp-content/plugins/DazzleRequirementsManager/images/Requisiti';
////print each file name
//foreach($images as $image){
//    echo $image;
//}
        

       /* $uploads = dirname(dirname($url)).'/wp-content/plugins/DazzleRequirementsManager/images/Requisiti';
        echo $uploads;
        $dir = opendir($uploads);
        if ($dir) {
                $images = array();
                while (false !== ($file = readdir($dir))) {
                        if ($file != "." && $file != "..") {
                                $images[] = $file; 
                        }
                }
                closedir($dir);
        }

        echo '<ul>';
        foreach($images as $image) {
                echo '<li><img src="';
                echo dirname(dirname($url)).'/wp-content/plugins/DazzleRequirementsManager/images/Requisiti/'.$image;
                echo '" alt="" /></li>';
        }
        echo '</ul>';
*/
        
        /*
         Per il momento le immagini si devono caricare dal frontend con una pagina apposita, serve il plugin Wordpress File Upload
         *          */
        
       // echo do_shortcode('[wordpress_file_upload uploadpath="plugins/DazzleRequirementsManager/images/Requisiti" createpath="true" showtargetfolder="true" placements="title/filename+selectbutton+uploadbutton/progressbar" uploadtitle="Carica foto" selectbutton="Seleziona Foto" uploadbutton="Carica" warningmessage="File %filename% è stato caricato ma con alcuni Warning" errormessage="File %filename% non caricato" waitmessage="il File %filename% si trova in fase di caricamento" medialink="true"]');
// echo '[fu-upload-form]';
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