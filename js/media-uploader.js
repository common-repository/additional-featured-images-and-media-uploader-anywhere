jQuery(document).ready(function($){
    //media uploader
    $('.upload_image_button').on('click', function(e){
        let button = $(this);
        let ids = button.siblings('.additional_featured_images_ids').val();
        ids = ids !== '' ? JSON.parse(ids) : {};
        custom_uploader = wp.media({
            title: 'Insert Additional Images',
            library : {
                type : 'image'
            },
            button: {
                text: 'Insert Image(s)'
            },
            multiple: true
        }).on('select', function(){
            let attachment = custom_uploader.state().get('selection').map(function(attachment){
                attachment.toJSON();
                return attachment;
            });
            
            $(attachment).each(function(){
                let id = $(this).attr('id');
                let url = $(this)[0].attributes.url;
                let order = Number(Object.keys(ids).length) + 1;
                ids[id] = {};
                ids[id].order = order
                ids[id].url = url;
                button.parent().next('.image-flex-wrap').append(`<div style="position: relative;" class="image-flex">
                    <img src="${$(this)[0].attributes.url}">
                    <a data-id="${$(this)[0].attributes.id}" class="image_remove" href="#">remove</a>
                    <div style="position:absolute; color: #FFF; top: 1px; right: 5px; font-weight: bold; text-shadow: 0px 0px 3px #000">${$(this)[0].attributes.id}</div>
                </div>`);
            });
            button.siblings('.additional_featured_images_ids').val(JSON.stringify(ids));
           
        }).open();
    });

    //delete image
    $(document).on('click', '.image_remove', function(e){
        e.preventDefault();
        let _this = $(this);
        let input = _this.closest('.image-flex-wrap').prev('.input-wrap-gallery').children('.additional_featured_images_ids');
        let ids = JSON.parse(input.val());
        let id = _this.data('id');
        delete ids[id];
        if($.isEmptyObject(ids)){
            input.val('');
        } else {
            input.val(JSON.stringify(ids));
        }
        _this.parent().remove();
    });     
});
