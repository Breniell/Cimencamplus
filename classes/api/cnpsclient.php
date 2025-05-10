<?php
namespace local_cimencamplus\api;
defined('MOODLE_INTERNAL') || die();

class cnpsclient {
    public static function verify($cnpsnum) {
        $url = 'https://api.cnps.cm/verify/'.$cnpsnum;
        $opts = ['http'=>['timeout'=>5]];
        $res = @file_get_contents($url, false, stream_context_create($opts));
        if ($res === false) {
            throw new \moodle_exception('cnpsapierror','local_cimencamplus');
        }
        return json_decode($res, true);
    }
}
