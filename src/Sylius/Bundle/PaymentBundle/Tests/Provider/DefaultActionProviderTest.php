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

namespace Sylius\Bundle\PaymentBundle\Tests\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProvider;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Component\Payment\Model\GatewayConfig;
use Sylius\Component\Payment\Model\Payment;
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

final class DefaultActionProviderTest extends TestCase
{
    private MockObject|PaymentMethodRepositoryInterface $paymentMethodRepository;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
    }

    /**
     * @dataProvider getConfigAndExpectation
     */
    public function test_it_provides_action_with_a_payment_request(array $config, string $expectedAction): void
    {
        $gatewayConfig = new GatewayConfig();
        $gatewayConfig->setConfig($config);

        $method = new PaymentMethod();
        $method->setGatewayConfig($gatewayConfig);

        $paymentRequest = new PaymentRequest(
            new Payment(),
            $method,
        );

        $provider = $this->createProvider();
        $action = $provider->getAction($paymentRequest);

        $this->assertSame($expectedAction, $action);
    }

    /**
     * @dataProvider getConfigAndExpectation
     */
    public function test_it_provides_action_with_a_payment_method(array $config, string $expectedAction): void
    {
        $gatewayConfig = new GatewayConfig();
        $gatewayConfig->setConfig($config);

        $method = new PaymentMethod();
        $method->setGatewayConfig($gatewayConfig);

        $provider = $this->createProvider();
        $action = $provider->getActionFromPaymentMethod($method);

        $this->assertSame($expectedAction, $action);
    }

    /**
     * @dataProvider getConfigAndExpectation
     */
    public function test_it_provides_action_with_a_payment_method_code(array $config, string $expectedAction): void
    {
        $gatewayConfig = new GatewayConfig();
        $gatewayConfig->setConfig($config);

        $method = new PaymentMethod();
        $method->setGatewayConfig($gatewayConfig);

        $this->paymentMethodRepository->expects($this->once())->method('findOneBy')->with(['code' => 'cash_on_delivery'])->willReturn($method);

        $provider = $this->createProvider();
        $action = $provider->getActionFromPaymentMethodCode('cash_on_delivery');

        $this->assertSame($expectedAction, $action);
    }

    public function test_it_provides_capture_action_if_payment_method_is_not_found(): void
    {
        $this->paymentMethodRepository->expects($this->once())->method('findOneBy')->with(['code' => 'cash_on_delivery'])->willReturn(null);

        $provider = $this->createProvider();
        $action = $provider->getActionFromPaymentMethodCode('cash_on_delivery');

        $this->assertSame(PaymentRequestInterface::ACTION_CAPTURE, $action);
    }

    private function createProvider(): DefaultActionProviderInterface
    {
        return new DefaultActionProvider($this->paymentMethodRepository, PaymentRequestInterface::ACTION_CAPTURE);
    }

    public static function getConfigAndExpectation(): iterable
    {
        yield [
            [], PaymentRequestInterface::ACTION_CAPTURE,
        ];
        yield [
            ['use_authorize' => true], PaymentRequestInterface::ACTION_AUTHORIZE,
        ];
    }
}
