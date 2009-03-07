/**
 * @version    $Id:$
 */

$(function() {
    var updateRadioVisibility = function() {
        var oldRevision = Number($('.wiki-history-revision-old-radio:checked').get(0).value);
        var newRevision = Number($('.wiki-history-revision-new-radio:checked').get(0).value);
        $('.wiki-history-revision-new-radio').each(function() {
            var el = $(this);
            el.css('visibility', el.val() > oldRevision ? 'visible' : 'hidden');
        });
        $('.wiki-history-revision-old-radio').each(function() {
            var el = $(this);
            el.css('visibility', el.val() < newRevision ? 'visible' : 'hidden');
        });
    };

    $('.wiki-history-revision-new-radio')
        .click(updateRadioVisibility)
        .get(0).checked = true;
    $('.wiki-history-revision-old-radio')
        .click(updateRadioVisibility)
        .get(1).checked = true;

    $('.wiki-compare-button').click(function() {
        var oldRevision = $('.wiki-history-revision-old-radio:checked').get(0).value;
        var newRevision = $('.wiki-history-revision-new-radio:checked').get(0).value;
        var path = document.location.pathname;
        if (!/\/$/.test(path)) {
            path += '/';
        }
        document.location.href = path + newRevision + '/' + oldRevision;
    });
    updateRadioVisibility();
});
