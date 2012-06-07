<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 POPrl 服务 http://poprl.com/api_ref
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-05-16
 */

class poprl extends short_url {
    protected $api = 'http://poprl.com/api/post';

    public function short($url) {
        $this->_allow_url_fopen = false;
        if ($this->_load_curl) {
            $this->_handle = curl_init();
            curl_setopt_array($this->_handle, array(
                CURLOPT_HTTPGET => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => sprintf('out=%s&url=%s', 'json', urlencode($url)),
                CURLOPT_RETURNTRANSFER => true
            ));

            $result = json_decode($this->_get($this->api), true);
            if ($result['code']) {
                $this->_error = $result['message'];
                return '';
            }
            return $result['data']['url'] ? $result['data']['url'] : '';
        } else {
            return '';
        }
    }
}
