<?php
namespace local_cimencamplus\service;
defined('MOODLE_INTERNAL') || die();

use context_system;
use core_user;
use \local_cimencamplus\ocr\vision;
use \local_cimencamplus\api\cnpsclient;

class cnps_service {
    /**
     * Traite et enregistre une demande CNPS (OCR, API, DB, notifications).
     *
     * @param \stdClass $data Form data (cnpsnum, draftitemid, etc.)
     * @return int $newid L’ID du record inséré.
     */
    public static function submit_request(\stdClass $data) {
        global $DB, $USER;

        $context = context_system::instance();

        // 1) Traitement OCR
        $tempdir = make_temp_directory('cimencamplus');
        $fs      = get_file_storage();
        $files   = $fs->get_area_files(
            $USER->id, 'local_cimencamplus', 'cnpsscan', $data->cnpsscan, 'filename', false
        );
        if ($file = reset($files)) {
            $filepath = $tempdir . DIRECTORY_SEPARATOR . $file->get_filename();
            $file->copy_to_pathname($filepath);
            $texte = vision::detect_text($filepath);
            if (preg_match('/\b\d{8,10}\b/', $texte, $m)) {
                $data->cnpsnum = $m[0];
            }
        }

        // 2) Vérification via API
        $status = 'pending';
        try {
            $api = cnpsclient::verify($data->cnpsnum);
            if (empty($api['valid'])) {
                $status = 'rejected';
            }
        } catch (\Exception $e) {
            debugging('CNPS API error: '.$e->getMessage());
        }

        // 3) Insertion en base
        $record = (object)[
            'userid'      => $USER->id,
            'cnpsnum'     => $data->cnpsnum,
            'status'      => $status,
            'timecreated' => time()
        ];
        $newid = $DB->insert_record('cimencamplus_cnps', $record);

        // 4) Notifications (email & SMS)
        $managers = get_role_users($DB->get_field('role','id',['shortname'=>'manager']), $context);
        foreach ($managers as $mgr) {
            // Email
            email_to_user(
                $mgr,
                core_user::get_support_user(),
                "Nouvelle CNPS {$record->cnpsnum}",
                "L’utilisateur {$USER->id} a soumis le CNPS {$record->cnpsnum} (ID $newid)."
            );
            // SMS (Twilio)
            if (!empty($mgr->phone1)) {
                $twilio = new \Twilio\Rest\Client(
                    get_config('local_cimencamplus','smsapi'),
                    ''  // ou ton token ici
                );
                $twilio->messages->create(
                    $mgr->phone1,
                    [
                        'from' => get_config('local_cimencamplus','smsfrom'),
                        'body' => "CNPS {$record->cnpsnum} via user {$USER->id}"
                    ]
                );
            }
        }

        return $newid;
    }
}
