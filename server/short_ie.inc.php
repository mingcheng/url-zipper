<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 Short.ie 服务 http://wiki.short.ie/index.php/Main_Page
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 */

class short_ie extends short_url {
    protected $api = 'http://short.ie/api?url=%s&format=json';

    public function short($url) {
        $result = json_decode($this->_get(sprintf($this->api, urlencode($url))), true);
        return $result['short']['shortened'] ? $result['short']['shortened'] : '';
    }
}
