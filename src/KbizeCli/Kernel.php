<?php
namespace KbizeCli;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Kernel
{
    public function __construct($env = 'prod')
    {
        // create and populate the container
        $this->container = new ContainerBuilder();

        // some useful paths
        $paths = array();
        $paths['root'] = __DIR__ . '/../../';
        $paths['config'] = $paths['root'] . 'app/config/kbize/';
        $this->container->setParameter('paths', $paths);

        // the main config
        $loader = new YamlFileLoader($this->container, new FileLocator($paths['config']));
        $loader->load("config.$env.yml");

        $this->gateway = $this->container->get('gateway');
    }

    public function login($email, $password)
    {
        $this->gateway->login($email, $password);
    }
}
