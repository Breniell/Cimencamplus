<?php
defined('MOODLE_INTERNAL') || die();

function local_cimencamplus_extend_navigation(global_navigation $nav) {
    if (isloggedin() && is_siteadmin()) {
        $node = $nav->add(
            get_string('pluginname', 'local_cimencamplus'),
            new moodle_url('/local/cimencamplus/index.php')
        );
        $node->add(
            get_string('cnpsstatus', 'local_cimencamplus'),
            new moodle_url('/local/cimencamplus/dashboard.php')
        );
    }
}

function local_cimencamplus_before_standard_html_head() {
    global $PAGE, $DB;

    // ✅ NE RIEN FAIRE EN CLI
    if (defined('CLI_SCRIPT') || PHP_SAPI === 'cli') {
        return;
    }

    // ✅ Vérification de la validité de $PAGE
    if (empty($PAGE) || !($PAGE instanceof moodle_page)) {
        return;
    }

    // ✅ Injection CSS (web uniquement)
    $PAGE->requires->css(new moodle_url('/local/cimencamplus/pix/styles.css'));

    // ✅ Widget si connecté
    if (isloggedin()) {
        $pending  = $DB->count_records('cimencamplus_cnps', ['status' => 'pending']);
        $approved = $DB->count_records('cimencamplus_cnps', ['status' => 'approved']);
        $rejected = $DB->count_records('cimencamplus_cnps', ['status' => 'rejected']);

        $PAGE->requires->js_init_code("
            document.addEventListener('DOMContentLoaded', function() {
                var block = document.createElement('div');
                block.id = 'cimencam-widget';
                block.innerHTML = `<h4>CIMENCAM CNPS</h4>
                    <ul>
                        <li>En attente: ${$pending}</li>
                        <li>Validées: ${$approved}</li>
                        <li>Rejetées: ${$rejected}</li>
                    </ul>`;
                document.body.insertBefore(block, document.body.firstChild);
            });
        ");
    }
}


/*
 * Nous retirons la fonction before_standard_html_head pour qu’elle ne s’exécute
 * ni en CLI (purge_caches, cron…) ni sur des pages non-web, ce qui donnait l’erreur.
 */
// function local_cimencamplus_before_standard_html_head() {
//     // plus rien ici
// }


// // injection CSS + widget CNPS (UNIQUEMENT en contexte web, jamais en CLI)
// function local_cimencamplus_before_standard_html_head() {
//     global $PAGE, $DB;

//     // 1) En CLI Moodle (purge_caches, cron, install...) : on sort immédiatement
//     if (PHP_SAPI === 'cli' || defined('CLI_SCRIPT')) {
//         return;
//     }

//     // 2) Si $PAGE n'est pas un objet moodle_page, ne rien faire
//     if (empty($PAGE) || !($PAGE instanceof moodle_page)) {
//         return;
//     }

//     // 3) Charger notre CSS
//     $PAGE->requires->css(new moodle_url('/local/cimencamplus/pix/styles.css'));

//     // 4) Si l'utilisateur est connecté, injecter le widget CNPS
//     if (isloggedin()) {
//         $counts = [
//             'pending'  => $DB->count_records('cimencamplus_cnps', ['status'=>'pending']),
//             'approved' => $DB->count_records('cimencamplus_cnps', ['status'=>'approved']),
//             'rejected' => $DB->count_records('cimencamplus_cnps', ['status'=>'rejected']),
//         ];
//         $PAGE->requires->js_init_code('
//             document.addEventListener("DOMContentLoaded", function(){
//                 var block = document.createElement("div");
//                 block.id = "cimencam-widget";
//                 block.innerHTML = "<h4>CIMENCAM CNPS</h4>"
//                     + "<ul>"
//                     + "<li>En attente: '.$counts['pending'].'</li>"
//                     + "<li>Validées: '.$counts['approved'].'</li>"
//                     + "<li>Rejetées: '.$counts['rejected'].'</li>"
//                     + "</ul>";
//                 document.body.insertBefore(block, document.body.firstChild);
//             });
//         ');
//     }
// }
