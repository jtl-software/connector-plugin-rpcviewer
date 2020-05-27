<?php
namespace RpcViewer;

$logDir = sprintf('%s/var/log', dirname(dirname(__DIR__)));
$configFile = sprintf('%s/config/config.php', __DIR__);
if(is_file($configFile)) {
    $config = require $configFile;
    if(is_array($config) && isset($config['logDir'])) {
        $logDir = $config['logDir'];
    }
}

require_once __DIR__ . '/ApiViewer.php';

$viewer = new ApiViewer($logDir);

switch($_GET['action']) {
    case 'run':
        $viewer->run();
        break;
    case 'clear':
        $viewer->clear();
        break;
    case 'latest':
        $viewer->getLatest();
        break;
}
