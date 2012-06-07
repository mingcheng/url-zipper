<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 t.cn 服务压缩 URL
 *
 * @author mingcheng<lucky@gracecode.com>
 * @date   2012-06-07
 */

class t_cn extends short_url {
    protected $api = 'http://api.t.sina.com.cn/short_url/shorten.json?source=%s&url_long=%s';
    protected $key = '1308651834';

    public function short($url) {
        $response = $this->_get(sprintf($this->api, $this->key, urlencode($url)));
        $json = json_decode($response);
        return $json[0]->url_short;
    }
}
