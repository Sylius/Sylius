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

namespace Sylius\Bundle\ApiBundle\Payment\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Sylius\Bundle\ApiBundle\Payment\Payum\PayumApiContextInterface;
use Webmozart\Assert\Assert;

final class SyliusApiGetHttpRequestAction implements ActionInterface
{
    public function __construct(
        private PayumApiContextInterface $payumApiContext,
    ) {
    }

    public function execute($request): void
    {
        /** @var $request GetHttpRequest */
        RequestNotSupportedException::assertSupports($this, $request);

        $this->updateRequest($request);
    }

    private function updateRequest(GetHttpRequest $request): void
    {
        $paymentRequest = $this->payumApiContext->getPaymentRequest();
        Assert::notNull($paymentRequest);

        /** @var array $details */
        $details = $paymentRequest->getRequestPayload();
        $httpRequest = $details['http_request'] ?? [];

        $request->query = $httpRequest['query'];
        $request->request = $httpRequest['request'];
        // Not existing property
        $request->headers = $httpRequest['headers'];
        $request->method = $httpRequest['method'] ?? 'POST';
        $request->uri = $httpRequest['uri'] ?? '';
        $request->clientIp = $httpRequest['client_ip'] ?? '';
        $request->userAgent = $httpRequest['user_agent'];
        $request->content = $httpRequest['content'];
    }

    public function supports($request): bool
    {
        return $this->payumApiContext->isEnabled() && $request instanceof GetHttpRequest;
    }
}
