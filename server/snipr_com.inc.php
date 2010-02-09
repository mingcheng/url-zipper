<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
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
    protected $api       = 'http://snipr.com/site/getsnip';
    protected $snipapi   = 'ab52da1ce580bbe3e241c079125a12f1';
    protected $snipuser  = 'feelinglucky';

    public function short($sniplink) {
        $params = sprintf('sniplink=%s&snipuser=%s&snipapi=%s&snipformat=simple',
            urlencode($sniplink), urlencode($this->snipuser), urlencode($this->snipapi));
        $result = $this->_post($this->api, $params);
        return empty($result) ? '' : $result;
    }
}
