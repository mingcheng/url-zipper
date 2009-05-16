<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 Cligs 服务压缩 URL
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 */

class cli_gs extends short_url {
    protected $api = 'http://cli.gs/api/v1/cligs/create?url=%s&key=%s&appid=url_zipper';
    protected $key = 'c04977416d73e10d4e0610ffbd349d86';

    public function short($url) {
        return $this->_get(sprintf($this->api, urlencode($url), $this->key));
    }
}
