<?php

namespace NitroLab\ShellsmartAPI\Connector;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Connector extends Request
{
    private $token;

    public function __construct()
    {
        parent::__construct();

        $this->token = app()->make(Token::class)->getToken();
    }


    protected function getHeaders()
    {
        return [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->token,
            ],
            'verify' => false,
            'http_errors' => false,
        ];
    }

    public function send(string $api_method, $send_method = 'GET', array $params = [])
    {
        $time_start = microtime(true);

        $response = parent::send( $api_method, $send_method, $params);

        $time_end = microtime(true);
        $execution_time = $time_end - $time_start;

        $this->log($response, $api_method, $params, $execution_time);

        return $response;
    }

    protected function log($request, $api_method, $params, $execution_time = null)
    {
        Log::channel('api')->debug(
            $api_method
            . PHP_EOL
            . json_encode($params)
            . PHP_EOL
            . ($execution_time? ('Execution time: ' . $execution_time . PHP_EOL) : '')
            . $request->getBody()
        );
    }


}
