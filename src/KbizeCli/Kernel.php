<?php
namespace KbizeCli;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Kernel
{
    private $kbize;

    public function __construct(KbizeInterface $kbize)
    {
        $this->kbize = $kbize;
    }

    public function login($email, $password)
    {
        $this->kbize->login($email, $password);
    }

    /* public function getProjectsAndBoards() */
    /* { */
    /*     $this->kbize->getProjectsAndBoards(); */
    /* } */

    public function __call($method, $args)
    {
        call_user_func_array([$this->kbize, $method], $args);
    }
}
