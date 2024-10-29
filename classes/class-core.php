<?php
/**
 * Core Class
 * 
 * @package Additional_Featured_Images
 */

class Additional_Featured_Images_Core {

    public function __construct(){
        add_action('save_post', [$this, 'saveAdditionalImages'], 0, 2);
    }

    /**
     * 
     * @param $args['type']
     * @param $args['name']
     * @param $args['button_value']
     * 
     * @return String
     */
    public function createMediaUploader($args){
        //validate args
        if(!$args || !isset($args['type']) || empty($args['type']) || !isset($args['name']) || empty($args['name'])){
            return 'Required parameters not found.';
        }

        $args['name'] = sanitize_text_field($args['name']);
        $args['button_value'] = isset($args['button_value']) ? sanitize_text_field($args['button_value']) : 'Add Additional Images';

        //if type is option update option on post
        if($args['type'] === 'option' && isset($_POST)){
            if(isset($_POST[$args['name'] . '_additional_featured_images_ids'])){
                update_option($args['name'] . '_additional_featured_images_ids', sanitize_text_field($_POST[$args['name'] . '_additional_featured_images_ids']));
            }
        }

        //update option with name for loop in save post
        $names = get_option('_additional_featured_images_names', []);
        $names[] = $args['name'];

        //create media uploader
        $core = new Additional_Featured_Images_Core();
        $gallery_images = $args['type'] === 'option' ? stripslashes_deep(get_option($args['name'] . '_additional_featured_images_ids', false)) : get_post_meta(get_the_ID(), $args['name'] . '_additional_featured_images_ids', true);
        $gallery_images_object = isset($gallery_images) && !empty($gallery_images) ? json_decode($gallery_images) : new stdClass();

        //sort by order number
        $gallery_images_object = $core->objectSortByKey($gallery_images_object, 'order');
        wp_nonce_field('Additional_Featured_Images_nonce_action', 'Additional_Featured_Images_nonce');
        ?>
        <div class="input-wrap-gallery">
            <input class="upload_image_button button upload-button" type="button" value="<?php echo $args['button_value']; ?>"/>
            <input type="hidden" name="<?php echo $args['name'] . '_additional_featured_images_ids'; ?>" class="additional_featured_images_ids" value="<?php echo $gallery_images ? esc_attr($gallery_images) : '' ?>">
        </div>
        <div class="image-flex-wrap">
            <?php 
                 foreach($gallery_images_object as $id => $url){
                    ?>  
                        <div style="position:relative;" class="image-flex">
                            <img src="<?php echo isset($url) && isset($url->url) ? $url->url : '' ?>">
                            <a data-order="<?php echo isset($url) && isset($url->order) ? $url->order : '' ?>" data-id="<?php echo isset($id) ? $id : '' ?>" class="image_remove" href="#">remove</a>
                            <div style="position:absolute; color: #FFF; top: 1px; right: 5px; font-weight: bold; text-shadow: 0px 0px 3px #000"><?php echo isset($id) ? $id : '' ?></div>
                        </div>
                    <?
                }
            ?>
        </div>
        <?php
    }

    public function saveAdditionalImages($post_id, $post){
        $nonce_name = isset($_POST['Additional_Featured_Images_nonce']) ? $_POST['Additional_Featured_Images_nonce'] : '';
        $nonce_action = 'Additional_Featured_Images_nonce_action';
        if(!isset($nonce_name) || !wp_verify_nonce($nonce_name, $nonce_action) || !current_user_can('edit_post', $post_id) || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)){
            return;
        }
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
            return;
        }
        $post_types = $this->getSelectedPostTypes();
        if(in_array(get_post_type(), $post_types)){
            //get options of list of the names of added media uploader and then loop through each.
            $names = get_option('_additional_featured_images_names', []);
            foreach($names as $name){
                if(isset($_POST[$name . '_additional_featured_images_ids'])){
                    update_post_meta($post_id, $name . '_additional_featured_images_ids', sanitize_text_field($_POST[$name . '_additional_featured_images_ids']));
                }
            }    
        }
        //wipe names to ensure array does not become bloated
        update_option('_additional_featured_images_names', []);
    }

    /**
     * Get selected post types
     * 
     * @return Array
     */
    public function getSelectedPostTypes(){
        $types = [];
        foreach(get_option('_additional_featured_image_post_types', []) as $post_type => $selected){
            if($selected === 'yes'){
                $types[] = $post_type;
            }
        };
        return $types;
    }

    /**
     * 
     * Sort object by property value
     * @param $object
     * @param $key
     * @return Object
     */
    public function objectSortByKey($object, $key){
        $sorted = new stdClass();
        $order = [];
        foreach($object as $objectKey => $value){
            $order[$objectKey] = $value->{$key}; 
        }
        uasort($order, function($a, $b){
            return $a <=> $b;
        });
        foreach($order as $objectKey => $value){
            $sorted->{$objectKey} = $object->{$objectKey};
        }
        return $sorted;
    }

}

new Additional_Featured_Images_Core();