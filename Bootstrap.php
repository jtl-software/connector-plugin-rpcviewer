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
        $logDir = sprintf('%s/var/log', dirname(dirname(__DIR__)));
        $configFile = sprintf('%s/config/config.php', __DIR__);
        if(is_file($configFile)) {
            $config = require $configFile;
            if(is_array($config) && isset($config['logDir'])) {
                $logDir = $config['logDir'];
            }
        }

        $listener = new RpcListener($logDir);

        $dispatcher->addListener(Event::createRpcEventName(Event::BEFORE), array($listener, 'beforeAction'));
        $dispatcher->addListener(Event::createRpcEventName(Event::AFTER), array($listener, 'afterAction'));
    }
}
