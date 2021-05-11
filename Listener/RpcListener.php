<?php

namespace RpcViewer\Listener;
use Jtl\Connector\Core\Event\RpcEvent;

class RpcListener
{
    /**
     * @var false|resource
     */
    protected $json;

    /**
     * @var string
     */
    protected $logDir;

    /**
     * RpcListener constructor.
     * @param string $logDir
     * @throws \Exception
     */
    public function __construct(string $logDir)
    {
        $fileName = sprintf('%s/rpcview_current.json', $logDir);
        $this->json = fopen($fileName, 'a');
        if (!is_resource($this->json)) {
            throw new \Exception(sprintf('Could not open file %s', $fileName));
        }
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

        fwrite($this->json, json_encode($entry) . "\n");
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

        fwrite($this->json, json_encode($entry) . "\n");
    }
}