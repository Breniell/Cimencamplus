<?php
require('../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/cimencamplus:view', $context);

use local_cimencamplus\model\cnps_request;
use local_cimencamplus\output\renderer;

// ─── Charger le CSS et injecter le widget CNPS ───
$PAGE->requires->css(new moodle_url('/local/cimencamplus/pix/styles.css'));
global $DB;
$counts = [
    'pending'  => $DB->count_records('cimencamplus_cnps', ['status' => 'pending']),
    'approved' => $DB->count_records('cimencamplus_cnps', ['status' => 'approved']),
    'rejected' => $DB->count_records('cimencamplus_cnps', ['status' => 'rejected']),
];
$PAGE->requires->js_init_code("
    document.addEventListener('DOMContentLoaded', function(){
        var block = document.createElement('div');
        block.id = 'cimencam-widget';
        block.innerHTML = '<h4>CIMENCAM CNPS</h4>'
            + '<ul>'
            + '<li>En attente: {$counts['pending']}</li>'
            + '<li>Validées: {$counts['approved']}</li>'
            + '<li>Rejetées: {$counts['rejected']}</li>'
            + '</ul>';
        document.body.insertBefore(block, document.body.firstChild);
    });
");
// ────────────────────────────────────────────────────

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('cnpsstatus', 'local_cimencamplus'));

// Récupérer et afficher le tableau des demandes
$filters = [
    'status' => optional_param('status', '', PARAM_ALPHA),
    'userid' => optional_param('userid', 0, PARAM_INT),
    'from'   => optional_param('from', '', PARAM_RAW),
    'to'     => optional_param('to', '', PARAM_RAW),
];
echo renderer::render_filters_form($filters);
$requests = cnps_request::get_requests($filters);
echo renderer::render_requests_table($requests);

echo $OUTPUT->footer();
