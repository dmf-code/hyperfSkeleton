<?php
declare(strict_types=1);


if (!function_exists("http_client")) {
    function http_client($url, $method='GET', $options=[])
    {
        try {
            $client = new \GuzzleHttp\Client();

            if (!isset($options['timeout'])) {
                $options['timeout'] = 30;
            }

            $url = 'http://'. config('bb_ip'). ':'. config('bb_port'). $url;

            $res = $client->request($method, $url, $options);

            $res = json_decode($res->getBody()->getContents(), true);
        } catch (\Exception $e) {
            log_standard_error($e);
            return resp(400, 'http请求失败');
        }

        return $res;
    }
}

if (!function_exists("resp")) {
    function resp($code, $msg, $data = [])
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
    }
}

if (!function_exists("log_standard_error")) {
    function log_standard_error(\Exception $e) {
        $str = sprintf("File: %s, Line %s, %s", $e->getFile(), $e->getLine(), $e->getMessage());
        \App\Log::error($str);
    }
}
