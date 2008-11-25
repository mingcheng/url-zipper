<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * 压缩 URL 长度
 *
 * 使用 http://is.gd 服务。API 参见：http://is.gd/api_info.php
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 * @link   http://www.gracecode.com/
 * @date   2008-08-01
 */

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


class is_gd
{
    protected $_error;
    protected $_handle;
    protected $_allow_url_fopen = false;
    protected $_load_curl = false;
    protected $_api = 'http://is.gd/api.php';


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

        return true;
    }


    public function short($url)
    {
        $request_url = $this->_api . '?longurl=' . $url;

        if ($this->_allow_url_fopen) {
            try {
                $result = @file_get_contents($request_url);
            } catch (Exception $e) {
                $this->_error = $e->getMessage();
                return false;
            }
        } elseif($this->_load_curl) {
            curl_setopt_array($this->_handle, array(
                CURLOPT_URL     => $request_url
            ));

            $result = curl_exec($this->_handle);

            if (!$result || preg_match('/^(Error: )(.*)/', $result, $match)) {
                $this->_error = isset($match[2]) ? $match[2] : '';
                return false;
            }
        } else {
            $this->_error = '';
            return null;
        }

        if (preg_match('/^http:\/\/./i', $result)) {
            return trim($result);
        } else {
            return '';
        }
    }


    public function getMessage()
    {
        return $this->_error;
    }
}
?>
