<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cimencamplus:view' => [
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [
            'manager' => CAP_ALLOW,
            'admin'   => CAP_ALLOW,
        ],
    ],

    'local/cimencamplus:submit' => [
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [
            'student' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'admin'   => CAP_ALLOW,
        ],
    ],
];
