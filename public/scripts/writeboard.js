/**
 * @version    $Id$
 */
var Writeboard = {
    id: 0,

    load: function() {
        $.ajax({
            url: '/writeboard/ajax-get/',
            data: {
                id: Writeboard.id
            },
            success: function(response) {
                if (response.success) {
                    $('.messages').text('').children().remove();
                    for (message in response.messages) {
                        $('.messages').append(Writeboard.getMessage(response.messages[message]));
                    }
                }
            }
        });
    },

    deleteHandler: function() {
        if (confirm('Вы действительно хотите удалить это сообщение?')) {
            $(this).children('img').each(function() {
                this.src = '/images/ajax-loader.gif';
            });
            $.ajax({
                url: '/writeboard/ajax-delete/',
                data: {
                    id: Writeboard.id,
                    messageid: this.id.replace('delete-', '')
                },
                success: function(scope) {
                    return function(response) {
                        if (!response.success) {
                            if (response.message) {
                                alert('Ошибка: ' + response.message);
                            } else {
                                alert('Ошибка удаления');
                            }
                        } else {
                            $(scope).parents('.message').remove();
                        }
                    };
                }(this),
                complete: function(scope) {
                    return function() {
                        $(scope).children('img').each(function() {
                            this.src = '/images/close.png';
                        });
                    };
                }(this)
            });
        }
        return false;
    },

    showTextareaHandler: function () {
        $(this).hide();
        $('#writeboard-input').show("fold");
        return false;
    },

    textareaKeyupHandler: function () {
        var val = $('#writeboard-message').val();
        var maxLength = 1000 - val.length;
        if (maxLength <= 0) {
            $('#writeboard-message').val(val.substr(0, 1000));
            maxLength = 0;
        }
        $('#writeboard-length').text(maxLength);
    },

    addHandler: function() {
        this.disabled = true;
        $.ajax({
            url: '/writeboard/ajax-add/',
            data: {
                id: Writeboard.id,
                message: $('#writeboard-message').val()
            },
            success: function(response) {
                if (response.success) {
                    $('.messages').prepend(Writeboard.getMessage(response.message));
                    $('#writeboard-message').val('');
                    $('#writeboard-length').text(1000);
                }
            },
            complete: function(scope) {
                return function() {
                    scope.disabled = false;
                };
            }(this)
        });
    },

    getMessage: function(options) {
        var result='<div class="message">'
            + '<div class="message-date">' + options.date + '</div>'
            + '<div class="message-user">';
        if (options.deleteAllowed) {
            result += '<a id="delete-' + options.id.toString() + '" href="#" class="delete"><img src="/images/close.png" /></a>';
        }
        result += '<a href="/user/' + options.login + '">' + options.login + '</a>'
            + '</div>'
            + '<div class="message-content">'
            + options.message
            + '</div>'
            + '</div>';
        return result;
    }
};