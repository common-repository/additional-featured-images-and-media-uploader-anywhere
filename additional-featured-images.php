<?php
/**
 * Plugin Name:       Additional Featured Images and Media Uploader Anywhere
 * Description:       Add additional featured images to any post type and display using either a built in image gallery/slideshow shortcode or by using a single image shortcode. Most plugins or developers use the non Javasript API media uploader, which is notorious for being glitchy and slow.  This leverages the not well known WordPress Javascript API to mimic the built in media uploader.  Great for end users or developers.
 * Plugin URI:        https://metawebdevelopment.com/product/additional-featured-images-and-media-upload-anywhere/
 * Version:           1.0.0
 * Author:            Andre de Almeida
 * Author URI:        https://www.andredealmeida.com
 * Requires at least: 5.4.2
 * Tested up to:      5.4.2
 *
 * @package Additional_Featured_Images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Additional_Featured_Images Class
 *
 * @class Additional_Featured_Images
 * @version	1.0.0
 * @since 1.0.0
 * @package	Additional_Featured_Images
 */
final class Additional_Featured_Images_Main_Class {

	/**
	 * Set up the plugin
	 */
	public function __construct() {
        add_action('init', [$this, 'Additional_Featured_ImagesSetup'], -1);
        require_once('classes/class-core.php');
        require_once('classes/class-meta-boxes.php');
        require_once('classes/class-settings.php');
        require_once('classes/class-shortcodes.php');
    }

	/**
	 * Setup all the things
	 */
	public function Additional_Featured_ImagesSetup() {
		add_action('wp_enqueue_scripts', [$this, 'Additional_Featured_Images_css'], 999);
        add_action('wp_enqueue_scripts', [$this, 'Additional_Featured_Images_js']);
        add_action('admin_enqueue_scripts', [$this, 'Additional_Featured_Images_admin_js']);
        add_action('admin_enqueue_scripts', [$this, 'Additional_Featured_Images_admin_css']);
	}

	/**
	 * Enqueue the CSS
	 *
	 * @return void
	 */
	public function Additional_Featured_Images_css() {
        wp_enqueue_style('additional-featured-images-css', plugins_url( '/css/style.css', __FILE__ ));
        //replace with otpions
        $core = new Additional_Featured_Images_Core();
        $post_types = $core->getSelectedPostTypes();
        foreach($post_types as $post_type){
            if(get_post_type() === $post_type){
                wp_enqueue_style('slick_css', plugins_url( '/slick/slick.css', __FILE__ ));
                wp_enqueue_style('slick_theme_css', plugins_url( '/slick/slick-theme.css', __FILE__ ));
            }
        }
	}

	/**
	 * Enqueue the Frontend Javascript
	 *
	 * @return void
	 */
	public function Additional_Featured_Images_js() {
        //replace with otpions
        $core = new Additional_Featured_Images_Core();
        $post_types = $core->getSelectedPostTypes();
        foreach($post_types as $post_type){
            if(get_post_type() === $post_type){
                wp_enqueue_script('slick_js', plugins_url('/slick/slick.min.js', __FILE__), ['jquery']);wp_enqueue_script('addtional_featured_images_js', plugins_url('/js/front-end.js', __FILE__), ['jquery']);
                $args = [
                    'autoplay' => get_option('_additional_featured_image_slideshow_autoplay', 'yes'),
                    'speed' => get_option('_additional_featured_image_slideshow_speed', 5000),
                    'show' => get_option('_additional_featured_image_slideshow_show', 1),
                    'scroll' => get_option('_additional_featured_image_slideshow_scroll', 1),
                    'arrows' => get_option('_additional_featured_image_slideshow_arrows', 'yes'),
                    'dots' => get_option('_additional_featured_image_slideshow_show', 'yes'),
                ];
                wp_localize_script('slick_js', 'slickSettings', $args);
            }
        }
	}

    /**
	 * Enqueue the Admin Javascript
	 *
	 * @return void
	 */
	public function Additional_Featured_Images_admin_js() {
        //load on all admin pages
        if(!did_action('wp_enqueue_media')){
            wp_enqueue_media();
        }
        wp_enqueue_script('additional-featured-images-admin-media-js', plugins_url('/js/media-uploader.js', __FILE__), ['jquery']);
    }
    
    /**
	 * Enqueue the Admin CSS
	 *
	 * @return void
	 */
	public function Additional_Featured_Images_admin_css() {
        $stylesheets = get_option('_additional_featured_image_stylesheets', '');
        if($stylesheets === 'yes'){
            wp_enqueue_style('additional-featured-images-admin-css', plugins_url('/css/admin.css', __FILE__));
        }
	}
    
} // End Class

/**
 * The 'main' function
 *
 * @return void
 */
function Additional_Featured_Images_Main_Init(){
	new Additional_Featured_Images_Main_Class();
}

/**
 * Backend function to add media uploader
 *
 */
function AFI_media_uploader($args) {
    //create media uploader
    if(!is_admin()){
        return 'Media uploader is only available in WordPress admin.';
    }
    $core = new Additional_Featured_Images_Core();
    $core->createMediaUploader($args);
}

/**
 * Initialise the plugin
 */
add_action('plugins_loaded', 'Additional_Featured_Images_Main_Init');

//activation hook
function additional_featured_image_activate_plugin(){
    update_option('_additional_featured_image_post_types', ['post' => 'yes', 'page' => 'yes']);
    update_option('_additional_featured_image_stylesheets', 'yes');
    update_option('_additional_featured_images_names', []);
    update_option('_additional_featured_image_slideshow_speed', 5000);
    update_option('_additional_featured_image_slideshow_show', 1);
    update_option('_additional_featured_image_slideshow_scroll', 1);
    update_option('_additional_featured_image_slideshow_arrows', 'yes');
    update_option('_additional_featured_image_slideshow_dots', 'yes');
    update_option('_additional_featured_image_slideshow_autoplay', 'yes');
}
register_activation_hook(__FILE__, 'additional_featured_image_activate_plugin');

//deactivation hook
function additional_featured_image_deactivate_plugin(){
}

register_deactivation_hook(__FILE__, 'additional_featured_image_deactivate_plugin');