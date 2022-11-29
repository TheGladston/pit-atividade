jQuery(document).ready(function ($) {
// hides as soon as the DOM is ready
    $('div.section_body').hide();
    // shows on clicking the noted link
    $('h3').click(function () {
        $(this).toggleClass("open");
        $(this).next("div").slideToggle('1000');
        return false;
    });
});

jQuery(function ($) {
    // Add Color Picker to all inputs that have 'color-field' class
    $(function () {
        $('.color-field').wpColorPicker();
    });

    // Set all variables to be used in scope
    var frame,
            metaBox = $('#meta-box-id.postbox'), // Your meta box id here
            addImgLink = metaBox.find('.upload-custom-img'),
            delImgLink = metaBox.find('.delete-custom-img'),
            imgContainer = metaBox.find('.custom-img-container'),
            imgIdInput = metaBox.find('.custom-img-id');
    // ADD IMAGE LINK
    $('.upload_logo_header').on('click', function (event) {

        event.preventDefault();
        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });
        // When an image is selected in the media frame...
        frame.on('select', function () {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();
            // Send the attachment URL to our custom image input field.
            imgContainer.append('<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>');
            // Send the attachment id to our hidden input
            imgIdInput.val(attachment.id);
            // Hide the add image link
            addImgLink.addClass('hidden');
            // Unhide the remove image link
            delImgLink.removeClass('hidden');
        });
        // Finally, open the modal on click
        frame.open();
    });
    // DELETE IMAGE LINK
    delImgLink.on('click', function (event) {

        event.preventDefault();
        // Clear out the preview image
        imgContainer.html('');
        // Un-hide the add image link
        addImgLink.removeClass('hidden');
        // Hide the delete image link
        delImgLink.addClass('hidden');
        // Delete the image id from the hidden input
        imgIdInput.val('');
    });
});
var jsonApi = {
    stripTags: function (string) {
        response = string.replace(/<\/?[^>]+>/gi, '');
        return response;
    },
    getMediaPost: function (id) {
        jQuery.getJSON("https://www.bertholdo.com.br/blog/wp-json/wp/v2/media/" + id + "", function (dados) {
            dados.forEach(function (media, i) {
                console.log(media);
            });
        });
    },
    getPosts: function (per_page) {
        jQuery.getJSON("https://www.bertholdo.com.br/blog/wp-json/wp/v2/posts/?per_page=" + per_page + "", function (dados) {
            dados.forEach(function (post, i) {
                console.log(post);
                html = '<a href="' + post.link + '" class="list-group-item clearfix" target="_blank">';
                html += '<div class="row">';
                html += '<div class="col-md-3"><img src="' + post._embedded['wp:featuredmedia']['0'].source_url + '" class="img-responsive" alt="' + post.title.rendered + '"/></div>';
                html += '<div class="col-md-9">';
                html += '<h4 class="list-group-item-heading">' + post.title.rendered + '</h4>';
                html += '<p class="list-group-item-text">' + json.stripTags(post.excerpt.rendered) + '</p>';
                html += '</div></div>';
                html += '</a>';
                $('#posts-blog-brt').append(html);
            });
        });
    },
}

//jQuery(document).ready(function ($) {
//    var url = 'https://www.bertholdo.com.br/blog/wp-json/wp/v2/posts?per_page=10&_embed';
//    var header = {
//        mode: "no-cors",
//        method: "GET",
//        headers: new Headers({'Accept': 'application/json', 'Content-Type': 'application/json', 'Access-Control-Allow-Origin': "*"}),
//    };
//    fetch(url, header).then(response => {
//        return response.json()
//    }).then(dados => {
//        console.log(response);
//
//        dados.forEach(function (post, i) {
//            html = '<a href="' + post.link + '" class="list-group-item clearfix" target="_blank">';
//            html += '<div class="row">';
//            html += '<div class="col-3"><img src="' + post._embedded['wp:featuredmedia']['0'].source_url + '" class="img-responsive" alt="' + post.title.rendered + '"/></div>';
//            html += '<div class="col-9">';
//            html += '<h4 class="list-group-item-heading">' + post.title.rendered + '</h4>';
//            html += '<p class="list-group-item-text">' + json.stripTags(post.excerpt.rendered) + '</p>';
//            html += '</div></div>';
//            html += '</a>';
//            $('#posts-blog-brt').append(html);
//        });
//    }
//    );
//}
//);