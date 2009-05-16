<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 * @link   http://www.gracecode.com/
 */

function __autoload($className) {
    $file = realpath('./server/') . DIRECTORY_SEPARATOR . $className . '.inc.php';
    if (file_exists($file)) include_once $file;
}

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


if (!function_exists('curl_setopt_array')) {
    function curl_setopt_array(&$ch, $curl_options)
    {
        foreach ($curl_options as $option => $value) {
            if (!curl_setopt($ch, $option, $value)) {
                return false;
            }
        }
        return true;
    }
}

abstract class short_url
{
    protected $_error;
    protected $_handle;
    protected $_allow_url_fopen = false;
    protected $_load_curl = false;
    protected $_api = '';

    function __construct($allow_url_fopen = false, $setTimeOut = false)
    {
        $this->_allow_url_fopen = $allow_url_fopen && ini_get('allow_url_fopen') ? true : false;
        $this->_load_curl = extension_loaded('curl') ? true : false;

        if ($this->_load_curl) {
            $this->_handle = curl_init();
            curl_setopt_array($this->_handle, array(
                CURLOPT_HTTPGET => true,
                CURLOPT_HEADER  => false,
                CURLOPT_TIMEOUT => $setTimeOut,
                CURLOPT_RETURNTRANSFER => true
            ));
        }

        if ($this->_allow_url_fopen && $setTimeOut) {
            ini_set('default_socket_timeout', intval($setTimeOut));
        }

        if (!$this->_allow_url_fopen || $this->_load_curl) {
            $this->error = 'No method for get data.';
            return false;
        }
    }

    protected function _get($request_url)
    {
        if ($this->_allow_url_fopen) {
            try {
                return @file_get_contents($request_url);
            } catch (Exception $e) {
                $this->_error = $e->getMessage();
                return '';
            }
        } elseif($this->_load_curl) {
            curl_setopt_array($this->_handle, array(
                CURLOPT_URL => $request_url
            ));

            return curl_exec($this->_handle);
        } else {
            $this->_error = '';
            return null;
        }
    }


    public function getMessage()
    {
        return $this->_error;
    }
}
