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

use Payum\Core\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\HttplugClient;

final class HttpClient implements HttpClientInterface
{
    public function send(RequestInterface $request)
    {
        $client = new HttplugClient();

        return $client->sendRequest($request);
    }
}
