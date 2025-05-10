<?php
namespace local_cimencamplus\ocr;
defined('MOODLE_INTERNAL') || die();

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class vision {
    public static function detect_text($filepath) {
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$_SERVER['GOOGLE_APPLICATION_CREDENTIALS']);
        $client = new ImageAnnotatorClient();
        $image  = file_get_contents($filepath);
        $response = $client->textDetection($image);
        $client->close();
        $texts = $response->getTextAnnotations();
        if (!empty($texts)) {
            return $texts[0]->getDescription();
        }
        return '';
    }
}
