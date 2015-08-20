<?php 
/*
* post extra metabox with visual editor or wp extra visual editor 
* Plugin Name: Of Metabox and Taxonomy
* Author: <a href="https://www.facebook.com/profile.php?id=100006084726970">Omar Fauruque </a>
* Author Link:  https://www.facebook.com/profile.php?id=100006084726970
* Version: 1.0
* Description: Plugin for Extra Wordpress Visual Editor.
*/

// Reguster Admin Menu
require_once('extra_function.php');  // extranal function file 

add_action( 'admin_menu', 'easy_metabox_menu' );

// Stylesheet for Plugin admin 
add_action( 'admin_init', 'easymetabox_admin_init' );

// admin enquey script 
function easymetabox_admin_init(){
        /* Register our stylesheet. */
       wp_register_style( 'easyMetabox_Stylesheet', plugins_url('css/easy_metabox.css', __FILE__) );
}
function Of_Metabox_admin_styles() {
       /*
        * It will be called only on your plugin admin page, enqueue our stylesheet here
        */
       wp_enqueue_style( 'easyMetabox_Stylesheet' );
}


//Metabox menu function  
function easy_metabox_menu(){

        $adminPage = add_menu_page( 'OF Metabox Options', 'OF Metabox', 'manage_options', 'easy_meta_box',  'of_metabox_function',  plugin_dir_url( __FILE__ ).'img/plus.png', NULL );
        add_action( 'admin_print_styles-' . $adminPage, 'Of_Metabox_admin_styles' );
}


//global css for all time action 
add_action('admin_head', 'my_OF_custom_css');

function my_OF_custom_css() {
  echo '<style>
    li#toplevel_page_easy_meta_box img {
    max-width: 18px;
    }
  </style>';
}



// Main Meta Box Control Page
function of_metabox_function(){
        require_once('of_meta_database.php');
        echo '<h2 class="text-left">OF Metabox and Taxonomy Options<h2><br/>';
        of_settings_page();
}




function of_custom_metabox(){
    global $wpdb;
    $allSections = $wpdb->get_results("SELECT * FROM `plugin_of_metabox`", OBJECT);  //

    $sections = array();
    foreach($allSections as $section){
         $sections[] = add_meta_box('custom_metabox', __($section->meta_section, 'OF Custom Metabox'), 'of_metabox_callback', $section->post_type);
    }
}
add_action('add_meta_boxes', 'of_custom_metabox');

function of_metabox_callback($post){
    global $wpdb;

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'OF_meta_box', 'OF_meta_box_nonce' );
    wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
    $prfx_stored_meta = get_post_meta( $post->ID );

    $allnormalMetas = $wpdb->get_results("SELECT * FROM `plugin_of_metabox`", OBJECT);  //\
    foreach($allnormalMetas as $single_meta){
        $single_metavalue = get_post_meta( $post->ID, $single_meta->meta_slug, true );
        if(get_post_type( $post ) == $single_meta->post_type):
            echo '<div style="background:#E0F4FE; padding:3px; color:#555; margin-top:15px">';
            echo '<label style="margin-top:15px; display:block; padding:5px" for="'.$single_meta->meta_slug.'">';
            _e( ''.$single_meta->meta_name.': <span>    </span>', 'ABM Water' );
            echo '</label> ';
            switch($single_meta->metabox_type):
                case 'text':
                echo '<input style="min-width:100%; margin-right:10px;" id="'.$single_meta->meta_slug.'" type="'.$single_meta->metabox_type.'" value="'.$single_metavalue.'" name="'.$single_meta->meta_slug.'" />';
                break;
                case 'textarea':
                echo '<textarea style="width:100%" name="'.$single_meta->meta_slug.'" id="'.$single_meta->meta_slug.'">' .  $single_metavalue  . '</textarea>';
                break;
            endswitch;
            echo '</div>';
        endif;
    }
}



// Save Meta Data 
/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function OF_meta_box_data_update( $post_id ) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['OF_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['OF_meta_box_nonce'], 'OF_meta_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */
    

       global $allowedtags;

        // allow iframe only in this instance
        $iframe = array( 'iframe' => array(
                            'src' => array(),
                            'width' => array(),
                            'height' => array(),
                            'frameborder' => array(),
                            'div' => array(),
                            'span'=> array(),
                            'class' => array(),
                            'allowFullScreen' => array() // add any other attributes you wish to allow
                             ) );

        $allowed_html = array_merge( $allowedtags, $iframe );


    // Sanitize user input.
    // Without html "sanitize_text_field"
    global $wpdb;
    $allnormalMetas = $wpdb->get_results("SELECT * FROM `plugin_of_metabox`", OBJECT);  //\
    foreach($allnormalMetas as $single_meta){        
        $my_data_sku = wp_kses( $_POST[$single_meta->meta_slug], $allowed_html ); // pic data from post 
        update_post_meta( $post_id, $single_meta->meta_slug, $my_data_sku); // Update the meta field in the database.
    }
}
add_action( 'save_post', 'OF_meta_box_data_update' );
















//Metabox with visual editor

define('FEATURES_META_BOX_ID', 'my-editor');
define('FEATURES_ID', 'myeditor'); //Important for CSS that this is different
define('FEATURES', 'extra-content');

add_action('admin_init', 'wysiwyg_register_meta_box');
function wysiwyg_register_meta_box(){
        add_meta_box(FEATURES_META_BOX_ID, __('FEATURES', 'features'), 'wysiwyg_render_meta_box', 'product');
}

function wysiwyg_render_meta_box(){
        global $post;
        $meta_box_id = FEATURES_META_BOX_ID;
        $editor_id = FEATURES_ID;
        //Add CSS & jQuery goodness to make this work like the original WYSIWYG
        echo "
                <style type='text/css'>
                        #$meta_box_id #edButtonHTML, #$meta_box_id #edButtonPreview {background-color: #F1F1F1; border-color: #DFDFDF #DFDFDF #CCC; color: #999;}
                        #$editor_id{width:100%;}
                        #$meta_box_id #editorcontainer{background:#fff !important;}
                        #$meta_box_id #$editor_id_fullscreen{display:none;}
                </style>           
                <script type='text/javascript'>
                        jQuery(function($){
                                $('#$meta_box_id #editor-toolbar > a').click(function(){
                                        $('#$meta_box_id #editor-toolbar > a').removeClass('active');
                                        $(this).addClass('active');
                                });                               
                                if($('#$meta_box_id #edButtonPreview').hasClass('active')){
                                        $('#$meta_box_id #ed_toolbar').hide();
                                }
                                
                                $('#$meta_box_id #edButtonPreview').click(function(){
                                        $('#$meta_box_id #ed_toolbar').hide();
                                });
                                
                                $('#$meta_box_id #edButtonHTML').click(function(){
                                        $('#$meta_box_id #ed_toolbar').show();
                                });

                //Tell the uploader to insert content into the correct features editor
                $('#media-buttons a').bind('click', function(){
                    var customEditor = $(this).parents('#$meta_box_id');
                    if(customEditor.length > 0){
                        edCanvas = document.getElementById('$editor_id');
                    }
                    else{
                        edCanvas = document.getElementById('content');
                    }
                });
                        });
                </script>
        ";
        
        //Create The Editor
        $content = get_post_meta($post->ID, FEATURES, true);
        the_editor($content, $editor_id);
        
        //Clear The Room!
        echo "<div style='clear:both; display:block;'></div>";
}


add_action('save_post', 'product_save_meta');
function product_save_meta(){
    
        $editor_id = FEATURES_ID;
        $meta_key = FEATURES;
    
        if(isset($_REQUEST[$editor_id]))
                update_post_meta($_REQUEST['post_ID'], FEATURES, $_REQUEST[$editor_id]);
                
}



?>
