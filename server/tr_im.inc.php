<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 tr.im 服务 http://tr.im/website/api
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 */

class tr_im extends short_url {
    protected $api = 'http://api.tr.im/api/trim_url.json?url=%s';

    public function short($url) {
        $result = json_decode($this->_get(sprintf($this->api, urlencode($url))), true);
        return $result['url'] ? $result['url'] : '';
    }
}
