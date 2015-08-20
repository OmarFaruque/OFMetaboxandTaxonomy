<?php 
	// Check Database
	global $wpdb;
	$check_OF_database = $wpdb->get_results( 'SELECT * FROM of_meta_taxonomy', OBJECT );
	if(empty($check_OF_database)):
		// Database Create Action Here  
   $table_name = $wpdb->prefix . '_of_metabox';
    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      metabox_type varchar (255) DEFAULT NULL,
      post_type varchar (200) DEFAULT NULL,
      meta_name varchar(255) DEFAULT NULL,
      meta_slug varchar (255) DEFAULT NULL,
      meta_section varchar (255) DEFAULT NULL,
      UNIQUE KEY id (id)
    );";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
	endif;
?>


test