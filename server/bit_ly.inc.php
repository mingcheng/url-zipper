<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 Bit.ly 网址压缩服务
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 * @link   http://www.gracecode.com/
 */

class bit_ly extends short_url {
    protected $api = 'http://api.bit.ly/shorten?version=2.0.1&longUrl=%s&login=%s&apiKey=%s';
    protected $login = 'feelinglucky';
    protected $key = 'R_b93bfd6e643469fa623cd9c7f92bb9c2';

    public function short($url) {
        try {
            $request = sprintf($this->api, urlencode($url), $this->login, $this->key);
            $result = json_decode($this->_get($request), true);
            if (!$result['errorCode']) {
                return $result['results'][$url]['shortUrl'];
            } else {
                $this->_error = $result['statusCode'];
                return '';
            }
        } catch (Exception $e) {
            return '';
        }
    }
}
