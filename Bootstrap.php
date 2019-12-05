<?php
namespace RpcViewer;

use Jtl\Connector\Core\Definition\Event;
use Jtl\Connector\Core\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use RpcViewer\Listener\RpcListener;

class Bootstrap implements PluginInterface
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $listener = RpcListener::getInstance();

        $dispatcher->addListener(Event::createRpcEventName(Event::BEFORE), array($listener, 'beforeAction'));
        $dispatcher->addListener(Event::createRpcEventName(Event::AFTER), array($listener, 'afterAction'));
    }
}
