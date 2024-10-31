<?php
global $lugar;
global $front_attrs;

$titulo = '';
$image = null;
$description = '';

if ($lugar) {
    $titulo = get_the_title($lugar);
    $image = get_the_post_thumbnail_url($lugar);
    $description = get_post_field('post_content', $lugar);
    
    $categories = get_the_terms( $lugar, 'category' );
    if ($categories) {
        $categories = $categories[0]; // the first one
    }
    
    $location = get_the_terms( $lugar, 'post_tag' );
    if ($location) {
        $location = $location[0]; // the first one
    } 
    
}
?>
<h1><?php echo get_the_title($lugar);?></h1>
<hr>
<form action="<?php echo get_the_permalink();?>" method="POST" enctype="multipart/form-data">

<div class="form-group">
	<?php 
	if ( !$front_attrs['exclude_thumbnail'] ) {
		if ( $image ) {
		    ?>
		    <img src="<?php echo esc_attr($image);?>" width="250"/>
		    <?php
		}
		?>
		<input type="file" name="imagen" class="form-control">
		
	<?php
	}
	?>
</div>
<div class="form-group">
	<label><?php _e('Title:', 'saas-generator');?></label>
	<input type="text" name="title" placeholder="<?php _e('Post title', 'saas-generator');?>" value="<?php echo esc_attr($titulo);?>" class="form-control"/>
</div>
    <?php 
    if ( !$front_attrs['exclude_description'] ) {
    ?>
    <div class="form-group">
    	<label><?php _e('Description:', 'saas-generator');?></label>
    	<?php 
    	$args = array(
    	    'quicktags' => false,
    	    'textarea_rows' => 5,
    	    'media_buttons' => false,
    	    'tinymce'       => array(
    	        'toolbar1'    => 'bold,italic,strikethrough,underline',
    	        'toolbar2'    => '',
    	    )
    	    , );
    	$editor_id = 'description';
    	wp_editor( $description, $editor_id, $args );
    ?>
    </div>
    <?php
    }
	
	// Taxonomies
	/*
	?>
	<span><?php _e('Category:', 'fronteditposts');?> </span>
	<?php
	// wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => "{$name}[]", 'selected' => $term_obj[0]->term_id, 'orderby' => 'name', 'hierarchical' => 0, 'show_option_none' => '&mdash;' ) );
	wp_dropdown_categories( array( 'taxonomy' => 'category', 'hide_empty' => 0, 'name' => "category", 'orderby' => 'name', 'selected' => $categories->term_id, 'hierarchical' => 1, 'show_option_none' => '&mdash;' ) );
	?>
	
	<span><?php _e('Tag:', 'fronteditposts');?> </span>
	<?php
	// wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => "{$name}[]", 'selected' => $term_obj[0]->term_id, 'orderby' => 'name', 'hierarchical' => 0, 'show_option_none' => '&mdash;' ) );
	wp_dropdown_categories( array( 'taxonomy' => 'post_tag', 'hide_empty' => 0, 'name' => "post_tag", 'orderby' => 'name', 'selected' => $location->term_id, 'hierarchical' => 1, 'show_option_none' => '&mdash;' ) );
    */
	
	// ACF
	if ( function_exists('acf_get_field_groups') ) {
	    $post_type = get_post_type($lugar);
	    $groups = acf_get_field_groups(array('post_type' => $post_type));
	    
	    if ( $groups ) {
	        foreach ( $groups as $group ) {
	            $fields = acf_get_fields($group['key']);
	            if ( $fields ) {
	                foreach ( $fields as $field ) {
	                    switch ($field['type']) {
	                        case 'text':
	                            ?>
	                            <div class="form-group">
	                            	<label><?php echo esc_html($field['label']);?></label>
	                            	<input type="text" name="<?php echo esc_attr($field['name']);?>" value="<?php echo esc_attr(get_field($field['name'], $lugar));?>" placeholder="<?php echo esc_attr($field['placeholder']);?>" class="form-control"/>
	                            </div>
	                            <?php
	                            break;
	                            
                            case 'select':
                                ?>
	                            <div class="form-group">
	                            	<label><?php echo esc_attr($field['label']);?></label>
	                            	<select name="<?php echo esc_attr($field['name']);?>" class="form-control">
    	                            <?php 
    	                            foreach ( $field['choices'] as $key=>$choice ) {
    	                               $selected = '';
    	                               if ( $key == get_field($field['name'], $lugar) ) {
    	                                   $selected = 'selected';
    	                               }
    	                               ?>
    	                               <option value="<?php echo esc_attr($key);?>" <?php echo $selected;?> ><?php echo esc_html($choice);?></option>
    	                               <?php   
    	                            }
    	                            ?>
    	                            </select>
	                            </div>
	                            <?php
	                            break;
                            case 'post_object':
                                ?>
                                <div class="form-group">
                                	<label><?php echo esc_html($field['label']);?></label>
                                <?php 
                                $multiple = '';
                                if ( $field['multiple'] ) {
                                    $multiple = 'multiple';    
                                }
                                ?>
	                            <?php 
	                            $objs = get_posts(array(
	                                'post_type' => $field['post_type']
	                            ));
	                            if ( $objs ) {
	                                // El campo no se puede llamar post_type, entra en conflicto
	                                ?>
                                	<select name="front_<?php echo esc_attr($field['name']);?>" <?php echo esc_attr($multiple);?> class="form-control">
	                                <?php
    	                            foreach ( $objs as $obj ) {
    	                               $selected = '';
    	                               if ( $obj->ID == get_field('front_' .$field['name'], $lugar) ) {
    	                                   $selected = 'selected';
    	                               }
    	                               ?>
    	                               <option value="<?php echo intval($obj->ID);?>" <?php echo $selected;?> ><?php echo get_the_title($obj);?></option>
    	                               <?php   
    	                            }
    	                            ?>
	                            	</select>
                                	<?php
	                            }
	                            ?>
	                            </div>
	                            <?php
                                break;
                            case 'true_false':
                                $selected = '';
                                if ( get_field($field['name'], $lugar) ) {
                                    $selected = 'checked';
                                }
                                ?>
                                <div class="form-group">
	                            	<input type="checkbox" name="<?php echo esc_attr($field['name']);?>" <?php echo $selected;?> class="form-control">
	                            	<label><?php echo esc_html($field['label']);?></label>
	                            </div>
	                            <?php
                                break;
                            default:
                                //var_dump($field);
                                break;
	                            
	                    }
	                }
	            }
	        }
	    }
	}
    ?>
    
    <input type="hidden" name="post_id" value="<?php echo isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;?>" />

	<?php wp_nonce_field('saas-edit-post', 'saas-edit-post');?>

	<input type="hidden" name="action" value="save" />
	<input type="submit" name="save" value="<?php _e('Save', 'saas-generator');?>" class="btn btn-primary"/>
    
</form>


