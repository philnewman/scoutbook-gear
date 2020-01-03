<?php
/**
 * Plugin Name: Scoutbook  Gear
 * Plugin URI: http://troop351.org/plugins/scoutbook-gear
 * Description:
 * Version: 0.1
 * Author: Phil Newman
 * Author URI: http://getyourphil.net
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.en.html
 **/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
define( 'WP_DEBUG', true );


function ptn_scoutbook_gear_load_css_and_js() {
  wp_enqueue_style('ptn-scoutbook-gear-styles', plugin_dir_url( __FILE__ ) . 'css/scoutbook-gear-styles.css' );
}
add_action( 'init', 'ptn_scoutbook_gear_load_css_and_js' );

include_once plugin_dir_path(__FILE__)."includes/shortcodes.php";
include_once plugin_dir_path(__FILE__)."includes/scoutbook-gear.inc";

/****************************************************************************/
/* Create custom post type of gear                                 */
/****************************************************************************/
function create_scoutbook_gear() {
    register_post_type( 'gear',
        array(
            'labels' => array(
                'name' => 'Gear',
                'singular_name' => 'Gear',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Gear',
                'edit' => 'Edit',
                'edit_item' => 'Edit Gear',
                'new_item' => 'New Gear',
                'view' => 'View',
                'view_item' => 'View Gear',
                'search_items' => 'Search Gear',
                'not_found' => 'No Gear found',
                'not_found_in_trash' => 'No Gear found in Trash',
                'parent' => 'Parent'
            ),
            'public' => true,
            'menu_position' => 15,
            'supports' => array(  'title', 'editor'),
           'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'assets/icon-20x20.png', __FILE__ ),
            'has_archive' => true
        )
    );
}
add_action( 'init', 'create_scoutbook_gear' );

add_action( 'add_meta_boxes', 'ptn_scoutbook_gear_add_meta_boxes');
add_action( 'save_post', 'ptn_scoutbook_gear_save_gear', 10, 2 );

function ptn_scoutbook_gear_add_meta_boxes(){

   add_meta_box(
    'gear-infor',
    'Gear Information',
    'ptn_scoutbook_gear_info_init',
    'gear');

  add_meta_box(
    'gear-checkout',
    'Gear CheckOut',
    'ptn_scoutbook_gear_checkout_init',
    'gear');
}

function ptn_scoutbook_gear_info_init(){
	    global $post;
	    // Use nonce for verification
	    wp_nonce_field( plugin_basename( __FILE__ ), 'gear_nonce' );
  ?>
      <div id="gear-storage">
        Name: <input type="text" name="gearName" value="<?php echo get_post_meta($post->ID, 'gearName', true); ?>" /></p>
        Storage: <input type="text" name="gearStorage" value="<?php echo get_post_meta($post->ID, 'gearStorage', true); ?>" /></p>
      </div>
      <?php
}

function ptn_scoutbook_gear_checkout_init(){
	    global $post;
	    // Use nonce for verification
	    wp_nonce_field( plugin_basename( __FILE__ ), 'gear_nonce' );
	    ?>

	    <div id="gear_meta_item">
	    <?php
	    //Obtaining the linked gearDetails meta values
	    $gearDetails = get_post_meta($post->ID,'gearDetails',true);
	    $c = 0;

      printf( '<table id="detailtable"><th>Who</th><th>CheckOut</th><th>CheckIn</th><th>Notes</th>');

	    if ( count( $gearDetails ) > 0 && is_array($gearDetails)) {
	        foreach( $gearDetails as $gearDetail ) {
	            if ( isset( $gearDetail['checkout'] ) || isset( $gearDetail['checkin'] ) ) {
                 printf('<tr><td><input type="text" name="gearDetails[%1$s][user]" value="%2$s" /></td>
                             <td><input type="date" name="gearDetails[%1$s][checkout]" value="%3$s" /></td>
                             <td><input type="date" name="gearDetails[%1$s][checkin]" value="%4$s" /></td>
                             <td><input type="text" name="gearDetails[%1$s][notes]" value="%5$s" /></td></tr>'
                        ,$c, $gearDetail['user'], $gearDetail['checkout'], $gearDetail['checkin'], $gearDetail['notes']);
	                $c = $c +1;
	            }
	        }

	    }          

	    ?>
        <span id="output-package">
  </table>
	<a href="#" class="add_package"><?php _e('Add Gear Checkin / Checkout Details'); ?></a>
	<script>
	    var $ =jQuery.noConflict();
	    $(document).ready(function() {
	        var count = <?php echo $c; ?>;
	        $(".add_package").click(function() {
	            count = count + 1;

            var table = document.getElementById("detailtable");
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);

            cell1.innerHTML = '<input type="text" name="gearDetails['+count+'][user]" value="" />';
            cell2.innerHTML = '<input type="date" name="gearDetails['+count+'][checkout]" value="" />';
            cell3.innerHTML = '<input type="date" name="gearDetails['+count+'][checkin]" value="" />';
            cell4.innerHTML = '<input type="text" name="gearDetails['+count+'][notes]" value="" />';
	          return false;
	        });

	    });
	    </script>
	</div>
<?php
	}


function ptn_scoutbook_gear_save_gear( $gear_id, $gear) {
    global $wpdb;

    // Check post type for gear_event
    if ( $gear>post_type == 'gear' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['gearName'] ) && $_POST['gearName'] != '' ) {
            update_post_meta( $gear_id, 'gearName', $_POST['gearName'] );
        }
        if ( isset( $_POST['gearStorage'] ) && $_POST['gearStorage'] != '' ) {
            update_post_meta( $gear_id, 'gearStorage', $_POST['gearStorage'] );
        }
        if ( isset( $_POST['gearDetails'] ) && $_POST['gearDetails'] != '' ) {
            update_post_meta( $gear_id, 'gearDetails', $_POST['gearDetails']);
        }
      }
    }

// Change title text
function wpb_change_title_text( $title ){
     $screen = get_current_screen();
     if  ( 'gear' == $screen->post_type ) {
          $title = 'Enter barcode here';
     }
     return $title;
}
add_filter( 'enter_title_here', 'wpb_change_title_text' );

// Move meta boxes above description
add_action('edit_form_after_title', function() {
    global $post, $wp_meta_boxes;
    do_meta_boxes(get_current_screen(), 'advanced', $post);
    unset($wp_meta_boxes[get_post_type($post)]['advanced']);
});

function ptn_scoutbook_gear_type_template($single_template) {
     global $post;

     if ($post->post_type == 'gear') {
          $single_template = dirname( __FILE__ ) . '/templates/single-gear.php';
     }
     return $single_template;
}
add_filter( 'single_template', 'ptn_scoutbook_gear_type_template' );

function ptn_scoutbook_gear_type_archive_template($archive_template) {
     global $post;
     if ($post->post_type == 'gear') {
          $archive_template = dirname( __FILE__ ) . '/templates/archive-gear.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'ptn_scoutbook_gear_type_archive_template' );

function ptn_scoutbook_gear_check_inout(){
    $gearName = get_post_meta($_POST['gearID'], 'gearName', true);
    $gearDetails = get_post_meta($_POST['gearID'], 'gearDetails', true);
    $orig_gearDetails = $gearDetails;
    if ($_POST['action'] == 'checkout'){
      $newdata =  array (
          'user' => $_POST['user'],
          'checkout' => $_POST['checkOut'],
          'notes' => $_POST['notes']
        );
      $gearDetails[] = $newdata;
      $gearInOut = "out";
    } else {
      end($gearDetails);
      $last_id = key($gearDetails);
      $gearDetails[$last_id]['checkin'] = $_POST['checkIn'];
      $newNotes =  $gearDetails[$last_id]['notes'].' '.$_POST['notes'];
      $gearDetails[$last_id]['notes'] = $newNotes;
      $gearInOut = "in";
    }
    $ptn_scoutbook_redirect = 'https://'.$_SERVER['SERVER_NAME'].'/wp-content/plugins/'.basename(__FILE__,'.php').'/';
    if (update_post_meta( $_POST['gearID'], 'gearDetails', $gearDetails, $orig_gearDetails)){
          $ptn_scoutbook_redirect .= "templates/success.php?id=".$_POST['gearID'];
    } else {
          $ptn_scoutbook_redirect .= "/templates/success.php?id=".$_POST['gearID'];
    }
    $ptn_scoutbook_redirect .= "&inout=".$gearInOut;
    ?><script>window.location.href = '<?php echo $ptn_scoutbook_redirect; ?>';</script> <?php
    exit;
}
add_action('admin_post_checkin', 'ptn_scoutbook_gear_check_inout');
add_action('admin_post_checkout', 'ptn_scoutbook_gear_check_inout');

add_filter('manage_gear_posts_columns', 'ptn_scoutbook_gear_table_head');
function ptn_scoutbook_gear_table_head($defaults){
  $defaults['name'] = 'Gear Name';
  $defaults['location'] = 'Gear Location';
  return $defaults;
}

add_action('manage_gear_posts_custom_column', 'ptn_scoutbook_gear_table_content', 10, 2);
function ptn_scoutbook_gear_table_content($column_name, $post_id){
  switch($column_name){
    case 'name':
      $meta_name = 'gearName';
      break;
    case 'location':
      $meta_name = 'gearStorage';
      break;
  }
  $column_data = get_post_meta($post_id, $meta_name, true);
  echo $column_data;
}

function ptn_scoutbook_gear_change_title_header($columns) {
	//unset($columns['checkbox']);
	$columns['title'] = 'Barcode ID';
	return $columns;
}
add_filter('manage_gear_posts_columns', 'ptn_scoutbook_gear_change_title_header');