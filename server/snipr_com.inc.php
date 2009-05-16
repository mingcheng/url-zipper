<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * 压缩 URL 长度
 *
 * 使用 http://snipr.com 服务。API 参见：http://snipr.com/site/help?go=api
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 * @link   http://www.gracecode.com/
 * @date   2008-08-04
 */

class snipr_com extends short_url {
    protected $api = 'http://snipr.com/site/snip?r=simple&link=%s';

    public function short($url) {
        $result = $this->_get(sprintf($this->api, urlencode($url)));
        return empty($result) ? '' : $result;
    }
}
