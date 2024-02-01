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

namespace Sylius\Bundle\PayumBundle\Action\PaymentRequest;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Sylius\Bundle\PayumBundle\PaymentRequest\PaymentRequestContextInterface;
use Webmozart\Assert\Assert;

final class SyliusGetHttpRequestAction implements ActionInterface
{
    public function __construct(
        private PaymentRequestContextInterface $payumApiContext,
    ) {
    }

    public function execute($request): void
    {
        /** @var GetHttpRequest $request */
        RequestNotSupportedException::assertSupports($this, $request);

        $this->updateRequest($request);
    }

    private function updateRequest(GetHttpRequest $request): void
    {
        $paymentRequest = $this->payumApiContext->getPaymentRequest();
        Assert::notNull($paymentRequest);

        /** @var array $payload */
        $payload = $paymentRequest->getRequestPayload();
        $httpRequest = $payload['http_request'] ?? [];

        $request->query = $httpRequest['query'] ?? [];
        $request->request = $httpRequest['request'] ?? [];
        $request->method = $httpRequest['method'] ?? 'POST';
        $request->uri = $httpRequest['uri'] ?? '';
        $request->clientIp = $httpRequest['client_ip'] ?? '';
        $request->userAgent = $httpRequest['user_agent'] ?? '';
        $request->content = $httpRequest['content'] ?? '';

        // Not existing property but used by the Symfony bridge
        $request->headers = $httpRequest['headers'] ?? [];
    }

    public function supports($request): bool
    {
        return $this->payumApiContext->isEnabled() && $request instanceof GetHttpRequest;
    }
}
