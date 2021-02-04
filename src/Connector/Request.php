<?php

namespace NitroLab\ShellsmartApi\Connector;

use GuzzleHttp\Client;

abstract class Request
{
    protected $client;

    public function __construct()
    {
        $config = config('сrm_api');

        $this->client = new Client([
            'base_uri' => $config['url'],
            'verify' => false,
        ]);
    }

    abstract protected function getHeaders();

    public function send(string $api_method, $send_method = 'GET', array $params = [])
    {
        $request_variables = $this->getHeaders();

        $request_url = $api_method;
        if($send_method == 'POST') {
            if(count($params)){
                $request_variables['json'] = $params;
            }
        }else{
            $request_url = "$api_method?".http_build_query($params);
        }


        $request = $this->client->request(
            $send_method,
            $request_url,
            $request_variables
        );

        return $request;
    }
}
