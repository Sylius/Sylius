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

namespace Sylius\Tests\Api\Utils;

trait ShopUserLoginTrait
{
    protected function logInShopUser(string $email, string $password): array
    {
        $this->client->request(
            'POST',
            '/api/v2/shop/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
