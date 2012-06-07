<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Short url from goo-gl
 *
 * @author mingcheng<i.feelinglucky#gmail.com>
 * @date   2010-02-09
 * @link   http://www.gracecode.com/
 *
 * Interface for creating/expanding goo.gl links
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @see    http://openpear.org/repository/Services_ShortURL_Googl/trunk/Services/ShortURL/Googl.php
 */

class goo_gl extends short_url {

    /**
     * API URL
     *
     * @var string $api The URL for the API
     * @access protected
     */
    protected $api = 'http://goo.gl/api/url';

    /**
     * The user name for API
     *
     * @var    string
     * @access protected
     */
    protected $user = 'toolbar@google.com';


    private function c() {
        $l = 0;
        foreach (func_get_args() as $val) {
            $val &= 4294967295;

            /**
             * 32bit signed
             * @see http://github.com/yappo/p5-WWW-Shorten-Google/
             */
            $val += $val > 2147483647 ? -4294967296 : ($val < -2147483647 ? 4294967296 : 0);
            $l   += $val;
            $l   += $l > 2147483647 ? -4294967296 : ($l < -2147483647 ? 4294967296 : 0);
        }
        return $l;
    }


    protected function d($l) {
        $l = $l > 0 ? $l : $l + 4294967296;
        $m = "$l";  // must to be string
        $o = 0;
        $n = false;
        for ($p = strlen($m) - 1; $p >= 0; --$p) {
            $q = $m[$p];
            if ($n) {
                $q *= 2;
                $o += floor($q / 10) + $q % 10;
            } else {
                $o += $q;
            }
            $n = !$n;
        }
        $m = $o % 10;
        $o = 0;
        if ($m != 0) {
            $o = 10 - $m;
            if (strlen($l) % 2 == 1) {
                if ($o % 2 == 1) {
                    $o += 9;
                }
                $o /= 2;
            }
        }
        return "$o$l";
    }


    protected function e($l) {
        $m = 5381;
        for ($o = 0; $o < strlen($l); $o++) {
            $m = $this->c($m << 5, $m, ord($l[$o]));
        }
        return $m;
    }


    protected function f($l) {
        $m = 0;
        for ($o = 0; $o < strlen($l); $o++) {
            $m = $this->c(ord($l[$o]), $m << 6, $m << 16, -$m);
        }
        return $m;
    }


    /**
     * generate token
     *
     * @param string $b The URL to shorten
     *
     * @return string The token for google authentication
     */
    protected function generateToken($b)
    {
        $i = $this->e($b);
        $i = $i >> 2 & 1073741823;
        $i = $i >> 4 & 67108800 | $i & 63;
        $i = $i >> 4 & 4193280 | $i & 1023;
        $i = $i >> 4 & 245760 | $i & 16383;
        $j = "7";
        $h = $this->f($b);
        $k = ($i >> 2 & 15) << 4 | $h & 15;
        $k |= ($i >> 6 & 15) << 12 | ($h >> 8 & 15) << 8;
        $k |= ($i >> 10 & 15) << 20 | ($h >> 16 & 15) << 16;
        $k |= ($i >> 14 & 15) << 28 | ($h >> 24 & 15) << 24;
        $j .= $this->d($k);
        return $j;
    }


    public function short($url) {
        $params = sprintf('user=%s&url=%s&auth_token=%s', urlencode($this->user), urlencode($url), urlencode($this->generateToken($url)));
        try {
            $result = $this->_post($this->api, $params);
            var_dump($result);
            $result = json_decode($result);
            return $result->short_url;
        } catch (Exception $e) {
            return "";
        }
    }
}
