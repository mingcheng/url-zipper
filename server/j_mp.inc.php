<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 Bit.ly 网址压缩服务
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 * @link   http://www.gracecode.com/
 */

class j_mp extends short_url {
    protected $api   = 'http://api.j.mp/shorten?version=2.0.1&longUrl=%s&login=%s&apiKey=%s';
    protected $login = 'feelinglucky';
    protected $key   = 'R_bed493816e5673a217d5b73f93ae5ef5';

    public function short($url) {
        $result = json_decode($this->_get(sprintf($this->api, urlencode($url), $this->login, $this->key)), true);
        if (!$result['errorCode']) {
            return $result['results'][$url]['shortUrl'];
        } else {
            $this->_error = $result['statusCode'];
            return '';
        }
    }
}
