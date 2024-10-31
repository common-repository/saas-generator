<?
class Saas_Shortcodes {

	public static function init() {
	    // CPT page
	    add_shortcode( 'saas', array( __CLASS__, 'shortcode_saas') );	
	}

	public static function shortcode_saas( $atts, $content = null ) {

	    global $lugar;
	    global $front_attrs;


	    $front_attrs = shortcode_atts( array(
	        'cpt' => 'post',
	        'exclude_thumbnail' => false,
	        'exclude_description' => false
	    ), $atts );

	    if ( $front_attrs['exclude_thumbnail'] == "true" ) {
	        $front_attrs['exclude_thumbnail'] = true;
	    }
	    
	    if ( $front_attrs['exclude_description'] == "true" ) {
	        $front_attrs['exclude_description'] = true;
	    }
	    	    
	    
	    // get the id
	    $lugar_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;
	    $lugar = null;
	    if ( $lugar_id ) {
	        $lugar = get_post($lugar_id);
	    }
	    
        $action = isset($_REQUEST['action']) ? esc_attr($_REQUEST['action']) : '';
        	        
        switch ($action) {
            case "edit":
            case "new":
                ob_start();
                include(plugin_dir_path( __FILE__ ) . 'edit-post.php');
                return ob_get_clean();
                break;
            case "delete":
                wp_delete_post( $lugar_id );
                echo "Eliminado";
                break;
            case "save":
                
                if ( ! isset( $_POST['saas-edit-post'] ) || ! wp_verify_nonce( $_POST['saas-edit-post'], 'saas-edit-post' ) ) {
                	break;
                }
                
                do_action('fronteditpost_cpt_saved', $lugar); // Hook
                
                // Guardamos el car si tiene lugar_id
                if ( $lugar ) {
                    echo '<div classs="notice">' . __('Updated', 'saas-generator') . '</div>';
                    $my_post = array(
                        'ID'           => $lugar_id,
                        'post_title'   => sanitize_text_field($_REQUEST['title']),
                        'post_content' => wp_kses_post($_REQUEST['description'])
                    );
                    wp_update_post( $my_post );
	                //update_field('kms', intval($_REQUEST['kms']), $lugar_id);
	            } else {
                    // Creamos nuevo coche
                    echo '<div classs="notice"' . __('New created', 'saas-generator') . '</div>';
                    $my_post = array(
                        'post_title'    => sanitize_text_field($_REQUEST['title']),
                        'post_content'  => wp_kses_post($_REQUEST['description']),
                        'post_status'   => 'publish',
                        'post_type'     => $front_attrs['cpt']
                    );
                    
                    // Insert the post into the database.
                    $lugar_id = wp_insert_post( $my_post );
                    
                }
                if ( $lugar_id ) {  // edit or new
                    //insert attachments
                    if ($_FILES) {
                        array_reverse($_FILES);
                        $i = 0;//this will count the posts
                        foreach ($_FILES as $file => $array) {
                            if ($i == 0) $set_feature = 1; //if $i ==0 then we are dealing with the first post
                            else $set_feature = 0; //if $i!=0 we are not dealing with the first post
                            self::insert_attachment($file,$lugar_id, $set_feature);
                            $i++; //count posts
                        }
                    }
                    
                    // taxonomies
                    $categorie = isset($_REQUEST['category']) ? intval($_REQUEST['category']) : 0;
                    if ($categorie) {
                        wp_set_post_terms( $lugar_id, array($categorie), 'category' );
                    }
                    $location = isset($_REQUEST['post_tag']) ? intval($_REQUEST['post_tag']) : 0;
                    if ($location) {
                        wp_set_post_terms( $lugar_id, array($location), 'post_tag' );
                    }
                    
                    
                    // ACF
                    if ( function_exists('acf_get_field_groups') ) {
	                    $post_type = get_post_type($lugar_id);
	                    $groups = acf_get_field_groups(array('post_type' => $post_type));
	                    
	                    if ( $groups ) {
	                        foreach ( $groups as $group ) {
	                            $fields = acf_get_fields($group['key']);
	                            if ( $fields ) {
	                                foreach ( $fields as $field ) {
	                                    if ( isset($_REQUEST[$field['name']]) ) {
    	                                    switch ($field['type']) {
    	                                        case 'text':
    	                                            update_field( $field['name'], sanitize_text_field($_REQUEST[$field['name']]), $lugar_id );
    	                                            break;
    	                                        case 'select':
    	                                            update_field( $field['name'], sanitize_text_field($_REQUEST[$field['name']]), $lugar_id );
    	                                            break;
    	                                        case 'post_object':
    	                                            update_field( 'front_' . $field['name'], sanitize_text_field($_REQUEST['front_' . $field['name']]), $lugar_id );
    	                                            break;
    	                                        case 'true_false':
    	                                            update_field( $field['name'], sanitize_text_field($_REQUEST[$field['name']]), $lugar_id );
    	                                            break;
    	                                    }
	                                    }
	                                }
	                            }
	                        }
	                    }
                    }                
                   
                }
                break;
        }
	    
	    // Si pasa todos los returns, mostramos el listado de coches
        $cars = get_posts(array(
            'post_type'   => $front_attrs['cpt'],
			'numberposts' => -1,
            'author'      =>  get_current_user_id()
		));
        
        $output = '<a href="' . get_the_permalink() . '/?action=new" class="btn btn-primary">' . __('Add new', 'saas-generator') . '</a>';
        
		if ( $cars ) {
			$output .= '
			<table class="table">
			  <thead>
			    <tr>
			      <th scope="col">#</th>
			      <th scope="col">' . __('Title', 'saas-generator') . '</th>
			      <th scope="col">' . __('Actions', 'saas-generator') . '</th>
			    </tr>
			  </thead>
			  <tbody>';
		  	foreach ( $cars as $car ) {
		  		$output .= '
			    <tr>
			      <th scope="row">' . $car->ID . '</th>
			      <td><a href="' . get_the_permalink($car->ID) . '">' . get_the_title($car) . '</a></td>
			      <td>
			      	<a href="' . get_the_permalink() . '/?post_id=' . $car->ID . '&action=edit">' . __('Edit', 'saas-generator') . '</a>
			      	<a href="' . get_the_permalink() . '/?post_id=' . $car->ID . '&action=delete">' . __('Delete', 'saas-generator') . '</a>
			      </td>
			    </tr>';
		    }
			$output .= '
			  </tbody>
			</table>';
		}
		return $output;
	} 

	//attachment helper function
	public static function insert_attachment($file_handler,$post_id,$setthumb='false') {
	    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK){ return __return_false();
	    }
	    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	    
	    $attach_id = media_handle_upload( $file_handler, $post_id );
	    //set post thumbnail if setthumb is 1
	    if ($setthumb == 1) update_post_meta($post_id,'_thumbnail_id',$attach_id);
	    return $attach_id;
}	
}
Saas_Shortcodes::init();



