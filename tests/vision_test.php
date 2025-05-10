<?php
// tests/vision_test.php
defined('MOODLE_INTERNAL') || die();

use local_cimencamplus\ocr\vision;

class vision_test extends \advanced_testcase {
    public function test_detect_text_returns_expected_number() {
        $this->resetAfterTest();

        // Chemin absolu vers l'image de test
        $fixture = __DIR__ . '/fixtures/cnps_sample.jpg';
        $this->assertFileExists($fixture, 'Le fichier de test doit exister');

        // Appel du service OCR
        $text = vision::detect_text($fixture);

        // On s’attend à trouver le numéro, par ex. "12345678"
        $this->assertStringContainsString('123456789', $text,
            'Le texte détecté doit contenir le numéro CNPS attendu');
    }
}
