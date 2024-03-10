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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Sylius\Bundle\PaymentBundle\Context\PaymentRequestContextInterface;
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

        /** @var array{
         *     'http_request'?: array{
         *         'query'?: array<string, string>,
         *         'request'?: array<string, string>,
         *         'method'?: string,
         *         'uri'?: string,
         *         'client_ip'?: string,
         *         'user_agent'?: string,
         *         'content'?: string,
         *         'headers'?: array<string, string>,
         *     },
         * } $payload
         */
        $payload = $paymentRequest->getPayload();
        $httpRequest = $payload['http_request'] ?? [];

        $request->query = $httpRequest['query'] ?? [];
        $request->request = $httpRequest['request'] ?? [];
        $request->method = $httpRequest['method'] ?? 'POST';
        $request->uri = $httpRequest['uri'] ?? '';
        $request->clientIp = $httpRequest['client_ip'] ?? '';
        $request->userAgent = $httpRequest['user_agent'] ?? '';
        $request->content = $httpRequest['content'] ?? '';

        // Next release of Payum will have this field
        if (property_exists($request, 'headers')) {
            $request->headers = $httpRequest['headers'] ?? [];
        }
    }

    public function supports($request): bool
    {
        return $this->payumApiContext->isEnabled() && $request instanceof GetHttpRequest;
    }
}
