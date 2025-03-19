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

namespace Sylius\Bundle\PaymentBundle\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Normalizer\SymfonyRequestNormalizerInterface;
use Sylius\Bundle\PaymentBundle\Processor\NotifyPayloadProcessor;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Request;

final class NotifyPayloadProcessorTest extends TestCase
{
    /**
     * @dataProvider getNormalizedRequestAndPayloadWithExpectation
     */
    public function test_process(array $normalizedRequest, mixed $payload, mixed $expectedPayload = null): void
    {
        $requestNormalizer = $this->createMock(SymfonyRequestNormalizerInterface::class);
        $notifyPayloadProcessor = new NotifyPayloadProcessor(
            $requestNormalizer,
        );

        $requestNormalizer->expects(self::once())
            ->method('normalize')
            ->willReturn($normalizedRequest);

        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $paymentRequest->expects(self::once())
            ->method('getPayload')
            ->willReturn($payload);

        if (null === $payload || is_array($payload)) {
            $paymentRequest->expects(self::once())
                ->method('setPayload')
                ->with($expectedPayload);
        } else {
            $paymentRequest->expects(self::never())
                ->method('setPayload');
        }

        $request = $this->createMock(Request::class);

        $notifyPayloadProcessor->process($paymentRequest, $request);
    }

    public static function getNormalizedRequestAndPayloadWithExpectation(): iterable
    {
        $normalizedRequest = [
            'http_request' => [
                'method' => 'POST',
            ],
        ];

        yield 'it processes array when payload is null' => [
            $normalizedRequest,
            null,
            [
                'http_request' => [
                    'method' => 'POST',
                ],
            ],
        ];

        yield 'it processes array when payload is an array' => [
            $normalizedRequest,
            [],
            [
                'http_request' => [
                    'method' => 'POST',
                ],
            ],
        ];

        yield 'it processes array when payload is an array with existing data' => [
            $normalizedRequest,
            [
                'existingData' => [],
            ],
            [
                'existingData' => [],
                'http_request' => [
                    'method' => 'POST',
                ],
            ],
        ];

        yield 'it processes array when payload is an array with existing data having previous request normalized data' => [
            $normalizedRequest,
            [
                'http_request' => [
                    'method' => 'GET',
                    'clientIp' => '127.0.0.1',
                ],
            ],
            [
                'http_request' => [
                    'method' => 'POST',
                ],
            ],
        ];

        yield 'it processes array when payload is not an array but payload should not change' => [
            $normalizedRequest,
            new \stdClass(),
        ];
    }
}
