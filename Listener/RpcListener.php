<?php

namespace RpcViewer\Listener;
use Jtl\Connector\Core\Event\RpcEvent;

class RpcListener
{
    /**
     * @var false|resource
     */
    protected $fileHandle;

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
        $this->fileHandle = fopen($fileName, 'a');
        if (!is_resource($this->fileHandle)) {
            throw new \Exception(sprintf('Could not open file %s', $fileName));
        }
    }

    /**
     * @param RpcEvent $event
     */
    public function beforeAction(RpcEvent $event): void
    {
        $this->writeEntry($event, 'request');
    }

    /**
     * @param RpcEvent $event
     */
    public function afterAction(RpcEvent $event): void
    {
        $this->writeEntry($event, 'result');
    }

    /**
     * @param RpcEvent $event
     * @param string $type
     */
    protected function writeEntry(RpcEvent $event, string $type): void
    {
        $entry = [
            'type' => $type,
            'controller' => $event->getController(),
            'action' => $event->getAction(),
            'timestamp' => date('H:i:s', time()),
            'data' => $event->getData()
        ];

        fwrite($this->fileHandle, json_encode($entry) . "\n");
    }
}