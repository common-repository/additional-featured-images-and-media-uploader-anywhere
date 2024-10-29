<?php
/**
 * Additional Featured Images Settings Class
 * 
 * @package Additional_Featured_Images
 */

class Additonal_Featured_Images_Settings {
    //paid version features 
    /*
        image reorder
        shortcode for single image
        custom post type support
        use media uploader pro for multi images - remove option for multi from regular
        add update functions
    */


    public function __construct() {
        add_action('admin_menu', [$this,'pluginSettingsOptions']);
    }

    public function pluginSettings(){
        $post_types = ['post' => 'post', 'page' => 'page'];
        ?>
        <h1><?php _e('Additional Featured Images Settings', 'Additional_Featured_Images'); ?></h1>
        <style>
            label:not(.checkbox) {
                width: 200px;
                display: inline-block;
                margin-bottom: 10px;
            }
            .input-wrap {
                margin:10px 0;
            }
            .input-wrap input:not(.checkbox){
                width: 400px;
                margin:2px 0;
                padding: 5px 10px;
            }
            .notice {
                margin:10px 20px 10px 0 !important;
            }
            textarea {
                width: 400px;
                min-height: 150px;
            }
            hr {
                margin: 20px 20px 0 0;
            }
            input.checkbox {
                margin: 0;
            }
            #wpcontent {
                background-color: #FFF;
            }
            h1 {
                margin-bottom: 30px;
            }
        </style>
        <?php
            if($_POST){
                $nonce_name = isset($_POST['Additional_Featured_Images_nonce']) ? $_POST['Additional_Featured_Images_nonce'] : '';
                $nonce_action = 'Additional_Featured_Images_nonce_action';
                $error = false;
                $msg = __('Settings saved!', 'Additional_Featured_Images');
                if ((isset($nonce_name) || wp_verify_nonce($nonce_name, $nonce_action)) && isset($_POST)) {
                    $additional_featured_image_post_types = [];
                    if(is_array($_POST['additional_featured_image_post_types'])){
                        foreach($_POST['additional_featured_image_post_types'] as $key => $post_type){
                            $additional_featured_image_post_types[$key] = sanitize_text_field($post_type); 
                        }
                    }
                    $additional_featured_image_stylesheets = sanitize_text_field($_POST['additional_featured_image_stylesheets']);
                    if(!$error){
                        update_option('_additional_featured_image_post_types', $additional_featured_image_post_types);
                        update_option('_additional_featured_image_stylesheets', $additional_featured_image_stylesheets);
                    }  
                }
                ?>
                <div class="notice <?php echo $error ? 'notice-error' : 'notice-success'; ?> is-dismissible">
                    <p><?php echo $msg ?></p>
                </div>
            <?php }
            $additional_featured_image_post_types = get_option('_additional_featured_image_post_types', '');
            $additional_featured_image_stylesheets = get_option('_additional_featured_image_stylesheets', '');
        ?>
        <form method="post" id="settings" action="" style="max-width: 600px; float:left;">
        
        <h2>Post Types</h2>
        <p>Select the post types to add extra images media uploader to.  Custom Post Types are availabe in the <a target="_blank" href="https://metawebdevelopment.com/product/additional-featured-images-and-media-upload-anywhere/">PRO Version</a>.</p>
        <div class="input-wrap">
            <?php foreach($post_types as $key => $post_type) : ?>
            <label class="checkbox" for="additional_featured_image_post_types[<?php echo $key; ?>]"><?php echo esc_html($post_type) ?></label>
            <input class="checkbox" id="additional_featured_image_post_types[<?php echo $key; ?>]" name="additional_featured_image_post_types[<?php echo $key; ?>]" type="checkbox" value="yes" <?php echo isset($additional_featured_image_post_types[$key]) && $additional_featured_image_post_types[$key] === 'yes' ? 'checked' : ''; ?>>
            <?php endforeach; ?>
        </div>
        <hr>

        <h2>Admin Styles</h2>
        <div class="input-wrap">
            <label class="checkbox" for="additional_featured_image_stylesheets">Load stylesheets</label>
            <input class="checkbox" id="additional_featured_image_stylesheets" name="additional_featured_image_stylesheets" type="checkbox" value="yes" <?php echo isset($additional_featured_image_stylesheets) && $additional_featured_image_stylesheets === 'yes' ? 'checked' : ''; ?>>
        </div>
        <hr>

        <h2>Slideshow Settings<em style="margin-left:15px; color:red;">Pro Only</em></h2>
        
        <div class="input-wrap">
            <label for="additional_featured_image_slideshow_speed">Slideshow Speed (millaseconds)</label>
            <input name="additional_featured_image_slideshow_speed" type="text" value="" disabled>
        </div>
        <div class="input-wrap">
            <label for="additional_featured_image_slideshow_show">Slides to Show</label>
            <input name="additional_featured_image_slideshow_show" type="text" value="" disabled>
        </div>
        <div class="input-wrap">
            <label for="additional_featured_image_slideshow_scroll">Slides to Scroll</label>
            <input name="additional_featured_image_slideshow_scroll" type="text" value="" disabled>
        </div>
        <div class="input-wrap">
            <input class="checkbox" id="additional_featured_image_slideshow_autoplay" name="additional_featured_image_slideshow_autoplay" type="checkbox" value="yes" disabled>
            <label class="checkbox" for="additional_featured_image_slideshow_autoplay">Autoplay</label>
        </div>
        <div class="input-wrap">
            <input class="checkbox" id="additional_featured_image_slideshow_arrows" name="additional_featured_image_slideshow_arrows" type="checkbox" value="yes" disabled>
            <label class="checkbox" for="additional_featured_image_slideshow_arrows">Arrows</label>
        </div>
        <div class="input-wrap">
            <input class="checkbox" id="additional_featured_image_slideshow_dots" name="additional_featured_image_slideshow_dots" type="checkbox" value="yes" disabled>
            <label class="checkbox" for="additional_featured_image_slideshow_dots">Dots</label>
        </div>
        <hr>
        <?php submit_button(); ?>
        <h2>Available Shortcodes</h2>
        <div class="input-wrap">
            <div class="shortcode-item">
                <label>Display Image Gallery: </label>
                <span>[featured_image_gallery]</span>
            </div>
            <div class="shortcode-item">
                <label>Display Single Image: </label>
                <span>[API_single_image id="0" class=""]</span><em style="margin-left:15px; color:red;">Pro Only</em>
            </div>
        </div>
        <hr>
       
        <h2>Media Uploader Anywhere (Backend Developers Only)</h2>
        <p>This can be used in any admin section or post type.  Custom post types are Pro only</p>
        <div class="input-wrap">
            <div class="shortcode-item">
                <label>Use the function: </label>
                <span>AFI_media_uploader($args = array())</span>
            </div>
            <div class="shortcode-item">
                <label>Arguments Array:</label>
                <div><strong style="min-width:200px; display:inline-block;">type:</strong> "option" or "post_meta" - <em>required</em></div>
                <div><strong style="min-width:200px; display:inline-block;">name:</strong> The prepended name for the option or post meta field - <em>required</em></div>
                <div><strong style="min-width:200px; display:inline-block;">button_value:</strong> The button text.</div>
            </div>
        </div>
        <hr>

        
        </form>
        <div style="float:left; margin-left:60px">
        <h2>Get more with <a target="_blank" href="https://metawebdevelopment.com/product/additional-featured-images-and-media-upload-anywhere/">Additonal Featured Images PRO</a></h2>
        <ul>
            <li><span class="dashicons dashicons-yes-alt"></span> Add addtional images to Custom Post Types with custom post type support</li>
            <li><span class="dashicons dashicons-yes-alt"></span> Display single images with the [AFI_single_image] shortcode</li>
            <li><span class="dashicons dashicons-yes-alt"></span> Reorder Gallery Images by simply drag and dropping</li>
            <li><span class="dashicons dashicons-yes-alt"></span> Slideshow customization settings</li>
        </ul>
        <p style="margin-top:15px;">
            <a style="margin-right: 5px; font-size:22px" class="button button-primary" target="_blank" href="https://metawebdevelopment.com/product/additional-featured-images-and-media-upload-anywhere/">Buy Now!</a> for only <span style="font-size: 36px; font-weight:900">$18</span>
        </p>
        </div>
        <?php 
    }

    public function pluginSettingsOptions(){
        add_menu_page(__('Additional Featured Images', 'Additional_Featured_Images'), __('AFI', 'Additional_Featured_Images'), 'manage_options', 'additional_featured_images_options',  [$this, 'pluginSettings'], WP_PLUGIN_URL . '/additional-featured-images/images/metaweb-logo.png', 10);
    }
}

new Additonal_Featured_Images_Settings();