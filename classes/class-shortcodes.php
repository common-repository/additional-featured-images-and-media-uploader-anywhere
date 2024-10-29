<?php
/**
 * Shortcodes Class
 * 
 * @package Additional_Featured_Images
 */

class Additional_Featured_Images_Shortcodes {
    
    public function __construct() {
        add_shortcode('featured_image_gallery', [$this, 'featuredImageGallery']);
    }
  
    /**
     * Create location shortcode
     * 
     * @param $atts
     * @return void
     */

    public function featuredImageGallery(){
        $post_id = get_the_ID();
        if(!$post_id && !is_admin()){
            return '';
        }
        $core = new Additional_Featured_Images_Core();
        $images = json_decode(get_post_meta($post_id, 'additional_featured_images_ids', true));
        $images = $core->objectSortByKey($images, 'order');
        //generate carousel html
        ob_start();
        include(WP_PLUGIN_DIR . '/additional-featured-images/templates/carousel-loop.php');
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}

new Additional_Featured_Images_Shortcodes();