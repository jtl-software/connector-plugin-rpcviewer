<?php
namespace rpcview\listener;

use Jtl\Connector\Core\Event\RpcEvent;

class RpcListener
{
    private static $instance = null;
    private $json;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->json = fopen(__DIR__.'/../../../logs/rpcview_current.json', 'a');
    }

    public function beforeAction(RpcEvent $event)
    {
        $entry = [
            'type' => 'request',
            'controller' => $event->getController(),
            'action' => $event->getAction(),
            'timestamp' => date('H:i:s', time()),
            'data' => $event->getData()
        ];

        fwrite($this->json, json_encode($entry)."\n");
    }

    public function afterAction(RpcEvent $event)
    {
        $entry = [
            'type' => 'result',
            'controller' => $event->getController(),
            'action' => $event->getAction(),
            'timestamp' => date('H:i:s', time()),
            'data' => $event->getData()
        ];

        fwrite($this->json, json_encode($entry)."\n");
    }
}