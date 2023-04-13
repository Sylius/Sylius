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

namespace Sylius\Bundle\PayumBundle\HttpClient;

use Http\Client\HttpClient as BaseHttpClientInterface;
use Payum\Core\HttpClientInterface;
use Psr\Http\Message\RequestInterface;

final class HttpClient implements HttpClientInterface
{
    public function __construct(private BaseHttpClientInterface $client)
    {
    }

    public function send(RequestInterface $request)
    {
        return $this->client->sendRequest($request);
    }
}
