<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\HttpClient;

use Payum\Core\HttpClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class HttpClient implements HttpClientInterface
{
    public function __construct(private ClientInterface $client)
    {
    }

    public function send(RequestInterface $request)
    {
        return $this->client->sendRequest($request);
    }
}
