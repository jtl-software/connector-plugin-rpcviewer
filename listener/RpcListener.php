<?php
namespace rpcview\listener;

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

    public function beforeAction($event)
    {
        $entry = array(
            'type' => 'request',
            'controller' => $event->getController(),
            'action' => $event->getAction(),
            'timestamp' => date('H:i:s', time()),
            'data' => json_decode($event->getData())
        );

        fwrite($this->json, json_encode($entry)."\n");
    }

    public function afterAction($event)
    {
        $entry = array(
            'type' => 'result',
            'controller' => $event->getController(),
            'action' => $event->getAction(),
            'timestamp' => date('H:i:s', time()),
            'data' => json_decode($event->getData())
        );

        fwrite($this->json, json_encode($entry)."\n");
    }
}