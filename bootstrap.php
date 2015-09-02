<?php
namespace rpcview;

use \jtl\Connector\Plugin\IPlugin;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \rpcview\listener\RpcListener;
use \jtl\Connector\Event\Rpc\RpcAfterEvent;
use \jtl\Connector\Event\Rpc\RpcBeforeEvent;

class Bootstrap implements IPlugin
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $listener = RpcListener::getInstance();

        $dispatcher->addListener(RpcBeforeEvent::EVENT_NAME, array($listener, 'beforeAction'));
        $dispatcher->addListener(RpcAfterEvent::EVENT_NAME, array($listener, 'afterAction'));
    }
}
