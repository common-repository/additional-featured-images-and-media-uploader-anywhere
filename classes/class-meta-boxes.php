<?php
/**
 * class-meta-boxes.php
 * 
 * All things metaboxes
 */

 class Additional_Featured_Images_Meta_Boxes{
    //construct
    function __construct(){
        if (is_admin()) {
            add_action('load-post.php', [$this, 'init_metabox']);
            add_action('load-post-new.php', [$this, 'init_metabox']);
        }
    }

    //Meta box initialization.
    public function init_metabox() {
        add_action('add_meta_boxes', [$this, 'add_metabox']);
        add_action('save_post', [$this, 'save_metabox'], 0, 2);
    }
    
    //Adds the meta box.
    public function add_metabox() {
            //switch to options selections
            $post_types = ['post','page'];
            add_meta_box(
                'gallery-metabox',
                __('Additional Featured Images', 'Additional_Featured_Images'),
                [$this, 'gallery_metabox'],
                $post_types,
                'side',
                'low'
            );
    }

    //Renders metabox
    public function gallery_metabox($post) {
        $core = new Additional_Featured_Images_Core();
        $current_screen = get_current_screen();
        $gallery_images = get_post_meta($post->ID, 'additional_featured_images_ids', true);       
        $gallery_images_object = isset($gallery_images) && !empty($gallery_images) ? json_decode($gallery_images) : new stdClass();

        //sort by order number
        $gallery_images_object = $core->objectSortByKey($gallery_images_object, 'order');

        wp_nonce_field('Additional_Featured_Images_nonce_action', 'Additional_Featured_Images_nonce');
        //create container
        ?>
        <div id="form_wrap" class="row">
            <div class="input-wrap-gallery">
                <?php if(method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) : ?>
                    <button class="components-button upload_image_button editor-post-featured-image__toggle">Add More Featured Images</button>
                <?php else : ?>
                    <input class="upload_image_button button upload-button" type="button" value="Add More Featured Images"/>
                <?php endif; ?>
                <input type="hidden" name="additional_featured_images_ids" class="additional_featured_images_ids" value="<?php echo $gallery_images ? esc_attr($gallery_images) : ''; ?>">
            </div>
            <div class="image-flex-wrap">
                <p>Drag to Reorder - Update to save</p>
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
            <label>Single Image:</label>
            <p><em>Image ID can be found on image thumbnail above.</em></p>
            <code style="width:95%; text-align:center; display:block; margin:5px 0;">[AFI_single_image id=""] <em>Pro Only</em></code>
            <label>Gallery/Slideshow:</label>
            <code style="width:95%; text-align:center; display:block; margin:5px 0;">[featured_image_gallery]</code>
        </div>
        <?php
    }
    
    public function save_metabox($post_id, $post) {
        global $post_type;
        $core = new Additional_Featured_Images_Core();
        $post_types = $core->getSelectedPostTypes();
        if(in_array($post_type, $post_types)){
            //security and authentication.
            $nonce_name = isset($_POST['Additional_Featured_Images_nonce']) ? $_POST['Additional_Featured_Images_nonce'] : '';
            $nonce_action = 'Additional_Featured_Images_nonce_action';
            if (!isset($nonce_name) || !wp_verify_nonce($nonce_name, $nonce_action) || !current_user_can('edit_post', $post_id) || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
                return;
            }
            if (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE){
                return;
            }
            if(isset($_POST['additional_featured_images_ids'])){
                update_post_meta($post_id, 'additional_featured_images_ids', sanitize_text_field($_POST['additional_featured_images_ids']));
            }
        }
    }
 }
 new Additional_Featured_Images_Meta_Boxes();