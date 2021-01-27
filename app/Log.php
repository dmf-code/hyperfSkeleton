<?php
declare(strict_types=1);

namespace App;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

class Log
{
    public static function get(string $name = 'app')
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
    }

    public static function __callStatic($name, $arguments)
    {
        self::get()->$name(...$arguments);
    }
}
