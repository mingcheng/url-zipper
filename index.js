jQuery(function($) {
    // 插入对应的节点
    var loading = $(document.createElement('span')).attr('id', 'loading').css('visibility', 'hidden'), timer;
    var error = $(document.createElement('div')).attr('class', 'msg').attr('id', 'error').html('<p class="error">.</p>').css('visibility', 'hidden');
    $('#submit').after(error).after(loading);

    // 表单提交事件
    $('#form').submit(function(e) {
        if (!$('#form textarea').val().match(/^http:\/\//i)) {
            $('#form textarea').val('http://' + $('#form textarea').val());
        }

        var val = $('#form textarea').val();
        if (!val.replace(/(^\s*)|(\s*$)/g, "").length) {
            $('#form textarea').val('').focus();
            return false;
        }

        if (!val.match(/^http:\/\/[\w|\d]+\.[\w|\d]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/i)) {
            $('#form textarea').focus();
            return false;
        }

        $.ajax({
            timeout: 15000, // 超时 15 秒
            url: $('#form').attr('action') + '?url=' + encodeURIComponent(val) + '&api=1', type: 'get',
            beforeSend: function() {
                if (timer) clearTimeout(timer);
                $('#submit').attr('disabled', 'disabled');
                $('#form textarea').attr('disabled', 'disabled');
                $(loading).css('visibility', '').removeAttr('class');
                $('#result').html('');
                $('#error').css('visibility', 'hidden');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#error p').html(textStatus);
                $('#error').css('visibility', '');
                $(loading).css('visibility', 'hidden').removeAttr('class');
                $('#submit').removeAttr('disabled');
                $('#form textarea').removeAttr('disabled').focus();
            },
            success: function(data) {
                $(loading).attr('class', 'finished');
                var data = eval('(' + data + ')');
                var html = [];
                for(var i in data) {
                    html.push('<li><input type="text" readonly="readonly" class="result '+ i +'" value="' + data[i] + '" /></li>');
                }
                $('#result').html(html.join(''));

                timer = setTimeout(function() {
                    $(loading).attr('class', 'rest');
                }, 5000);

                $('#error').css('visibility', 'hidden');
                $('#submit').removeAttr('disabled');
                $('#form textarea').removeAttr('disabled');
            }
        });
    
        return false;
    });

    $('#result').click(function(e) {
        var target = e.target;
        if ('input' == target.nodeName.toLowerCase()) {
            $(target).select();
        }
        return false;
    });

    // 预加载图片
    $(["images/loading.gif", "images/finish.gif", "images/rest.gif"]).each(function(i, s) {
        var image = $(new Image());
            image.attr('src', s).css('display', 'none');
            $(document.body).append(image);
    });
});
