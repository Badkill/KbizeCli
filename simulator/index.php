<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->post('/login', function (Request $request) {

    $inputs = json_decode($request->getContent(), true);
    if (
        $inputs['email'] == 'name.surname@email.com' &&
        $inputs['pass']  == 'secret'
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
    $projectsAndBoards = [
        'projects' => [
            0 =>[
                'name' => 'Company',
                'id' => '1',
                'boards' => [
                    0 => [
                        'name' => 'Main development',
                        'id' => '2',
                    ],
                    1 => [
                        'name' => 'Support board',
                        'id' => '3',
                    ],
                ],
            ],
        ],
    ];

    return json_encode($projectsAndBoards);
});

$app->post('/get_all_tasks', function (Request $request) use ($app) {
    $inputs = json_decode($request->getContent(), true);
    if ($inputs['boardid'] != '2') {
        return new Response('Board ' . $inputs['boardid'] . ' not exists', 400);
    }

    $yamlParser = new Parser();
    $tasks = $yamlParser->parse(file_get_contents('../fixtures/tasks.yml'));

    return $app->json($tasks, 200);
});

$app->post('/get_full_board_structure', function (Request $request) use ($app) {
    $inputs = json_decode($request->getContent(), true);
    if ($inputs['boardid'] != '2') {
        return new Response('Board ' . $inputs['boardid'] . ' not exists', 400);
    }

    $yamlParser = new Parser();
    $tasks = $yamlParser->parse(file_get_contents('../fixtures/fullBoardStructure.yml'));

    return $app->json($tasks, 200);
});

$app->run();
