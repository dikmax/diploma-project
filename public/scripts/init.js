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

    $('.title-mark a').click(function() {
        var link = $(this);
        if (link.hasClass('selected')) {
            return false;
        }
        var mark = undefined;
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
        }
        if (mark === undefined) {
            return false;
        }
        var titleId = link.parents('.title-mark').attr('id').replace('title-mark-', '');
        $.ajax({
            url: '/ajax/set-mark',
            data: {
                title_id: titleId,
                mark: mark
            },
            success: function (response) {
                if (response.success) {
                    link.siblings().removeClass('selected');
                    link.addClass('selected');
                }
            }
        });

        return false;
    });
});