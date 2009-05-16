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
 *
 * [+] 2008-08-05
 *     初始化版本
 */

require_once 'common.inc.php';

function __autoload($className) {
    $file = realpath('./server/') . DIRECTORY_SEPARATOR . $className . '.inc.php';
    if (file_exists($file)) include_once $file;
}

$server_list = array (
    'bit_ly', 'cli_gs', 'is_gd', 'kl_am', 
    'poprl', 'short_ie', 'snipr_com', 'tr_im', 
);
$url = urldecode(getRequest('url', null, 'get'));

if (!$url || !preg_match('/^http:\/\/[\w|\d]+\.[\w|\d]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/i', $url)) {
    if (getRequest('api', false, 'get')) {
        header('Content-type: text/javascript');
        die('{"error": "request empty"}');
    }
} else {
    $server_result = array();
    foreach($server_list as $server) {
        $server_api = new $server(true, 2000);
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Url Zipper - Gracecode.com</title>
        <meta name="keywords" content="URL 压缩,URL 简化" />
        <meta name="description" content="URL 压缩器，简短 URL 的长度" />
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://rss.gracecode.com" />
        <meta name="author" content="手气不错" />
        <link rel="icon" type="image/x-icon" href="http://www.gracecode.com/favicon.ico" />
        <link rel="stylesheet" href="http://assets.taobaocdn.com/tbsp/tbsp.css" type="text/css" media="screen" />
        <style type="text/css">
            h1 {font-size: 18px; font-weight: bold;}
            p {margin: 10px 0px;}
            body {
                background: url(http://www.gracecode.com/usr/themes/gracecode/images/bg_plant.jpg) no-repeat top right;
            }
            legend {display: none;}
            textarea {width: 800px; height: 100px; font-family: sans-serif; font-size: 16px;}
            textarea:focus, input:focus {background: #ffc;}
            form {font-size: 14px;}
            input[type=submit]{font-size: 16px;}

            input.result {
                width: 798px;
                font-size: 18px;
                height: 25px;
                line-height: 25px;
                padding: 2px;
                margin-bottom: 5px;
                border: 1px solid #999;
            }

            input.best {
                width: 794px;
                border: 3px solid #555;
            }

            input.isgd {
                background: url(logo_is_gd.png) no-repeat center right;
            }

            input.snipr {
                background: url(logo_snipr_com.png) no-repeat center right;
            }

            input.tweetburner {
                background: url(logo_tweetburner_com.png) no-repeat center right;
            }

            .error {
                border: 1px solid red;
                background: yellow;
                padding: 3px;
                margin: 0px 5px;
            }
            .error:hover {
                cursor: pointer;
            }

            p.addZipper {
                width: 200px;
                height: 45px;
                line-height: 45px;
                font-size: 24px;
                background: #ffc;
                border: 1px dotted #555;
                text-align: center;
                font-family:arial;
                font-weight:bold;
            }

            p.addZipper a:link, p.addZipper a:visited {
                display: block;
                width: 200px;
                height: 45px;
                text-decoration: none;
                color: black;
                border: 0px;
                margin: 0px;
                text-indent: 0px;
                padding: 0px;
                margin: 0px;
            }

            p.addZipper a:hover {
                border: 0px;
                color: none;
                background: none;
            }
        </style>
        <script type="text/javascript" src="http://assets.taobaocdn.com/js/tbra/yui-base.js"></script>
        <script type="text/javascript">
            var Event = YAHOO.util.Event;
            var Dom = YAHOO.util.Dom;

            Event.onDOMReady(function() {
                var input = Dom.getElementsByClassName('result', 'input', 'ark:result');
                Event.on(input, 'click', function (e) {
                    this.select();
                    Event.stopEvent(e);
                });

                var callback = (function () {
                    return {
                        success: function (req) {
                            try {
                                var json = eval('(' + req.responseText + ')');
                                if (json) {
                                    this.show(json);
                                }
                                Dom.setStyle(Dom.get('ark:result'), 'display', '');
                                Dom.setStyle(Dom.get('error'),  'display', 'none');
                            } catch(e) {
                                this.error(e);
                            }
                        },

                        failure: function (req) {
                            this.error('获取数据错误');
                        },

                        show: function (data) {
                            if (data.error) {
                                this.error(data.error);
                                return;
                            }
                            this._set('isgd', data.is_gd || null);
                            this._set('snipr', data.snipr_com || null);
                            this._set('tweetburner', data.tweetburner_com || null);
                        },

                        _set: function(el, data) {
                            el = Dom.get(el);
                            if (el && data && data.match(/^http:\/\/./i)) {
                                el.value = data;
                                Dom.setStyle(el, 'display', '');
                            } else {
                                Dom.setStyle(el, 'display', 'none');
                            }
                        },

                        error: function (message) {
                            var box = Dom.get('error');
                            if (!box) {
                                var box = document.createElement('span');
                                box.id = 'error';
                                Dom.addClass(box, 'error');
                                Event.on(box, 'click', function(e){
                                        Dom.setStyle(this, 'display', 'none');
                                });
                                Dom.insertAfter(box, 'submit');
                            }

                            box.title = message;
                            box.innerHTML = message;
                            Dom.setStyle(box, 'display', '');
                        },

                        cache: false
                    }
                })();

                Event.on('form', 'submit', function (e) {
                    var url = Dom.get('url');
                    if (!((url || 0).value || 0).length) {
                        callback.error('请您复制/粘贴 URL 至输入框');
                        url.focus();
                    } else if (!url.value.match(/^http:\/\/./i)) {
                        callback.error('请您输入正确的 URL 格式（http:// 开头）');
                        url.focus();
                    } else {
                        var action = this.action + '?url=' + url.value + '&api=1';
                        YAHOO.util.Connect.asyncRequest('GET', action, callback);
                    }

                    Event.stopEvent(e);
                });

                YAHOO.util.Connect.startEvent.subscribe(function () {
                    Dom.get('submit').disabled = 'disabled';
                });

                YAHOO.util.Connect.completeEvent.subscribe(function () {
                    Dom.get('submit').disabled = '';
                });

                <?php
                    if (!$url) {
                        echo "Dom.setStyle(Dom.get('ark:result'), 'display', 'none');";
                    }
                ?>
            });
        </script>
    </head>
    <body>
        <div id="page">
        <div id="cotnent">
            <h1>Url Zipper - Url 压缩器</h1>
            <fieldset>
                <legend>Url Zipper</legend>
                <form method="get" action="" id="form">
                    <p><textarea name="url" id="url" rows="10" cols="100"><?php echo $url ?></textarea></p>
                    <p><input type="submit" value="Zipper!" id="submit" /></p>
                    <p id="ark:result">
                        <?php
                            if (!empty($server_result)) {
                                foreach($server_result as $type => $result) {
                                    printf('<input type="text" readonly="readonly" class="result %s" value="%s" />'."\n", $type, $result);
                                }
                            }
                        ?>
                    </p>
                </form>
            </fieldset>
            <h1>Url Zipper</h1>
              <p>如果您觉得这个工具好用，请将下面的按钮拖动到您的书签工具栏中。
                这样您以后就可以点击此按钮，自动会将您当前页面的 URL 压缩。</p>
              <p><img src="addZipper.png" alt="addZipper.png" title="将左边的按钮拖放到您的书签工具栏中"
                 style="float: right; margin: 0px 25px;border: 1px solid #ccc;" /></p>
              <p class="addZipper"><a 
                    href="javascript:void(location.href='http://lab.gracecode.com/url_zipper/?url='+encodeURIComponent(location.href));">Zipper!</a></p>
              <p><em>如您有任何的建议或者意见，请您登录
                    <a href="http://www.gracecode.com">Gracecode.com</a> 联系我，谢谢。</em></p>
            </div>
        </div>
        </div>
    </body>
</html>
