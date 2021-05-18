<?php
namespace RpcViewer;

use DI\Container;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Core\Config\GlobalConfig;
use Jtl\Connector\Core\Definition\Event;
use Jtl\Connector\Core\Plugin\PluginInterface;
use Noodlehaus\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use RpcViewer\Listener\RpcListener;

class Bootstrap implements PluginInterface
{
    public function registerListener(ConfigInterface $config, Container $container, EventDispatcher $dispatcher)
    {
        $listener = new RpcListener($config->get(ConfigSchema::LOG_DIR));
        $dispatcher->addListener(Event::createRpcEventName(Event::BEFORE), array($listener, 'beforeAction'));
        $dispatcher->addListener(Event::createRpcEventName(Event::AFTER), array($listener, 'afterAction'));
    }
}
