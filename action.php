<?php
require('../../config.php');
require_login();
$context = context_system::instance();
require_capability('local_cimencamplus:view', $context);

$id     = required_param('id', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);
if (!in_array($action, ['approve','reject'])) {
    throw new coding_exception('Action invalide');
}

$newstatus = $action === 'approve' ? 'approved' : 'rejected';
$DB->set_field('cimencamplus_cnps','status',$newstatus, ['id'=>$id]);

// Notifier l’utilisateur
$req  = $DB->get_record('cimencamplus_cnps',['id'=>$id]);
$user = $DB->get_record('user',['id'=>$req->userid]);
$message = $action==='approve'
    ? "Votre demande CNPS {$req->cnpsnum} a été validée."
    : "Votre demande CNPS {$req->cnpsnum} a été rejetée.";
email_to_user(
    $user,
    core_user::get_support_user(),
    get_string($action,'local_cimencamplus'),
    $message
);

redirect(new moodle_url('/local/cimencamplus/dashboard.php'));
