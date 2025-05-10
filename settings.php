<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_cimencamplus',
        get_string('pluginname', 'local_cimencamplus')
    );

    $settings->add(new admin_setting_configtext(
        'local_cimencamplus/notifyemail',
        get_string('notifyemail', 'local_cimencamplus'),
        get_string('notifyemail_desc', 'local_cimencamplus'),
        '', PARAM_EMAIL
    ));

    $settings->add(new admin_setting_configtext(
        'local_cimencamplus/smsapi',
        get_string('smsapi', 'local_cimencamplus'),
        get_string('smsapi_desc', 'local_cimencamplus'),
        '', PARAM_RAW
    ));

    $settings->add(new admin_setting_configtext(
        'local_cimencamplus/smsfrom',
        get_string('smsfrom', 'local_cimencamplus'),
        get_string('smsfrom_desc', 'local_cimencamplus'),
        '', PARAM_RAW
    ));

    $ADMIN->add('localplugins', $settings);
}
