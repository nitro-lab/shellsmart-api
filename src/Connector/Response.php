<?php

namespace NitroLab\ShellsmartApi\Connector;

class Response
{
    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function __get($name)
    {
        return $this->body()->{$name}?? null;
    }

    public function body()
    {
        return json_decode($this->response->getBody());
    }

    public function getBody()
    {
        return $this->response->getBody();
    }
}
