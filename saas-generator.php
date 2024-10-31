<?php
/**
 * Plugin Name: Saas Generator
 * Plugin URI: https://eggemplo.com
 * Description: Create the Saas that you need.
 * Version: 1.0.1
 * Author: ablancodev
 * Author URI: https://www.ablancodev.com
 * Text Domain: saas-generator
 * Domain Path: /languages
 */

add_action( 'init', 'saas_init' );

function saas_init() {
    
    // Imports
    require_once 'view/shortcodes.php';
    
    // Settings
    add_action ( 'admin_menu', 'saas_admin_menu', 40 );

    load_plugin_textdomain ( 'saas-generator', null, 'saas-generator/languages' );

    // Scripts
    add_action( 'wp_enqueue_scripts', 'saas_shortcode_scripts');
}

// Bootstrap
function saas_shortcode_scripts() {
    global $post;
    if( apply_filters('saas-add-bootstrap', true) && has_shortcode( $post->post_content, 'saas') ) {
        wp_register_script('saas-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.1.3', true);
        wp_enqueue_script( 'saas-bootstrap');

        wp_enqueue_style( 'saas-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',false,'5.1.3','all');
    }
}



// Settings
function saas_admin_menu() {
    add_menu_page (
        __ ( 'Saas settings', 'saas-generator' ),
        __ ( 'Saas settings', 'saas-generator' ),
        'manage_options', 
        'saas',
        'saas_menu_settings',
        'dashicons-lightbulb'
    );
}
function saas_menu_settings() {
    ?>
    <h1>Settings</h1>
    <h2>You have available these Shortcodes:</h2>
    <p><strong>[saas]</strong></p>
    <p>Params:</p>
    <ul>
    	<li>cpt</li>
    	<li>exclude_thumbnail</li>
    	<li>exclude_description</li>
    </ul>
    <?php
}

?>