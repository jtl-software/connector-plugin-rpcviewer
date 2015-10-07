<?php
namespace rpcview;

class Api
{
    private static $current = "../../logs/rpcview_current.json";
    private static $latest = "../../logs/rpcview_latest.json";

    public static function run()
    {
        set_time_limit(0);

        touch(static::$current);

        $handle = @fopen(static::$current, "r");

        while (file_exists(static::$current)) {
            $last_ajax_call = isset($_GET['timestamp']) ? (int)$_GET['timestamp'] : null;

            clearstatcache();

            $last_change_in_data_file = filemtime(static::$current);

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

    public static function clear()
    {
        if (file_exists(static::$current)) {
            unlink(static::$latest);
            copy(static::$current, static::$latest);
        }

        unlink(static::$current);
    }

    public static function getLatest()
    {
        if (file_exists(static::$latest)) {
            $handle = @fopen(static::$latest, "r");

            $data = array();

            while (($buffer = fgets($handle)) !== false) {
                $data[] = json_decode(trim($buffer, "\n"));
            }

            header('Content-Type: application/json');
            echo json_encode(array(
                'data' => $data
            ));
        }
    }
}

switch($_GET['action']) {
    case 'run':
        Api::run();
        break;
    case 'clear':
        Api::clear();
        break;
    case 'latest':
        Api::getLatest();
        break;
}
