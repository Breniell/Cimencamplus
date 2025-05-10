<?php
namespace local_cimencamplus\output;
defined('MOODLE_INTERNAL') || die();

use html_table;
use html_writer;

class renderer {
    public static function render_filters_form($current) {
        $html  = '<form method="get">';
        $html .= '<label>'.get_string('filterstatus','local_cimencamplus').'</label>';
        $html .= '<select name="status">'
              . '<option value="">'.get_string('all','local_cimencamplus').'</option>'
              . '<option value="pending">'.get_string('pending','local_cimencamplus').'</option>'
              . '<option value="approved">'.get_string('approved','local_cimencamplus').'</option>'
              . '<option value="rejected">'.get_string('rejected','local_cimencamplus').'</option>'
              . '</select>';
        $html .= '<label>'.get_string('filteruserid','local_cimencamplus').'</label>';
        $html .= '<input type="text" name="userid" value="'.($current['userid'] ?? '').'">';
        $html .= '<label>'.get_string('from','local_cimencamplus').'</label>';
        $html .= '<input type="date" name="from" value="'.($current['from'] ?? '').'">';
        $html .= '<label>'.get_string('to','local_cimencamplus').'</label>';
        $html .= '<input type="date" name="to" value="'.($current['to'] ?? '').'">';
        $html .= '<button type="submit">'.get_string('filter','local_cimencamplus').'</button>';
        $html .= '</form>';
        return $html;
    }

    public static function render_requests_table($requests) {
        $table = new html_table();
        $table->head = [
            get_string('id','local_cimencamplus'),
            get_string('user','local_cimencamplus'),
            get_string('cnpsnum','local_cimencamplus'),
            get_string('status','local_cimencamplus'),
            get_string('date','local_cimencamplus'),
            get_string('actions','local_cimencamplus')
        ];
        foreach ($requests as $r) {
            $buttons = '';
            if ($r->status === 'pending') {
                $buttons .= html_writer::link(
                   new \moodle_url('/local/cimencamplus/action.php', ['id'=>$r->id,'action'=>'approve']),
                   get_string('approve','local_cimencamplus')
                );
                $buttons .= ' ';
                $buttons .= html_writer::link(
                   new \moodle_url('/local/cimencamplus/action.php', ['id'=>$r->id,'action'=>'reject']),
                   get_string('reject','local_cimencamplus')
                );
            }
            $table->data[] = [
                $r->id,
                $r->userid,
                $r->cnpsnum,
                $r->status,
                date('Y-m-d H:i',$r->timecreated),
                $buttons
            ];
        }
        return html_writer::table($table);
    }
}
