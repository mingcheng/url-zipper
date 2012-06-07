<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * 使用 http://is.gd 服务。API 参见：http://is.gd/api_info.php
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 * @link   http://www.gracecode.com/
 * @date   2008-08-01
 */

class is_gd extends short_url {
    protected $api = 'http://is.gd/api.php?longurl=%s';

    public function short($url) {
        return $this->_get(sprintf($this->api, urlencode($url)));
    }
}
