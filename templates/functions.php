<?php

/* Sparkling - child */


/* Simplify Admin bar*/
add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );

function remove_wp_logo( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
	$wp_admin_bar->remove_node('comments');
}

/* Simplify Admin Screens */
add_action('admin_head', 'unused_meta_boxes');
function unused_meta_boxes() {
	remove_meta_box('commentstatusdiv','post','side');
	remove_meta_box('commentsdiv','post','side');
	remove_meta_box('postcustom','post','side');
	remove_meta_box('slugdiv','post','side');
	remove_meta_box('postexcerpt','post','side');
	remove_meta_box('tagsdiv-post_tag','post','side');
	remove_meta_box('trackbacksdiv','post','side');
	remove_meta_box('postcustom','post','side');
}

/* Redirect to home page after delete */
/* Will later change this to redirect to category page */
add_action('trashed_post','my_trashed_post_handler',10,1);
function my_trashed_post_handler($post_id)
{
	$sparkling_referer = wp_get_referer();
	if (strpos($sparkling_referer, 'wp-admin') == FALSE){
		$categories = get_the_category($post_id);
		if (!empty($categories)){
			$location = site_url().'/topics/'.$categories[0]->slug;
			wp_redirect($location);
			exit;
		}
    	wp_redirect( get_option('siteurl') );
    	exit;
	}
}



/* Dynamic Patrol Menus */

/* Dynamically add Patrol directories */
add_filter( 'wp_nav_menu_items', 'add_patrol_menu_item', 10, 2 );
function add_patrol_menu_item ( $items, $args ) {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	if (is_plugin_active('scouttroop/scouttroop.php')){
		$list_of_patrols = patrol_list_func("ARRAY");
		$new_menu_items = '>Patrol Directory</a>';
//		$new_menu_items .= '<ul class="sub-menu">';
		$new_menu_items .= '<ul role="menu" class=" dropdown-menu">';
	
		if (is_array($list_of_patrols)){
			foreach ($list_of_patrols as $patrol){
				// $new_menu_items .= '<li class="dynamic-patrol-list menu-item menu-item-type-custom menu-item-object-custom"><a href="http://troop351.org/patrol-dir/?'.$patrol.'">'.$patrol.'</a></li></li>';
				$new_menu_items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="http://troop351.org/patrol-dir/?'.$patrol.'">'.$patrol.'</a></li></li>';
			}
			$new_menu_items .= '</ul>';

			$items = str_replace(">Patrols</a>", $new_menu_items, $items);
		}
	} 
		return $items;
}

/* Remove authors last name when user is unauthenticated */
add_filter('the_author', ptn_author_security);
function ptn_author_security(){

	global $authordata;	
	
	$name = explode(' ',$authordata->data->display_name);
	if (is_user_logged_in()){
		$author = $authordata->data->display_name;
	}else{
		$author = $name[0].' '.$name[1][0];		
	}
	return $author;
}

// Filter to fix the Post Author Dropdown
add_filter('wp_dropdown_users', 'theme_post_author_override');
function theme_post_author_override($output)
{
  // return if this isn't the theme author override dropdown
  if (!preg_match('/post_author_override/', $output)) return $output;

  // return if we've already replaced the list (end recursion)
  if (preg_match ('/post_author_override_replaced/', $output)) return $output;

  // replacement call to wp_dropdown_users
	$output = wp_dropdown_users(array(
	  'echo' => 0,
		'name' => 'post_author_override_replaced',
		'selected' => get_current_user_id(),
		'include_selected' => true
	));
		
	// put the original name back
	$output = preg_replace('/post_author_override_replaced/', 'post_author_override', $output);

  return $output;
}



/**
 * Add First Name, Last Name and Phone Number to Registration Page
*/
add_action( 'register_form', 'myplugin_register_form',5 );
function myplugin_register_form() {

    $first_name = ( ! empty( $_POST['first_name'] ) ) ? trim( $_POST['first_name'] ) : '';
    $last_name  = ( ! empty( $_POST['last_name'] ) ) ? trim( $_POST['last_name'] ) : '';
        
        ?>
        <p>
            <label for="first_name"><?php _e( 'First Name', 'mydomain' ) ?><br />
                <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr( wp_unslash( $first_name ) ); ?>" size="25" /></label>
        </p>
                <p>
            <label for="last_name"><?php _e( 'Last Name', 'mydomain' ) ?><br />
                <input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr( wp_unslash( $last_name ) ); ?>" size="25" /></label>
        </p>
        <p>
			<label for="phone"><?php _e("Phone") ?><br/>
			<input type="phone" name="phone" id="phone" class="input" value="<?php echo esc_attr($_POST['phone']); ?>" size="25" tabindex="21" />
			</label>
		</p>
        <p>
			<label for="description"><?php _e("Please briefly describe your relationship with Troop 351") ?><br/>
			<textarea type="text" name="description" id="description" class="input" value="<?php echo esc_textarea($_POST['description']); ?>" rows="4" cols="50"></textarea>
			</label>
		</p>
        <?php
    }

    add_filter( 'registration_errors', 'myplugin_registration_errors', 10, 3 );
    function myplugin_registration_errors( $errors, $sanitized_user_login, $user_email ) {
        
        if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
            $errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', 'mydomain' ) );
        }
        if ( empty( $_POST['last_name'] ) || ! empty( $_POST['last_name'] ) && trim( $_POST['last_name'] ) == '' ) {
            $errors->add( 'last_name_error', __( '<strong>ERROR</strong>: You must include a last name.', 'mydomain' ) );
        }
        if ( empty( $_POST['phone'] ) || ! empty( $_POST['phone'] ) && trim( $_POST['phone'] ) == '' ) {
            $errors->add( 'phone_error', __( '<strong>ERROR</strong>: You must include a phone number.', 'mydomain' ) );
        }
        if ( empty( $_POST['description'] ) || ! empty( $_POST['description'] ) && trim( $_POST['description'] ) == '' ) {
            $errors->add( 'description_error', __( '<strong>ERROR</strong>: For the saftey of our scouts, we require a description.', 'mydomain' ) );
        }
        return $errors;
    }

    add_action( 'user_register', 'myplugin_user_register' );
    function myplugin_user_register( $user_id ) {
        if ( ! empty( $_POST['first_name'] ) ) {
            update_user_meta( $user_id, 'first_name', trim( $_POST['first_name'] ) );
        }
        if ( ! empty( $_POST['last_name'] ) ) {
            update_user_meta( $user_id, 'last_name', trim( $_POST['last_name'] ) );
        }
        if (! empty($_POST['phone'])){
			update_user_meta($user_id, 'phone', trim($_POST['phone']));
		}
		if (! empty($_POST['description'])){
			update_user_meta($user_id, 'description', trim($_POST['description']));
		}
    }

/* Remove non-needed admin menu items for editors */
add_action('admin_init', 'my_remove_menu_pages');
function my_remove_menu_pages(){
	
	global $user_ID;
	if ( current_user_can( 'editor')) {
		remove_menu_page('edit-comments.php');
		remove_menu_page('tools.php');
		remove_menu_page('wpcf7');
	}
}

/* Email on new post */
add_action( 'save_post', 'send_email' );
function send_email( $post_id ) {
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
		$return;
	}
	if (false !== wp_is_post_revision($post_id)){
		return;
	}

	$category = get_the_category($post_ID); 
	$the_category = $category[0]->cat_name;
	if ($the_category == 'Troop Updates'){	
		$post_url = get_permalink( $post_id );
		$content_post = get_post($post_id);
		$email = 'phil.newman@gmail.com';
		//sends email
		wp_mail($email, $content_post->post_title, $content_post->post_content );
	}
	return;
}


/* Add widget area to header for login stuff */
//add_action( 'after_setup_theme', 'child_theme_setup' );

if ( !function_exists( 'child_theme_setup' ) ):
function child_theme_setup() {

	register_sidebar( array(
		'name' => __( 'Horizontal Widget Area One', 'sparking' ),
		'id' => 'horizontal-1',
		'description' => __( 'An optional horizontal widget area', 'sparkling' ),
	) );

}
endif;


/**
 * Override parent menu to allow submenus
 */
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'sparkling-bootstrap', get_template_directory_uri() . '/inc/css/bootstrap.min.css' );
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

function sparkling_header_menu(){
	wp_nav_menu(array(
		'menu' 				=>	'primary',
		'theme_location'	=>	'primary',
		'depth'				=>	6,
		'container'			=> 'div',
		'container_class' 	=> 'collapse navbar-collapse navbar-ex1-collapse',
		'menu_class'		=>	'nav navbar-nav',
		'fallback_cb'		=>	'wp_bootsrap_navwalker::fallback',
		'walker'			=>	new wp_bootstrap_navwalker()
	));
}

/** 
* Turn off WP Admin Bar
**/
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}


?>