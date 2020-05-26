<?php


namespace RpcViewer;


class ApiViewer
{
    /**
     * @var string
     */
    protected $logDir;

    /**
     * @var string
     */
    protected $current;

    /**
     * @var string
     */
    protected $latest;

    /**
     * Api constructor.
     * @param string $logDir
     */
    public function __construct(string $logDir)
    {
        $this->logDir = $logDir;
        $this->current = sprintf('%s/rpcview_current.json', $logDir);
        $this->latest = sprintf('%s/rpcview_latest.json', $logDir);
    }

    public function run()
    {
        set_time_limit(0);

        touch($this->current);

        $handle = @fopen($this->current, "r");

        while (file_exists($this->current)) {
            $last_ajax_call = isset($_GET['timestamp']) ? (int)$_GET['timestamp'] : null;

            clearstatcache();

            $last_change_in_data_file = filemtime($this->current);

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

    public function clear()
    {
        if (file_exists($this->current)) {
            unlink($this->latest);
            copy($this->current, $this->latest);
        }

        unlink($this->current);
    }

    public function getLatest()
    {
        if (file_exists($this->latest)) {
            $handle = @fopen($this->latest, "r");

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