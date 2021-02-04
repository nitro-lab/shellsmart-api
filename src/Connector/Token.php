<?php

namespace NitroLab\ShellsmartApi\Connector;

use NitroLab\ShellsmartApi\Exceptions\TokenException;


class Token extends Request
{

    protected function getHeaders():array
    {
        return [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ];
    }

    public function getToken()
    {
        $token_request = $this->tokenRequest();
        if(!preg_match('/^2\d{2}$/', $token_request->getStatusCode())){
            throw new TokenException('Can\'t get a token', $token_request);
        }

        $body = json_decode($token_request->getBody());
        if(!isset($body->token)){
            throw new TokenException($body->errorDescription, $token_request);
        }

        return $body->token;
    }

    private function tokenRequest()
    {
        $config = config('сrm_api');

        $params = [
            'siteUser' => $config['login'],
            'sitePassword' => $config['password'],
        ];

        return $this->send('loyalty/authorization', 'POST', $params);
    }
}
