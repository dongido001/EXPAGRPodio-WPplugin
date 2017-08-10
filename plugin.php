<?php
/*
Plugin Name: AIESEC EXPA Registration 
Description: Plugin based on gis_curl_registration script by Dan Laush upgraded to Wordpress plugin by Krzysztof Jackowski, updated and optimized for WP 
podio and getResponse by Enrique Suarez
Version: 0.2.1
Author: Enrique Suarez 
Author URI: https://www.linkedin.com/profile/view?id=AAIAABf8S30Bu64oKEuBPfCG5ZYEUJC_-zyYli4&trk=nav_responsive_tab_profile_pic
License: GPL 
TEST: YES
*/

$save_data = '';
if( $_SERVER['REQUEST_METHOD'] == "POST"){

   require_once('gis_reg_process.php');

}
// [expa-form program="gt"]

ob_start();
wp_enqueue_script('jquery');
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

///GENERAL
function expa_form( $atts ) {
    $a = shortcode_atts( array(
        'program' => '',
        ), $atts );
    
    $configs = include('config.php');

    $form = file_get_contents('form.html',TRUE);
    //states
    $leads_json_state = plugins_url('leads_state.json', __FILE__ );

    $json_state = file_get_contents($leads_json_state, false, stream_context_create($arrContextOptions)); 
    $states = json_decode($json_state);  
    //states

    $leads_json = plugins_url('leads.json', __FILE__ );
    $json = file_get_contents($leads_json, false, stream_context_create($arrContextOptions)); 
    $leads = json_decode($json); 
    $option_list = "";
    foreach($leads as $key => $value){
        $option_list = $option_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
    $form = str_replace("{path-gis_reg_process}",plugins_url('plugin.php', __FILE__ ),$form);
    $form = str_replace("{path-gis_lcMapper}",plugins_url('gis_lcMapper.js', __FILE__ ),$form);
    $form = str_replace("{path-leads-json}",plugins_url('leads.json', __FILE__ ),$form);
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $form = str_replace("{website_url}",$actual_link,$form);
    $form = str_replace("{leads-option-list}",$option_list,$form);
    $form = str_replace("{name}",$configs["name"],$form);
    $form = str_replace("{surname}",$configs["surname"],$form);
    $form = str_replace("{e-mail}",$configs["e-mail"],$form);
    $form = str_replace("{leads_state}",$json_state ,$form);
    $form = str_replace("{password}",$configs["password"],$form);
    $form = str_replace("{lead-name}",$configs["lead-name"],$form);
    $form = str_replace("{lc}",$configs["lc"],$form);       
    $form = str_replace("{phone}",$configs["phone"],$form);
    $form = str_replace("{source}",$configs["source"],$form);
    $form = str_replace("{interested_in}",$configs["interested_in"],$form);
    
    
    if($_GET["thank_you"]==="true"){
        return $configs["thank-you-message"]; 
    } elseif ($_GET["error"]!=""){

        $form = str_replace('<div id="error" class="error"><p></p></div>','<div id="error" class="error"><p>'.$_GET["error"].'</p></div>',$form);
        return $form;    
    }
    //var_dump( plugins_url('gis_reg_process.php', __FILE__ ));
    return $form;
}
add_shortcode( 'expa-form', 'expa_form' );

//OGT
function expa_form_ogt( $atts ) {
    $a = shortcode_atts( array(
        'program' => '',
        ), $atts );
    
    $configs = include('config.php');

    $form = file_get_contents('form_gt.html',TRUE);
    //states
    $leads_json_state = plugins_url('leads_state.json', __FILE__ );

    $json_state = file_get_contents($leads_json_state, false, stream_context_create($arrContextOptions)); 
    $states = json_decode($json_state);  
    //states
    $leads_json = plugins_url('leads.json', __FILE__ );

    $json = file_get_contents($leads_json, false, stream_context_create($arrContextOptions)); 
    $leads = json_decode($json); 
    $option_list = "";
    foreach($leads as $key => $value){
        $option_list = $option_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
    $form = str_replace("{path-gis_reg_process}",plugins_url('plugin.php', __FILE__ ),$form);
    $form = str_replace("{path-gis_lcMapper}",plugins_url('gis_lcMapper.js', __FILE__ ),$form);
    $form = str_replace("{path-leads-json}",plugins_url('leads.json', __FILE__ ),$form);
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $form = str_replace("{website_url}",$actual_link,$form);
    $form = str_replace("{leads-option-list}",$option_list,$form);
    $form = str_replace("{name}",$configs["name"],$form);
    $form = str_replace("{surname}",$configs["surname"],$form);
    $form = str_replace("{e-mail}",$configs["e-mail"],$form);
    $form = str_replace("{password}",$configs["password"],$form);
    $form = str_replace("{lead-name}",$configs["lead-name"],$form);
    $form = str_replace("{lc}",$configs["lc"],$form);       
    $form = str_replace("{leads_state}",$json_state ,$form);

    $form = str_replace("{phone}",$configs["phone"],$form);
    $form = str_replace("{source}",$configs["source"],$form);
    $form = str_replace("{interested_in}",$configs["interested_in"],$form);
    
    
    if($_GET["thank_you"]==="true"){
        return $configs["thank-you-message"]; 
    } elseif ($_GET["error"]!=""){

        $form = str_replace('<div id="error" class="error"><p></p></div>','<div id="error" class="error"><p>'.$_GET["error"].'</p></div>',$form);
        return $form;    
    }
    //var_dump( plugins_url('gis_reg_process.php', __FILE__ ));
    return $form;
}
add_shortcode( 'expa-form-ogt', 'expa_form_ogt' );




//OGV
function expa_form_ogv( $atts ) {
    $a = shortcode_atts( array(
        'program' => '',
        ), $atts );
    
    $configs = include('config.php');

    $form = file_get_contents('form_gv.html',TRUE);
        //states
    $leads_json_state = plugins_url('leads_state.json', __FILE__ );

    $json_state = file_get_contents($leads_json_state, false, stream_context_create($arrContextOptions)); 
    $states = json_decode($json_state);  
    //states
    $leads_json = plugins_url('leads.json', __FILE__ );

    $json = file_get_contents($leads_json, false, stream_context_create($arrContextOptions)); 
    $leads = json_decode($json); 
    $option_list = "";
    foreach($leads as $key => $value){
        $option_list = $option_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
    $form = str_replace("{path-gis_reg_process}",plugins_url('plugin.php', __FILE__ ),$form);
    $form = str_replace("{path-gis_lcMapper}",plugins_url('gis_lcMapper.js', __FILE__ ),$form);
    $form = str_replace("{path-leads-json}",plugins_url('leads.json', __FILE__ ),$form);
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $form = str_replace("{website_url}",$actual_link,$form);
    $form = str_replace("{leads-option-list}",$option_list,$form);
    $form = str_replace("{name}",$configs["name"],$form);
    $form = str_replace("{surname}",$configs["surname"],$form);
    $form = str_replace("{e-mail}",$configs["e-mail"],$form);
    $form = str_replace("{password}",$configs["password"],$form);
    $form = str_replace("{lead-name}",$configs["lead-name"],$form);
    $form = str_replace("{lc}",$configs["lc"],$form);   
    $form = str_replace("{leads_state}",$json_state ,$form);
    
    $form = str_replace("{phone}",$configs["phone"],$form);
    $form = str_replace("{source}",$configs["source"],$form);
    $form = str_replace("{interested_in}",$configs["interested_in"],$form);
    
    
    if($_GET["thank_you"]==="true"){
        return $configs["thank-you-message"]; 
    } elseif ($_GET["error"]!=""){

        $form = str_replace('<div id="error" class="error"><p></p></div>','<div id="error" class="error"><p>'.$_GET["error"].'</p></div>',$form);
        return $form;    
    }
    //var_dump( plugins_url('gis_reg_process.php', __FILE__ ));
    return $form;
}
add_shortcode( 'expa-form-ogv', 'expa_form_ogv' );

//OGE
function expa_form_oge( $atts ) {
    $a = shortcode_atts( array(
        'program' => '',
        ), $atts );
    
    $configs = include('config.php');

    $form = file_get_contents('form_ge.html',TRUE);
        //states
    $leads_json_state = plugins_url('leads_state.json', __FILE__ );

    $json_state = file_get_contents($leads_json_state, false, stream_context_create($arrContextOptions)); 
    $states = json_decode($json_state);  
    //states
    $leads_json = plugins_url('leads.json', __FILE__ );

    $json = file_get_contents($leads_json, false, stream_context_create($arrContextOptions)); 
    $leads = json_decode($json); 
    $option_list = "";
    foreach($leads as $key => $value){
        $option_list = $option_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
    $form = str_replace("{path-gis_reg_process}",plugins_url('gis_reg_process.php', __FILE__ ),$form);
    $form = str_replace("{path-gis_lcMapper}",plugins_url('gis_lcMapper.js', __FILE__ ),$form);
    $form = str_replace("{path-leads-json}",plugins_url('leads.json', __FILE__ ),$form);
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $form = str_replace("{website_url}",$actual_link,$form);
    $form = str_replace("{leads-option-list}",$option_list,$form);
    $form = str_replace("{name}",$configs["name"],$form);
    $form = str_replace("{surname}",$configs["surname"],$form);
    $form = str_replace("{e-mail}",$configs["e-mail"],$form);
    $form = str_replace("{password}",$configs["password"],$form);
    $form = str_replace("{lead-name}",$configs["lead-name"],$form);
    $form = str_replace("{lc}",$configs["lc"],$form);   
    $form = str_replace("{leads_state}",$json_state ,$form);
    
    $form = str_replace("{phone}",$configs["phone"],$form);
    $form = str_replace("{source}",$configs["source"],$form);
    $form = str_replace("{interested_in}",$configs["interested_in"],$form);
    
    
    if($_GET["thank_you"]==="true"){
        return $configs["thank-you-message"]; 
    } elseif ($_GET["error"]!=""){

        $form = str_replace('<div id="error" class="error"><p></p></div>','<div id="error" class="error"><p>'.$_GET["error"].'</p></div>',$form);
        return $form;    
    }
    //var_dump( plugins_url('gis_reg_process.php', __FILE__ ));
    return $form;
}
add_shortcode( 'expa-form-oge', 'expa_form_oge' );

    add_action( 'admin_menu', 'wpse_91693_register' );

    function wpse_91693_register()
    {
        add_menu_page(
            'aiesec user list',     // page title
            'AIESEC',     // menu title
            'manage_options',   // capability
            'include-text',     // menu slug
            'list_registered_users' // callback function
        );
    }

    function list_registered_users()
    {
        global $title;
        global $wpdb;

        $table_name = $wpdb->prefix . 'aiesec_table';

        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        
        // var_dump($results); die();

        print '<div class="wrap">';
        print "<h1>$title</h1><br>";

        print '
                <table class="responsive" width="70%">
                 <thead>
                  <tr>
                   <td> <b>First Name</b></td>
                   <td> <b>Surname</b></td>
                   <td> <b>Email</b></td>
                   <td> <b>Phone</b></td>
                   <td> <b>Source</b></td>

                   <td> <b>Facebook id</b></td>
                   <td> <b>Whatsapp Number</b></td>
                   <td> <b>Date Registered</b></td>
                  </tr>
                 </thead>
                 <tbody>
                ';
            foreach( $results  as $result ){
                print "
                      <tr>
                       <td>$result->fname</td>
                       <td>$result->lname</td>
                       <td>$result->email</td>
                       <td>$result->phone</td>
                       <td>$result->source</td>

                       <td>$result->facebook_id</td>
                       <td>$result->whatsapp</td>
                       <td>$result->time</td>
                      </tr>
                     ";
            }
        print '
                 </tbody>
                </table>

                ';

        print '</div>';
    }


/////////////////////////PLUGIN ACTIVATION //////////////////////////////////
    global $jal_db_version;
    $jal_db_version = '1.0';

    function jal_install() {
        global $wpdb;
        global $jal_db_version;

        $table_name = $wpdb->prefix . 'aiesec_table';
        
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            fname VARCHAR(100)  NULL,
            lname VARCHAR(100)  NULL,
            email VARCHAR(100)  NULL,
            phone VARCHAR(100)  NULL,
            source VARCHAR(100)  NULL,
            interested_in VARCHAR(100)  NULL,
            lead_name VARCHAR(100)  NULL,
            facebook_id VARCHAR(100)  NULL,
            whatsapp VARCHAR(100)  NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( 'jal_db_version', $jal_db_version );
    }

    function aiesec_add_data( $data ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aiesec_table';
        
        return $wpdb->insert( 
            $table_name, 
            array( 
                'time' => current_time( 'mysql' ),
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'source' => $data['source'],
                'interested_in' => $data['interested_in'],
                'lead_name' => $data['lead_name'],
                'facebook_id' => $data['facebook_id'],
                'whatsapp' => $data['whatsapp'],
            ) 
        );

    }

    register_activation_hook( __FILE__, 'jal_install' );

?>