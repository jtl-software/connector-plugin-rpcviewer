<?php
class Viewer
{
    public static function run()
    {
        set_time_limit(0);

        $handle = @fopen("../logs/rpc.log", "r");

        while (true) {
            $last_ajax_call = isset($_GET['timestamp']) ? (int)$_GET['timestamp'] : null;

            clearstatcache();

            $last_change_in_data_file = filemtime("../logs/rpc.log");

            if ($last_ajax_call == null || $last_change_in_data_file > $last_ajax_call) {
                if (!isset($_GET['pointer']) || $_GET['pointer'] == 0) {
                    fseek($handle, -128000, SEEK_END);
                } else {
                    fseek($handle, $_GET['pointer']);
                }

                $data = array();

                while (($buffer = fgets($handle)) !== false) {
                    $timestampRegEx = '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/';
                    $requestRegEx = '/rpc.DEBUG: RequestPacket: {"method":"(.*)","params":(.*),"jtlrpc"/';
                    $resultRegEx = '/rpc.DEBUG: {"result":(.*),"error":(.*),"jtlrpc"/';

                    preg_match($timestampRegEx, $buffer, $timestamp);

                    if (preg_match($requestRegEx, $buffer, $request) !== 0) {
                        $data[] = array(
                            'type' => 'request',
                            'label' => '<b>'.$request[1].'</b>',
                            'timestamp' => $timestamp[4] . ':' . $timestamp[5] . ':' . $timestamp[6],
                            'data' => json_decode(stripslashes(trim($request[2], '"')))
                        );
                    } elseif (preg_match($resultRegEx, $buffer, $result) !== 0) {
                        if ($result[2] !== 'null') {
                            $data[] = array(
                                'type' => 'error',
                                'label' => 'Error',
                                'timestamp' => $timestamp[4] . ':' . $timestamp[5] . ':' . $timestamp[6],
                                'data' => json_decode($result[2])
                            );
                        } else {
                            $data[] = array(
                                'type' => 'result',
                                'label' => 'Result',
                                'timestamp' => $timestamp[4] . ':' . $timestamp[5] . ':' . $timestamp[6],
                                'data' => json_decode($result[1])
                            );
                        }
                    }
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
        unlink("../logs/rpc.log");
    }
}

switch($_GET['action']) {
    case 'run':
        Viewer::run();
        break;
    case 'reset':
        Viewer::reset();
        break;
}
