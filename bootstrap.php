<?php
namespace rpcview;

use Jtl\Connector\Core\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use rpcview\listener\RpcListener;
use Jtl\Connector\Core\Event\Rpc\RpcAfterEvent;
use Jtl\Connector\Core\Event\Rpc\RpcBeforeEvent;

class Bootstrap implements PluginInterface
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $listener = RpcListener::getInstance();

        $dispatcher->addListener(RpcBeforeEvent::EVENT_NAME, array($listener, 'beforeAction'));
        $dispatcher->addListener(RpcAfterEvent::EVENT_NAME, array($listener, 'afterAction'));
    }
}
