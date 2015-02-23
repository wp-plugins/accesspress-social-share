jQuery(document).ready(function ($) {

    var shortcode_profile_array = [];
    $('.apss-count').each(function () {
        var social_detail = $(this).attr('data-social-detail');
        if ($.inArray(social_detail, shortcode_profile_array) == -1) {
            shortcode_profile_array.push(social_detail);
        }
    });
    if (shortcode_profile_array.length > 0)
            {
                $.ajax({
                    type: 'post',
                    url: frontend_ajax_object.ajax_url + '/?action=frontend_counter&_wpnonce=' + frontend_ajax_object.ajax_nonce,
                    data: {shortcode_data: shortcode_profile_array},
                    success: function (res) {
                        res = $.parseJSON(res);
                        for (var i=0;i<=shortcode_profile_array.length;i++) {
                            var social_detail = shortcode_profile_array[i];
                            var count = (res[i])?res[i]:0;
                            $('.apss-count[data-social-detail="' + social_detail + '"]').html(count);
                        }
                    }
                });
            }

});