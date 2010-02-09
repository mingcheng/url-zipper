<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * Url Zipper 
 *
 * 聚合各种URL 缩短工具
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 * @link http://www.gracecode.com/
 *
 * @change
 *     [+]new feature  [*]improvement  [!]change  [x]bug fix
 *
 * [+] 2010-02-00
 *     增加 goo.gl 、j.mp 短域名服务
 *
 * [+] 2009-05-16
 *     改写程序结构、优化代码并加入更多的服务商 API
 *
 * [+] 2008-08-05
 *     初始化版本
 */

require_once 'common.inc.php';

// 需要请求的服务列表
$server_list = array ('bit_ly', /* 'cli_gs', */ 'is_gd', 'goo_gl', 'j_mp', 'kl_am', /* 'poprl', 'short_ie', */ 'snipr_com', 'tr_im');
$url = urldecode(getRequest('url', '', 'get'));
if (empty($url) || !preg_match('/^http:\/\/[\w|\d]+\.[\w|\d]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/i', $url)) {
    if (getRequest('api', false, 'get')) {
        header('Content-type: text/javascript');
        die('{"error": "request empty"}');
    }
} else {
    $server_result = array();
    foreach($server_list as $server) {
        $server_api = new $server(true, 2000); // 超时时间
        $result = $server_api->short($url);
        if (!empty($result)) $server_result[$server] = trim($result);
    }

    if (getRequest('api', false, 'get')) {
        header('Content-type: text/javascript');
        $callback = getRequest('callback', '', 'get');
        $var = getRequest('var', '', 'get');
        if (preg_match('/^\w+$/i', $callback)) {
            die($callback . '(' . json_encode($server_result) . ');');
        } elseif (preg_match('/^\w+$/i', $var)) {
            die($var . ' = ' . json_encode($server_result) . ';');
        } else {
            die(json_encode($server_result));
        }
    }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Url Zipper - 批量网址压缩工具 - Gracecode.com</title>
        <meta name="keywords" content="URL 压缩,URL 简化" />
        <meta name="description" content="URL 压缩器，简短 URL 的长度" />
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://feed.gracecode.com/gracecode/" />
        <meta name="author" content="手气不错" />
        <link rel="icon" type="image/x-icon" href="http://www.gracecode.com/favicon.ico" />
        <link rel="stylesheet" href="http://assets.taobaocdn.com/tbsp/tbsp.css" type="text/css" media="screen" />
        <style><?php include 'style-min.css'; ?></style>
    </head>
    <body>
        <div id="page">
            <div id="cotnent">
                <h1>Url Zipper - 批量网址压缩工具</h1>
                <fieldset>
                    <legend>Url Zipper</legend>
                    <form method="get" action="/url_zipper/" id="form">
                    <p><textarea name="url" id="url" rows="10" cols="100"><?php echo htmlentities($url);?></textarea></p>
                    <div><input type="submit" value="Zipper!" id="submit" /></div>
                    </form>
                </fieldset>
                <ul id="result">
                    <?php 
                        if (isset($server_result) && !empty($server_result)) {
                            foreach ($server_result as $name => $value) {
                                printf('<li><input type="text" readonly="readonly" class="result %s" value="%s" /></li>', $name, $value);
                            }
                        }
                    ?>
                </ul>
                <h1>快捷方式</h1>
                <div class="shortcut">
                    <p style="width:450px;">如果您觉得这个工具好用，请将下面的按钮拖动到您的书签工具栏中。
                    这样您以后就可以点击此按钮，自动压缩将您当前页面的 URL 地址。</p>
                    <p class="preview"><img src="addZipper.png" alt="addZipper.png" title="将左边的按钮拖放到您的书签工具栏中" /></p>
                    <p class="addZipper"><a 
                        href="javascript:void(location.href='http://lab.gracecode.com/url_zipper/?url='+encodeURIComponent(location.href));">Zipper!</a></p>
                    <h3>目前支持的服务接口</h3>
                    <p id="support">
                        <a href="http://bit.ly/" rel="nofollow"><img src="images/logo/bit_ly.png" /></a>
                        <a href="http://j.mp/" rel="nofollow"><img src="images/logo/j_mp.png" /></a>
                        <a href="http://kl.am/" rel="nofollow"><img src="images/logo/kl_am.png" /></a>
                        <a href="http://goo.gl/" rel="nofollow"><img src="images/logo/goo_gl.png" /></a>
                        <a href="http://snipr.com/" rel="nofollow"><img src="images/logo/snipr_com.png" /></a>
                        <a href="http://is.gd/" rel="nofollow"><img src="images/logo/is_gd.png" /></a>
<?php 
/*
                        <a href="http://short.ie/" rel="nofollow"><img src="images/logo/short_ie.png" /></a>
                        <a href="http://cli.gs/" rel="nofollow"><img src="images/logo/cli_gs.jpg" /></a>
                        <a href="http://poprl.com/" rel="nofollow"><img src="images/logo/poprl.jpg" /></a>
 */
?>
                        <a href="http://tr.im/" rel="nofollow"><img src="images/logo/tr_im.png" /></a>
                    </p>
                </div>
                <p class="links"><a href="#">API</a> / <a href="#">更改历史</a> / <a href="http://www.gracecode.com/">Gracecode.com</a></h1>
            </div>
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
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
                        },
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
            });
        </script>
        <div style="width:1px;hegith:1px;overflow:hidden;display:none;">
            <img src="images/loading.gif" /> <img src="images/finish.gif" /> <img src="images/rest.gif" />
        </div>
    </body>
</html>
