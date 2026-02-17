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

namespace Sylius\Bundle\PayumBundle\Client;

use Payum\Core\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class PaypalHeaderInjectingClient implements HttpClientInterface
{
    /** @var HttpClientInterface */
    private $innerClient;

    public function __construct(HttpClientInterface $innerClient)
    {
        $this->innerClient = $innerClient;
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        $host = $request->getUri()->getHost();
        if (false !== strpos($host, 'api-3t.paypal.com')) {
            $request = $request->withHeader('PayPal-Partner-Attribution-Id', 'SYLIUS_MP_PPCP');
        }

        return $this->innerClient->send($request);
    }
}
