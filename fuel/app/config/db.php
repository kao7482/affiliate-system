<?php

return array(
    'default' => array(
        'connection' => array(
            'dsn'      => 'mysql:host=localhost;dbname=affiliate_system',
            'username' => 'root',
            'password' => 'kao7482@MySQL',
        ),
        'profiling' => false,
    ),

    'redis'   => array(
        'default'   => array(
            'hostname' => 'localhost',
            'port'     => 6379,
        ),
        'profiling' => false,
    ),
);
