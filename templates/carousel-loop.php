<div class="additional-featured-images">
    <?php if ($images) : ?>
        <?php foreach($images as $key => $image) : ?>
            <div class="carousel-item">
                <img alt="" data-srcset="<?php echo wp_get_attachment_image_srcset($key); ?>" class="attachment-full" src="<?php echo $image->url; ?>" srcset="<?php echo wp_get_attachment_image_srcset($key); ?>">
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>