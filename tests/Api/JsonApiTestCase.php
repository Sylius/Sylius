<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dataFixturesPath = __DIR__ . '/DataFixtures/ORM';
        $this->expectedResponsesPath = __DIR__ . '/Responses/Expected';
    }

    protected function get($id)
    {
        if (property_exists(static::class, 'container')) {
            return static::$container->get($id);
        }

        return parent::get($id);
    }

    protected function getAuthorizationHeaderAsCustomer(string $email, string $password): array
    {
        $this->client->request(
            'POST',
            '/api/v2/shop/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );
        $this->assertResponseStatusCodeSame(200);

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $this->assertIsString($token);

        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $this->assertIsString($authorizationHeader);

        return ['HTTP_' . $authorizationHeader => 'Bearer ' . $token];
    }
}
