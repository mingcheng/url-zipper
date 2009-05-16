<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 kl.am 服务 http://kl.am/api
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 */

class kl_am extends short_url {
    protected $api = 'http://kl.am/api/shorten/?url=%s&format=text';

    public function short($url) {
        return $this->_get(sprintf($this->api, urlencode($url)));
    }
}
