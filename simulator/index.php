<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->post('/login', function () {
    $data = [
        'email' => 'danilo.silva@neomobile.com',
        'username' => 'danilo.silva',
        'realname' => 'Danilo Silva',
        'companyname' => 'Onebip',
        'timezone' => '0:0',
        'apikey' => 'ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy'
    ];

    return json_encode($data);
});

$app->post('/get_projects_and_boards', function () {
    $data = [
        'foo' => 'bar',
        'bo' => 'ciao',
    ];

    return json_encode($data);
});

$app->run();
