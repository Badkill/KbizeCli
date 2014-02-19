<?php
namespace Badkill\KbizeCli\Sdk;

use Badkill\KbizeCli\Http\ClientInterface;
use Badkill\KbizeCli\Http\Exception\ServerErrorResponseException;

class Sdk implements ApiInterface
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function login($email, $password)
    {
        $request = $this->client->post(sprintf(
            'login/email/%s/pass/%s',
            urlencode($email),
            $password
        ));

        $data = $this->ensureIsValidResponse($request);

        return $data;
    }

    public function getProjectsAndBoards()
    {

    }

    public function getBoardStructure($boardId)
    {

    }

    public function getAllTasks($boardId)
    {

    }

    public function getTaskDetails($boardId, $taskId)
    {

    }

    //FIXME:! wrap the response and inject this method
    private function ensureIsValidResponse($request)
    {
        $isValid = true;
        $response = $request->send();

        try {
            $data = $response->json();
        } catch (\Exception $e) {
            $isValid = false;
            $data = array('error' => 'Invalid json in response: `' . $response->getBody() . '`');
        }

        if ($response->isError() || !$isValid) {
            throw new ServerErrorResponseException($request, $repsonse);
        }

        return $data;
    }
}
