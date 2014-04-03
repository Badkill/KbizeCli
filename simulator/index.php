<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

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

    if (is_file($createdTaskFile = sys_get_temp_dir() . '/kbizeCliTmpData/newTaskTest')) {
        $newTasks = $yamlParser->parse(file_get_contents($createdTaskFile));
        if ($newTasks) {
            $tasks = array_merge($tasks, $newTasks);
        }
    }

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

$app->post('/create_new_task', function (Request $request) use ($app) {
    $inputs = json_decode($request->getContent(), true);
    $boardid = $inputs['boardid'];
    if ($boardid != '2') {
        return new Response('Board ' . $boardid . ' not exists', 400);
    }

    $id = rand();
    $taskData = array_merge(emptyTask(), $inputs, ['taskid' => $id]);
    unset($taskData['boardid']);

    $tmpFolder = sys_get_temp_dir() . '/kbizeCliTmpData';
    if (!is_dir($tmpFolder)) {
        mkdir($tmpFolder);
    }
    $yamlDumper = new Dumper();
    file_put_contents($tmpFolder . '/newTaskTest', $yamlDumper->dump([$taskData], 3));
    error_log('writed');
    $data = [
        'id' => $id,
    ];

    return $app->json($data, 200);
});

$app->run();


function emptyTask()
{
    return [
        "taskid" => '',
        "position" => '',
        "type" => '',
        "assignee" => '',
        "title" => '',
        "description" => '',
        "subtasks" => '',
        "subtaskscomplete" => '',
        "color" => '',
        "priority" => '',
        "size" => '',
        "deadline" => '',
        "deadlineoriginalformat" => '',
        "extlink" => '',
        "tags" => '',
        "columnid" => '',
        "laneid" => '',
        "leadtime" => '',
        "blocked" => '',
        "blockedreason" => '',
        "subtaskdetails" => '',
        "columnname" => '',
        "lanename" => '',
        "columnpath" => '',
        "logedtime" => '',
        "links" => '',
    ];
}
