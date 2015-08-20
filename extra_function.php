<?php
function of_admin_tabs( $current = 'settings' ) {
    $tabs = array( 'settings' => 'Settings', 'visual-metabox' => 'Visual Metabox', 'taxonomy' => 'Taxonomy' );
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=easy_meta_box&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function of_settings_page() {

// Date Insert functionality 
if(isset($_POST['of_plugin_Submit'])){
  global $wpdb;
  $table_name = $wpdb->prefix . '_of_metabox';
  $meta_slug = str_replace(' ', '-', $_POST['meta_name']);
  $wpdb->query( $wpdb->prepare(
    "INSERT INTO ".$table_name." (metabox_type, post_type, meta_name, meta_slug, meta_section) VALUES ( %s, %s, %s, %s, %s )",
    array(
        $_POST['of_meta_class'],
        $_POST['post_type'],
        $_POST['meta_name'], 
        $meta_slug,
        $_POST['add_meta_section']
    )
));
  
}



// dynamic function 
/*
$thing = 'some_function';
$$thing = function() {
   echo 'test function';
};
$some_function();
*/

if ( isset ( $_GET['tab'] ) ){
	of_admin_tabs($current = $_GET['tab']); 
}else{
	of_admin_tabs('settings');	
}


// Display TaB CONTENT 
?>
<form method="post" action="<?php admin_url( 'admin.php?page=easy_meta_box' ); ?>">
<?php
	wp_nonce_field( "of-settings-page" );  // set nonce field 
	
if ( $_GET['page'] == 'easy_meta_box' ){
   if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
   else $tab = 'settings';

   echo '<table class="form-table OF_table">';
   switch ( $tab ){
      case 'settings' :
         ?>
         <tr class="meta_type">
            <th>Custom Metabox Settings:</th>
         </tr>
          <tr class="meta_type"><td>
               <input id="of_text_meta" name="of_meta_class" type="radio" value="text" required  />
               <label for="of_text_meta">Text Type Metabox</label><br/><br/>
          </td></tr>
          <tr class="meta_type"><td>
               <input id="of_textarea_meta" name="of_meta_class" type="radio" value="textarea" />
               <label for="of_textarea_meta">Textarea Type Metabox</label><br/><br/>
          </td></tr>
          <tr class="meta_type"><td>  
               <input id="of_radio_meta" name="of_meta_class" type="radio" value="radio" />
               <label for="of_radio_meta">Radio Type Metabox</label><br/><br/>
          </td></tr>
          <tr class="meta_type"><td>
               <input id="of_checkbox_meta" name="of_meta_class" type="radio" value="radio" />
               <label for="of_checkbox_meta">Checkbox Type Metabox</label><br/><br/>
          </td></tr>
          <tr class="meta_type"><td>
               <input id="of_colorpicker_meta" name="of_meta_class" type="radio" value="radio" />
               <label for="of_colorpicker_meta">Color Picker Metabox</label><br/><br/>
          </td></tr>
          <tr>
          <td>
         <?php
        $post_types = get_post_types();
        $delete_posts = array('attachment', 'revision', 'nav_menu_item');
        $new_post_arrays = array_diff($post_types, $delete_posts ); 
        ?>
        <label for="post_type">Post Type: </label>
        <select name="post_type" id="post_type" required>
          <option value="">Set Post Type</option>
          <?php foreach($new_post_arrays as $singleP): ?>
            <option value="<?= $singleP; ?>"><?= $singleP; ?></option>
          <?php endforeach; ?>
        </select>
            </td>
         </tr>
         <tr>
          <td>
            <label for="meta_name">Meta Name: </label>
            <input type="text" name="meta_name" value="" id="meta_name" required/>
          </td>
         </tr>
         <tr>
          <td>
            <label for="meta_instruction">Meta Instruction: </label>
            <input type="text" name="meta_instruction" value="" id="meta_instruction" />
          </td>
         </tr>
         <tr>
          <td>
            <div id="meta_Section"><label for="meta_Section">Meta Section</label>
                <select name="add_meta_section">
                    <option value="">Select Meta Section</option>
                  <?php
                    global $wpdb;
                    $alls = $wpdb->get_results("SELECT * FROM `plugin_of_metabox` GROUP BY `meta_section`", OBJECT);  //\
                    foreach($alls as $single_section){
                      echo '<option value="'.$single_section->meta_section.'">'.$single_section->meta_section.'</option>';
                    }
                  ?> 
                </select></div>
            <input type="button" id="add_new"  class="button-primary" value="Add New" />
            <div style="margin-top:10px;" class="new_element"></div>
            <script>
              jQuery('#add_new').click(function(){
                  jQuery('#meta_Section').remove();
                  jQuery('input#add_new').next('.new_element').html('<label for="add_meta_section">New Meta Section: </label><input type="text" value="" name="add_meta_section" id="add_meta_section" />');
              });
            </script>
          </td>
         </tr>
        <?php
      break;
      case 'visual-metabox' :
         ?>
         <tr>
            <th><label for="ilc_ga">Insert tracking code:</label></th>
            <td>
               Enter your Google Analytics tracking code:
               <textarea id="ilc_ga" name="ilc_ga" cols="60" rows="5"><?php echo esc_html( stripslashes( $settings["ilc_ga"] ) ); ?></textarea><br />

            </td>
         </tr>
         <?php
      break;
      case 'taxonomy' :
         ?>
         <tr>
            <th><label for="ilc_intro">Introduction</label></th>
            <td>
               Enter the introductory text for the home page:
               <textarea id="ilc_intro" name="ilc_intro" cols="60" rows="5" ><?php echo esc_html( stripslashes( $settings["ilc_intro"] ) ); ?></textarea>
            </td>
         </tr>
         <?php
      break;
   }
   echo '</table>';
}

?>
   <p class="submit" style="clear: both;">
      <input type="submit" name="of_plugin_Submit"  class="button-primary" value="Submit" />
      <input type="hidden" name="of-settings-submit" value="Y" />
   </p>
</form>


<?php
}
?>