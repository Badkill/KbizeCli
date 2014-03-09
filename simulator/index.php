<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->post('/login', function (Request $request) {

    $data = json_decode($request->getContent(), true);
    if (
        $data['email'] == 'name.surname@email.com' &&
        $data['pass']  == 'secretpassword'
    ) {
        $userData = [
            'email' => 'name.surname@email.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
            'apikey' => 'ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy'
        ];

        return new Response(json_encode($userData), 200);
    }

    return new Response('User or password is invalid', 403);
});

$app->post('/get_projects_and_boards', function () {
    $data = [
        'foo' => 'bar',
        'bo' => 'ciao',
    ];

    return json_encode($data);
});

$app->post('/get_all_tasks', function () use ($app) {
    $yamlParser = new Parser();
    $data = $yamlParser->parse(file_get_contents('../fixtures/tasks.yml'));

    return $app->json($data, 200);
});

$app->run();
