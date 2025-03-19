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

namespace Sylius\Bundle\ApiBundle\Tests\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Attribute\PaymentRequestActionAware;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\PaymentRequestActionAwareContextBuilder;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class PaymentRequestActionAwareContextBuilderTest extends TestCase
{
    private MockObject|SerializerContextBuilderInterface $decoratedContextBuilderMock;

    private DefaultActionProviderInterface|MockObject $defaultActionProviderMock;

    private PaymentRequestActionAwareContextBuilder $paymentRequestActionAwareContextBuilder;

    protected function setUp(): void
    {
        $this->decoratedContextBuilderMock = $this->createMock(SerializerContextBuilderInterface::class);
        $this->defaultActionProviderMock = $this->createMock(DefaultActionProviderInterface::class);
        $this->paymentRequestActionAwareContextBuilder = new PaymentRequestActionAwareContextBuilder($this->decoratedContextBuilderMock, PaymentRequestActionAware::class, PaymentRequestActionAware::DEFAULT_ARGUMENT_NAME, $this->defaultActionProviderMock);
    }

    public function test_it_sets_action_as_a_constructor_argument(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))->method('toArray')->willReturn([
            'paymentMethodCode' => 'cash_on_delivery',
        ]);

        $this->decoratedContextBuilderMock
            ->expects($this->once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn([
                'input' => ['class' => AddPaymentRequest::class],
            ])
        ;

        $this->defaultActionProviderMock
            ->expects($this->once())
            ->method('getActionFromPaymentMethodCode')
            ->with('cash_on_delivery')
            ->willReturn(PaymentRequestInterface::ACTION_CAPTURE)
        ;

        $this->assertSame([
            'input' => ['class' => AddPaymentRequest::class],
            AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                AddPaymentRequest::class => ['action' => PaymentRequestInterface::ACTION_CAPTURE],
            ],
        ], $this->paymentRequestActionAwareContextBuilder->createFromRequest($requestMock, true, []));
    }

    public function test_it_sets_action_as_a_constructor_argument_if_null_action_is_given(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))->method('toArray')->willReturn([
            'paymentMethodCode' => 'cash_on_delivery',
            PaymentRequestActionAware::DEFAULT_ARGUMENT_NAME => null,
        ]);

        $this->decoratedContextBuilderMock
            ->expects($this->once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn([
                'input' => ['class' => AddPaymentRequest::class],
            ])
        ;

        $this->defaultActionProviderMock
            ->expects($this->once())
            ->method('getActionFromPaymentMethodCode')
            ->with('cash_on_delivery')
            ->willReturn(PaymentRequestInterface::ACTION_CAPTURE)
        ;

        $this->assertSame([
            'input' => ['class' => AddPaymentRequest::class],
            AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                AddPaymentRequest::class => ['action' => PaymentRequestInterface::ACTION_CAPTURE],
            ],
        ], $this->paymentRequestActionAwareContextBuilder->createFromRequest($requestMock, true, []));
    }

    public function test_it_does_nothing_if_there_is_no_input_class_or_no_existing_context_having_payment_method_code(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $this->decoratedContextBuilderMock->expects($this->once())->method('createFromRequest')->with($requestMock, true, [])
            ->willReturn([])
        ;
        $this->defaultActionProviderMock->expects($this->never())->method('getActionFromPaymentMethodCode');
        $this->assertSame([], $this->paymentRequestActionAwareContextBuilder->createFromRequest($requestMock, true, []));
    }

    public function test_it_does_nothing_if_input_class_is_not_payment_request_action_aware(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        $this->decoratedContextBuilderMock->expects($this->once())->method('createFromRequest')->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $this->defaultActionProviderMock->expects($this->never())->method('getActionFromPaymentMethodCode');
        $this->assertSame(['input' => ['class' => \stdClass::class]], $this->paymentRequestActionAwareContextBuilder
            ->createFromRequest($requestMock, true, []))
        ;
    }
}
