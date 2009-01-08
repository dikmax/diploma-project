/**
 * Initializes all common components
 *
 * @version    $Id$
 */

$(function() {
    $.ajaxSetup({
        type: "post",
        dataType: "json"
    });

    // Show remove mark links
    $('.title-mark').each(function() {
        if ($(this).children('.selected').length > 0) {
            $(this).children('.markremove').show();
        }
    });

    // Set/remove mark click
    $('.title-mark a').click(function() {
        var link = $(this);
        if (link.hasClass('selected')) {
            return false;
        }
        var mark = undefined;
        var url = '/ajax/set-mark';
        if (link.hasClass('mark2')) {
            mark = 2;
        } else if (link.hasClass('mark1')) {
            mark = 1;
        } else if (link.hasClass('mark0')) {
            mark = 0;
        } else if (link.hasClass('mark-1')) {
            mark = -1;
        } else if (link.hasClass('mark-2')) {
            mark = -2;
        } else if (link.hasClass('markremove')) {
            mark = -100;
            url = '/ajax/remove-mark';
        } else {
            return false;
        }
        var titleId = link.parents('.title-mark').attr('id').replace('title-mark-', '');
        var data = mark === -100
            ? {title_id: titleId}
            : {
                title_id: titleId,
                mark: mark
            };
        $.ajax({
            url: url,
            data: data,
            success: function (response) {
                if (response.success) {
                    link.siblings().removeClass('selected');
                    if (mark !== -100) {
                        link.addClass('selected');
                        link.siblings('.markremove').show();
                    } else {
                        link.hide();
                    }
                }
            }
        });

        return false;
    });
});