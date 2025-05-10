<?php
namespace local_cimencamplus\form;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class cnps_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'cnpsnum', get_string('cnpsnum', 'local_cimencamplus'));
        $mform->setType('cnpsnum', PARAM_TEXT);
        $mform->addRule('cnpsnum', null, 'required', null, 'client');

        $mform->addElement(
            'filepicker',
            'cnpsscan',
            get_string('cnpsscan','local_cimencamplus'),
            null,
            ['accepted_types'=>['image'], 'maxbytes'=>5*1024*1024]
        );
        $mform->addRule('cnpsscan', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('submit','local_cimencamplus'));
    }
}
