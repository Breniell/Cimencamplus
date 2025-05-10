<?php
require('../../config.php');
require_login();
require_once($CFG->dirroot . '/local/cimencamplus/classes/form/cnps_form.php');
require_once(__DIR__ . '/vendor/autoload.php');  // Composer

$context = context_system::instance();
require_capability('local/cimencamplus:submit', $context);

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

$mform = new \local_cimencamplus\form\cnps_form();
$usercontext = context_user::instance($USER->id);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/'));
}

if ($data = $mform->get_data()) {
    // 1) Sauvegarde du scan
    file_save_draft_area_files(
        $data->cnpsscan,
        $usercontext->id,
        'local_cimencamplus',
        'cnpsscan',
        0,
        ['subdirs' => 0]
    );
    // 2) Traitement complet via service
    require_once(__DIR__ . '/classes/service/cnps_service.php');
    $newid = \local_cimencamplus\service\cnps_service::submit_request($data);
    // 3) Redirection
    redirect(
        new moodle_url('/local/cimencamplus/index.php'),
        get_string('submissionok', 'local_cimencamplus')
    );
}

// Affichage du formulaire
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('cnps', 'local_cimencamplus'));
$mform->display();
echo $OUTPUT->footer();
