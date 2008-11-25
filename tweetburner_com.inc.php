<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * 压缩 URL 长度
 *
 * 使用 http://tweetburner.com/ 的服务。API 参见：http://tweetburner.com/api
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 * @link   http://www.gracecode.com/
 * @date   2008-08-04
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


class tweetburner_com
{
    protected $_error;
    protected $_handle;
    protected $_timeout;
    protected $_api = 'http://tweetburner.com/links';


    function __construct()
    {
        $this->_handle = curl_init();
        curl_setopt_array($this->_handle, array(
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true
        ));
        return true;
    }


    public function short($url)
    {
        curl_setopt_array($this->_handle, array(
            CURLOPT_URL => $this->_api,
            CURLOPT_POSTFIELDS => 'link[url]='. urlencode($url)
        ));

        $result = curl_exec($this->_handle);

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
