<?php

namespace Sylius\Tests\Controller;

class JsonApiTestCase extends \Lakion\ApiTestCase\JsonApiTestCase
{
    const HTTP_AUTHORIZATION = 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ';

    const AUTHORIZATION_HEADER_WITH_CONTENT_TYPE = [
        'HTTP_Authorization' => self::HTTP_AUTHORIZATION,
        'CONTENT_TYPE' => 'application/json',
    ];


    const AUTHORIZATION_HEADER_WITH_ACCEPT = [
        'HTTP_Authorization' => self::HTTP_AUTHORIZATION,
        'ACCEPT' => 'application/json',
    ];
}