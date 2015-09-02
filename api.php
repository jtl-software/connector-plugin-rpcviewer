<?php
namespace rpcview;

class Api
{
    public static function run()
    {
        //error_reporting(E_ALL);
        //ini_set('display_errors', true);

        set_time_limit(0);

        $handle = @fopen("../../logs/rpc.json", "r");

        while (true) {
            $last_ajax_call = isset($_GET['timestamp']) ? (int)$_GET['timestamp'] : null;

            clearstatcache();

            $last_change_in_data_file = filemtime("../../logs/rpc.json");

            if ($last_ajax_call == null || $last_change_in_data_file > $last_ajax_call) {
                if (!isset($_GET['pointer']) || $_GET['pointer'] == 0) {
                    fseek($handle, -128000, SEEK_END);
                } else {
                    fseek($handle, $_GET['pointer']);
                }

                $data = array();

                while (($buffer = fgets($handle)) !== false) {
                    $data[] = json_decode(trim($buffer, "\n"));
                }

                $result = array(
                    'data' => $data,
                    'timestamp' => $last_change_in_data_file,
                    'lastPointer' => $_GET['pointer'],
                    'pointer' => ftell($handle)
                );

                header('Content-Type: application/json');
                echo json_encode($result);

                break;
            } else {
                sleep(1);
                continue;
            }
        }
    }

    public static function reset()
    {
        unlink("../../logs/rpc.json");
    }
}

switch($_GET['action']) {
    case 'run':
        Api::run();
        break;
    case 'reset':
        Api::reset();
        break;
}
