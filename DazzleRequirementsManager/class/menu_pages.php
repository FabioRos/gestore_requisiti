<?php

/**
 * Questa classe gestisce il menu del plugin nel back-end di wp
 */
/* * ******************************************************************************** */


class DWMenuPages {

    public function __construct() {
        add_action('admin_menu', array(&$this, 'register_pages'));
        add_action('admin_enqueue_scripts', array(&$this, 'adds_to_the_head')); //Backend
        add_action('wp_enqueue_scripts', array(&$this, 'adds_to_the_head'));    //Frontend
        //Aggiungo la favicon al front-end
        add_action('wp_head', array(&$this, 'my_favicon'));
        //Aggiungo la favicon al back-end
        add_action('admin_head', array(&$this, 'my_favicon'));
        $this->includes();
        $controller = new DB_controller();
        $controller->setup_db();
        
        //GENERAZIONE SHORTCODES
        add_shortcode('tbl_verifica', array(&$this, 'verifica_requisiti_handler'));
        
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
        //echo  admin_url( 'admin.php?page=inserimento_slug', 'http' );
        ?>
        <form enctype="multipart/form-data" method="POST" action="<?php echo admin_url( 'admin.php?page=inserimento_slug', 'http' ); ?>" id="nuovoRequisito" >
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
            $controller_dipendenze=new controller_dipendenze();
            if (isset($parent_)&& $parent_!="NULL"){
                //genero IdReq
                $figli=$controller_dipendenze->conta_figli($parent_);
                $idReq="".$parent_.".".(($figli)+1);//aumentare di numero
                //unset($_POST["parent"]);
            }
			else{
               $idReq = $controller->get_max_top_level_number()+1;
            }
            
 
            $controller = new controller_requisito ();
            $controller->inserisci($idReq, $t, $i, $d);
            $controller_img=new controller_immagineuc();
            
            
            $controller_dipendenze->insert($idReq, $parent_);
            
            $controller_req_img=new controller_req_img();
            
            $base_path = $_POST["base_path_img"];;
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
        $url_ajax_modifica_requisito  = PLUGIN_BASE_URL."class/ajax_modifica_requisito.php";
        ?>
        <script type='text/javascript'>
        
        function drawTable(data) {
            for (var i = 0; i < data.length; i++) {
                drawRow(data[i],i);
            }
        }
        

        function drawRow(rowData,index) {
            var row = jQuery("<tr id='riga"+index+"' />");
            
            jQuery("#requirements_render").append(row); //this will append tr element to table... keep its reference for a while since we will add cels into it
            row.append(jQuery("<td id='idReq"+index+"'>" + rowData.IdReq + "</td>"));
            
            var t="Funzionale";
            if(rowData.Tipo=="Q")
                t="Qualitativo";
            else if(rowData.Tipo=="D")
                t="Prestazionale"
            row.append(jQuery("<td id='Tipo"+index+"'>" + t + "</td>"));
            var imp="Obbligatorio";
            if(rowData.Imp=="1")
                imp="Desiderabile";
            else if(rowData.Tipo=="2")
                imp="Opzionale";
            row.append(jQuery("<td id='Importanza"+index+"'>" + imp + "</td>"));
            row.append(jQuery("<td id='Descrizione"+index+"'>" + rowData.Descr + "</td>"));
             var s;
            if(rowData.Soddisfatto=="1"){
                s="requisito_soddisfatto";
            }else  if(rowData.Soddisfatto=="0"){
                s="requisito_non_soddisfatto";
            }
            row.append(jQuery("<td id='Soddisfatto"+index+"' class='"+ s +"'>" + rowData.Soddisfatto + "</td>"));  
//        
//        var row = jQuery("<tr id='riga"+index+"' />");
//            jQuery("#requirements_render").append(row); //this will append tr element to table... keep its reference for a while since we will add cels into it
//            row.append(jQuery("<td id='idReq"+index+"'>" + rowData.IdReq + "</td>"));
//            row.append(jQuery("<td id='Tipo"+index+"'>" + rowData.Tipo + "</td>"));
//            row.append(jQuery("<td id='Importanza"+index+"'>" + rowData.Imp + "</td>"));
//            row.append(jQuery("<td id='Descrizione"+index+"'>" + rowData.Descr + "</td>"));
//            row.append(jQuery("<td id='Soddisfatto"+index+"'>" + rowData.Soddisfatto + "</td>"));
           row.append(jQuery("<td id='btn_modifica"+index+"'> <input type='button' value='modifica' onclick='formMod("+index+")' /></td>"));
        }
       
        function formMod(indice) {
            var id= jQuery("#idReq"+indice+"").text();
            var old_tipo=jQuery("#Tipo"+indice+"").text();
            var old_imp=jQuery("#Importanza"+indice+"").text();
            var old_desc=jQuery("#Descrizione"+indice+"").text();
            var old_sodd=jQuery("#Soddisfatto"+indice+"").text();
            jQuery("#Tipo"+indice+"").html("<select class='form_item_DW' name='tipo' id='tipo_edit"+indice+"'><option>Funzionale</option><option>Qualitativo</option><option>Prestazionale</select>");
            jQuery("#Importanza"+indice+"").html("<select class='form_item_DW' name='importanza' id='importanza_edit"+indice+"'><option>Obbligatorio</option><option>Desiderabile</option><option>Opzionale</option></select>");
            jQuery("#Descrizione"+indice+"").html("<textarea class='form_item_DW' rows='4' cols='50' name='descrizione' id='descrizione_edit"+indice+"' placeholder=''></textarea>");
            jQuery("#Soddisfatto"+indice+"").html("<select class'form_item_DW' name 'soddisfatto' id='soddisfatto_edit"+indice+"'><option>No</option><option>Si</option></select>");
            jQuery("#btn_modifica"+indice+"").html("<input type='button' value='salva' id='submit_edit"+indice+"' onclick='salva("+id+","+indice+")' />");
            //popolare con valori di default
            jQuery("#tipo_edit"+indice).val(old_tipo);
            jQuery("#importanza_edit"+indice+"").val(old_imp);
            var s='No';        
            if(old_sodd==1)
                s='Si';
            jQuery("#soddisfatto_edit"+indice+"").val(s);
            jQuery("#descrizione_edit"+indice+"").val(old_desc);
        }
        
        function salva(id,indice) { // io passerei direttamente la stringa json con tutto dentro
                //alert(indice);
                var v = Array();
                
                v[0]=id;
                v[1]=jQuery("#tipo_edit"+indice+"").val();
                v[2]=jQuery("#importanza_edit"+indice+"").val();
                v[3]=jQuery("#descrizione_edit"+indice+"").val();
                v[4]=jQuery("#soddisfatto_edit"+indice+"").val();
                var json_v=JSON.stringify(v);
                alert(json_v);
                
              jQuery.ajax({

                type: "POST",
//                dataType: "json",
                url:'<?php echo $url_ajax_modifica_requisito; ?>',
                data: {
                    indice_riga: indice.toString(),
                    id: v[0],
                    tipo: v[1],
                    importanza: v[2],
                    descrizione: v[3],
                    soddisfatto: v[4]
                },
                success: function(data){
                    alert('ok');
                    jQuery("tr#riga"+indice+"").html(data);
                    jQuery("tr#riga"+indice+"").append( "<td id='btn_modifica"+indice+"'> <input type='button' value='modifica' onclick='formMod("+indice+")'></td>");
                },
                error: function(){
                    alert('ko');
                }

            }); // Ajax Call
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
            
            var t="Funzionale";
            if(rowData.Tipo=="Q")
                t="Qualitativo";
            else if(rowData.Tipo=="D")
                t="Prestazionale"
            row.append(jQuery("<td id='Tipo"+rowData.IdReq+"'>" + t + "</td>"));
            var imp="Obbligatorio";
            if(rowData.Imp=="1")
                imp="Desiderabile";
            else if(rowData.Tipo=="2")
                imp="Opzionale";
            row.append(jQuery("<td id='Importanza"+rowData.IdReq+"'>" + imp + "</td>"));
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

        $url_ajax_utils  = PLUGIN_BASE_URL."class/ajax_utils.php";
        //$url_menu_pages .= $_SERVER[''];        
        //echo $url_ajax_utils;
        $controller_requisiti = new controller_requisito ();
        $json_data=json_encode($controller_requisiti->get_all());
        //$current_Req_Id;
        echo "<select class=\"form_item_DW\" id='select_requisiti' name=\"select_requisiti\" > "
              . " <option value=\"NULL\">nessuno</option></select>";
        ?>
        <div id="id_corrente_txt" ></div>
        <div id="img_wrapper"></div>
        <?php

        $cntrl_db= new DB_controller();
        ?>
                
        <script type='text/javascript'>
        var id_selezionato=jQuery('#select_requisiti option:selected').val();
        jQuery('#img_wrapper option:selected').append(id_selezionato);
        </script>
                 
        <script type='text/javascript'>

        function popola_select(data) {
            for (var i = 0; i < data.length; i++) {
                aggiungi_opzione(data[i]);
            }
        }

        function aggiungi_opzione(rowData) {
            jQuery("#select_requisiti").append(new Option(""+rowData.IdReq+"", ""+rowData.IdReq+""));
        }
        
        jQuery("#select_requisiti").change(function(){
            var nuovo_id=jQuery("#select_requisiti").val();
            jQuery("#id_corrente_txt").html(nuovo_id);
            jQuery("#img_wrapper").html("ciao");
               
            jQuery.ajax({
            type: "GET",
            url:'<?php $url_ajax_utils.="?function_name=ajax_get_images_url&id_req=";
                 echo $url_ajax_utils; ?>'+nuovo_id,
                 // dataType: 'json',
                 // data: {'function_name':'ajax_get_images_url', 'id_req':''+nuovo_id+''},
            success:function(obj){
                        jQuery('#img_wrapper').html(obj);
                        //alert('ok');
                    },
            error:function(){
                      alert('ko');
                  }
            });
        });

        jQuery(document).ready(function() {
            var lista_requisiti_json = <?php echo $json_data; ?>;
            popola_select(lista_requisiti_json);       
        });  

        </script>
        <?php
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
$aux = new DWMenuPages();

?>