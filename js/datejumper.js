jQuery(document).ready(function ($) {

    $('.DiscussionDateSpacer').on('click', function (event) {
        var index = $(".DiscussionDateSpacer").index(this);
        index++;
        var target = $(".DiscussionDateSpacer").get(index);
        if (typeof target == 'undefined') {
            target = ".DiscussionDateSpacer";
        }
        $('html, body').animate({
            scrollTop: $(target).offset().top
        }, 1000);
    });

    $('.CommentDateSpacer').on('click', function (event) {
        var index = $(".CommentDateSpacer").index(this);
        index++;
        var target = $(".CommentDateSpacer").get(index);
        if (typeof target == 'undefined') {
            target = ".CommentDateSpacer";
        }
        $('html, body').animate({
            scrollTop: $(target).offset().top
        }, 2000);
    });

});
