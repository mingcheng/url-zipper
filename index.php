<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * Url Zipper
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 *   @link http://www.gracecode.com/
 *   @date 2008-08-05
 */

/**
 * 安全获取 GET/POST 的参数
 *
 * @param  String $request_name
 * @param  Mixed  $default_value
 * @param  String $method 'post', 'get', 'all' default is 'all'
 * @return String
 */
function getRequest($request_name, $default_value = null, $method = "all")
{
    $magic_quotes = ini_get("magic_quotes_gpc") ? true : false;
    $method = strtolower($method);

    switch (strtolower($method)) {
    default:
    case "all":
        if (isset($_POST[$request_name])) {
            return $magic_quotes ? stripslashes($_POST[$request_name]) : $_POST[$request_name];
        } else if (isset($_GET[$request_name])) {
            return $magic_quotes ? stripslashes($_GET[$request_name]) : $_GET[$request_name];
        } else {
            return $default_value;
        }
        break;

    case "get":
        if (isset($_GET[$request_name])) {
            return $magic_quotes ? stripslashes($_GET[$request_name]) : $_GET[$request_name];
        } else {
            return $default_value;
        }
        break;

    case "post":
        if (isset($_POST[$request_name])) {
            return $magic_quotes ? stripslashes($_POST[$request_name]) : $_POST[$request_name];
        } else {
            return $default_value;
        }
        break;

    default:
        return $default_value;
        break;
    }
}

$url = getRequest('url', null, 'get');
if (!$url || !preg_match('/^http:\/\/./i', $url)) {
    if (getRequest('api', false, 'get')) {
        header('Content-type: text/javascript');
        die('{"error": "request empty"}');
    }
} else {
    require_once 'is_gd.inc.php';
    require_once 'snipr_com.inc.php';
    require_once 'tweetburner_com.inc.php';

    $is_gd = new is_gd;
    $snipr_com = new snipr_com;
    $tweetburner_com = new tweetburner_com;

    $result = array();
    if ($is_gd_result = $is_gd->short($url)) {
        $result['is_gd'] = $is_gd_result;
    }
    if ($snipr_com_result = $snipr_com->short($url)) {
        $result['snipr_com'] = $snipr_com_result;
    }
    if ($tweetburner_com_result = $tweetburner_com->short($url)) {
        $result['tweetburner_com'] = $tweetburner_com_result;
    }

    if (getRequest('api', false, 'get')) {
        header('Content-type: text/javascript');
        die(json_encode($result));
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
        <link rel="stylesheet" href="http://lab.gracecode.com/style-min.css" type="text/css" media="screen" />
        <style type="text/css">
            h1 {font-size: 18px; font-weight: bold;}
            p {margin: 10px 0px;}
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
        <div class="wrapper">
            <h1>Url Zipper - Url 压缩器</h1>
            <fieldset>
                <legend>Url Zipper</legend>
                <form method="get" action="" id="form">
                    <p><textarea name="url" id="url" rows="10" cols="100"><?php echo $url ?></textarea></p>
                    <p><input type="submit" value="Zipper!" id="submit" /></p>
                    <p id="ark:result">
                    <input type="text" id="isgd" 
                        <?php 
                            if ($url && !$result['is_gd']) {
                                echo 'style="display: none;"';
                            }
                        ?>
                        readonly="readonly" class="result isgd" value="<?php echo $result['is_gd']; ?>" /> 
                    <input type="text" id="snipr"
                        <?php 
                            if ($url && !$result['snipr_com']) {
                                echo 'style="display: none;"';
                            }
                        ?>
                        readonly="readonly" class="result snipr" value="<?php echo $result['snipr_com']; ?>" />
                    <input type="text" id="tweetburner" 
                        <?php 
                            if ($url && !$result['tweetburner_com']) {
                                echo 'style="display: none;"';
                            }
                        ?>
                        readonly="readonly" class="result tweetburner" value="<?php echo $result['tweetburner_com']; ?>" />
                    </p>
                </form>
            </fieldset>

            <div class="container">
            <h1>Url Zipper</h1>
              <p>如果您觉得这个工具好用，请将下面的按钮拖动到您的书签工具栏中。
                这样您以后就可以点击此按钮，自动会将您当前页面的 URL 压缩。</p>
              <p><img src="addZipper.png" alt="addZipper.png" title="将左边的按钮拖放到您的书签工具栏中"
                 style="float: right; margin: 0px 25px;border: 1px solid #ccc;" /></p>
              <p class="addZipper"><a 
                    href="javascript:void(location.href='http://lab.gracecode.com/zip_url/?url='+location.href);">Zipper!</a></p>
              <p><em>如您有任何的建议或者意见，请您登录
                    <a href="http://www.gracecode.com">Gracecode.com</a> 联系我，谢谢。</em></p>
            </div>
        </div>
    </body>
</html>
