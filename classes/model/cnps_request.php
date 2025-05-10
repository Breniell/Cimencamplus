<?php
namespace local_cimencamplus\model;
defined('MOODLE_INTERNAL') || die();

class cnps_request {
    public static function get_requests($filters = []) {
        global $DB;
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = 'status = :status';
            $params['status']  = $filters['status'];
        }
        if (!empty($filters['userid'])) {
            $where[]            = 'userid = :userid';
            $params['userid']   = $filters['userid'];
        }
        if (!empty($filters['from'])) {
            $where[]           = 'timecreated >= :from';
            $params['from']    = strtotime($filters['from'].' 00:00:00');
        }
        if (!empty($filters['to'])) {
            $where[]            = 'timecreated <= :to';
            $params['to']       = strtotime($filters['to'].' 23:59:59');
        }

        $sql = 'SELECT * FROM {cimencamplus_cnps}'
             .(count($where) ? ' WHERE '.implode(' AND ', $where) : '')
             .' ORDER BY timecreated DESC';

        return $DB->get_records_sql($sql, $params);
    }
}
