<?php
namespace KbizeCli\Http;

use Guzzle\Http\ClientInterface as GuzzleClientInterface;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Message\EntityEnclosingRequest as GuzzleEntityEnclosingRequest;
use Guzzle\Common\Event as Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\OptionsResolver\OptionsResolver;
use KbizeCli\Http\Exception\ForbiddenException;
use KbizeCli\Http\Exception\RequestException;

class Client implements ClientInterface
{
    private function __construct(array $config, GuzzleClientInterface $rawClient)
    {
        $resolver = $this->initOptionsResolver();
        $this->config = $resolver->resolve($config);

        $this->rawClient = $rawClient;
        $this->rawClient->setBaseUrl($this->config['baseUrl']);
        $this->rawClient->setDefaultOption('headers', $this->defaultHeaders());

        /* $this->rawClient->getEventDispatcher()->addListener( */
        /*     'request.error', */
        /*     function (Event $event) { */
        /*         $this->handleError($event); */
        /*     } */
        /* ); */
    }

    public static function fromConfig(array $config, GuzzleClientInterface $rawClient = null)
    {
        if (!$rawClient) {
            $rawClient = new GuzzleClient();
        }

        return new static($config, $rawClient);
    }

    public function setApikey($apikey)
    {
        $this->rawClient->setDefaultHeaders(array_merge(
            $this->rawClient->getDefaultOption('headers'),
            ['apikey' => $apikey]
        ));
    }

    public function __call($method, $args)
    {
        $result = call_user_func_array(array($this->rawClient, $method), $args);
        if ($result instanceof GuzzleEntityEnclosingRequest) {
            return new Request($result);
        }

        return $result;
    }

    protected function initOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'baseUrl',
        ));
        $resolver->setOptional(array( //FIXME:! separate class with required apikey
            'apikey',
        ));

        return $resolver;
    }

    protected function defaultHeaders()
    {
        $defaults = array (
            'Accept' => 'application/json',
        );

        if (array_key_exists('apikey', $this->config)) {
            $defaults = array_merge($defaults, ['apikey' => $this->config['apikey']]);
        }

        return $defaults;
    }
}
