<?php
declare(strict_types=1);


use Hyperf\Guzzle\ClientFactory;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

if (!function_exists("http_client")) {
    function http_client($url, $method='GET', $options=[])
    {
        try {
            $clientFactory = new ClientFactory(ApplicationContext::getContainer());
            $client = $clientFactory->create();

            if (!isset($options['timeout'])) {
                $options['timeout'] = 30;
            }

            $res = $client->request($method, $url, $options);

            $res = json_decode($res->getBody()->getContents(), true);

        } catch (\Exception $e) {
            \App\Log::error($url);
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

if (!function_exists("request")) {
    function request()
    {
        return ApplicationContext::getContainer()->get(\Hyperf\HttpServer\Contract\RequestInterface::class);
    }
}

if (!function_exists("response")) {
    function response()
    {
        return ApplicationContext::getContainer()->get(\Hyperf\HttpServer\Contract\ResponseInterface::class);
    }
}

if (!function_exists("redis")) {
    function redis()
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Redis\Redis::class);
    }
}

if (!function_exists("log_standard_error")) {
    function log_standard_error(\Exception $e) {
        $str = sprintf("File: %s, Line %s, %s", $e->getFile(), $e->getLine(), $e->getMessage());
        \App\Log::error($str);
    }
}

if (!function_exists("file_path")) {
    function file_path($path='') {
        $path = BASE_PATH . '/storage/files/' . $path;
        return $path;
    }
}

if (!function_exists("logger")) {
    function logger($group='default')
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get('log', $group);
    }
}
