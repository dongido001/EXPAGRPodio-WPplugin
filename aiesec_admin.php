<?php

//////////////////////////ADIMIN PAGE//////////////////////////////////////////

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
                   <td> <b>Interested in</b></td>
                   <td> <b>Lead Name</b></td>
                   <td> <b>Facebook id</b></td>
                   <td> <b>Whatsapp Number</b></td>
                  </tr>
                 </thead>
                 <tbody>
                ';
            print '
                  <tr>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                   <td>Tom</td>
                  </tr>
                 ';
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