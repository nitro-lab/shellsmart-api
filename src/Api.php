<?php

namespace NitroLab\ShellsmartAPI;

use NitroLab\ShellsmartAPI\Exceptions\CardNotFoundException;
use NitroLab\ShellsmartAPI\Exceptions\NeedRegisterException;
use NitroLab\ShellsmartAPI\Connector\Connector;
use NitroLab\ShellsmartAPI\Connector\Response;

class Api
{
    private $connector;
    private $response;

    public function __construct()
    {
        $this->connector = new Connector;
    }

    public function getPhoneByCard($card)
    {
        //GET /v2/holder/phone
        $response = new Response(
            $this->connector->send('holder/phone', 'GET', ['card' => $card])
        );

        $body = $response->body();

        if(!isset($body->phone) || empty($body->phone)){
            throw new CardNotFoundException('Card not found', $response);
        }

        return $response;
    }

    public function loginByPhone($phone)
    {
        $cleared_phone = $this->clearPhone($phone);
        $response = new Response(
            $this->connector->send('holder/login', 'POST', ['mobile' => $cleared_phone])
        );

        $body = $response->body();

        if(!isset($body->errorCode) || $body->errorCode == 129){
            throw new NeedRegisterException('User unauthorized', $response);
        }

        return $response;
    }

    public function otp($phone)
    {
        //GET /v2/holder/otp
        $cleared_phone = $this->clearPhone($phone);
        $response = $this->connector->send('holder/otp', 'GET', ['phone' => $cleared_phone]);

        return new Response($response);
    }

    public function register($phone)
    {
        $cleared_phone = $this->clearPhone($phone);
        $response = $this->connector->send('holder/registration', 'POST', ['mobile' => $cleared_phone]);

        return new Response($response);
    }

    public function userUpdate($holder_id, $holder_data)
    {
        $response = $this->connector->send('holder/data/change', 'POST', [
            'holderId' => $holder_id,
            'holderData' => $holder_data,
        ]);

        return new Response($response);
    }

    public function accounts($holder_id)
    {
        $response = $this->connector->send('holder/accounts', 'GET', [
            'holderId' => $holder_id
        ]);

        return new Response($response);
    }

    public function cardAdd($card, $holder_id, $account_id)
    {
        $response = $this->connector->send('holder/card/add', 'POST', [
            'holderId' => $holder_id,
            'accountId' => $account_id,
            'cardNo' => $card,
        ]);

        return new Response($response);
    }

    public function getFuelPrices()
    {
        $response = $this->connector->send('loyalty/fuels/price');

        return new Response($response);
    }

    public function wallets()
    {
        $response = $this->connector->send('loyalty/purses');

        return new Response($response);
    }

    public function userWallets($card)
    {
        //GET /v2/holder/purses
        $response = $this->connector->send('holder/purses', 'GET', ['card_No' => $card]);

        return new Response($response);
    }

    public function precalculation($account_id, $purse_id, $good, $quantity = 0, $price = 0, $amount = 0)
    {
        //payments/precalculation

        $response = $this->connector->send('payments/precalculation', 'POST',
            [
                'accountId' => $account_id,
                'purseId' => $purse_id,
                'goods' => [
                    [
                        'code2' => $good,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount
                    ]
                ]
            ]
        );

        return new Response($response);
    }

    public function refill($order_id, $account_id, $purse_id, $good, $quantity, $price, $amount, $discount)
    {
        //POST /v2/payments/refill
        $response = $this->connector->send('payments/refill', 'POST',
            [
                'transactionNo' => $order_id,
                'accountId' => $account_id,
                'purseId' => $purse_id,
                'goods' => [
                    [
                        'code2' => $good,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'discount' => $discount
                    ]
                ]
            ]
        );

        return new Response($response);
    }

    public function getFuelPrice($fuel_id)
    {
        $prices = collect($this->getFuelPrices()->body()->fuels)->keyBy('fuelId');
        return $prices->get($fuel_id)->fuelPrice;
    }

    public function getPurseId($fuel_id)
    {
//        $wallets = $this->wallets()->body();
        $goods_wallet = $this->goodsWallet();
        return $goods_wallet[$fuel_id];
    }

    public function getDiscount($account_id, $purse_id, $fuel_id, $fuel_price, $quantity, $amount)
    {
        $cost = $this->precalculation($account_id, $purse_id, $fuel_id, $quantity, $fuel_price, $amount)->body();

        return  $cost->goods[0]->discount;
    }


    public function clearPhone($phone)
    {
        return preg_replace('/\D+/', '', $phone);
    }

    public function goodsWallet()
    {
        $wallets = $this->wallets()->body()->purses;
//        $goods_wallet = $this->goodsWallet($wallets->purses);

        $goods = [];
        foreach($wallets as $wallet){
            foreach($wallet->goods as $good){
                $goods[$good->code2] = $wallet->purseId;
            }
        }

        return $goods;
    }
}
