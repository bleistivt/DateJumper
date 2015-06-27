jQuery.fn.dateSpacerClick = function (selector, duration) {
    'use strict';

    return this.on('click', function () {
        var index = jQuery(selector).index(this) + 1,
            target = jQuery(selector).get(index) || selector;
        jQuery('html, body').animate({
            scrollTop: $(target).offset().top
        }, duration);
    });
};

jQuery(function ($) {
    'use strict';

    $('.DiscussionDateSpacer').dateSpacerClick('.DiscussionDateSpacer', 1000);
    $('.CommentDateSpacer').dateSpacerClick('.CommentDateSpacer', 2000);
});
