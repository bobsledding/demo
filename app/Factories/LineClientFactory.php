<?php

namespace App\Factories;

use GuzzleHttp\Client;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;

class LineClientFactory
{
    protected static function config(): Configuration
    {
        return (new Configuration())->setAccessToken(config('services.line.channel_access_token'));
    }

    protected static function http(): Client
    {
        return new Client();
    }

    public static function messaging(): MessagingApiApi
    {
        return new MessagingApiApi(
            client: self::http(),
            config: self::config()
        );
    }

    public static function blob(): MessagingApiBlobApi
    {
        return new MessagingApiBlobApi(
            client: self::http(),
            config: self::config()
        );
    }
}
