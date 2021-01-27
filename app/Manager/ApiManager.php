<?php


namespace App\Manager;


use App\Log;
use GuzzleHttp\Client;

class ApiManager
{
    public function getUserInfo($token): array
    {
        try {
            $res = http_client(
                '/api/user/userInfo',
                'POST', [
                    'form_params' => [
                        'token' => $token
                    ]
                ]
            );

        } catch (\Exception $e) {
            log_standard_error($e);
            return resp(400, '获取用户信息失败');
        }

        return resp(200, 'ok');
    }
}