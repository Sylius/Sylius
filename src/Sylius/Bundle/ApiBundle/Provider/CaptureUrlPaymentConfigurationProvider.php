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

namespace Sylius\Bundle\ApiBundle\Provider;

use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class CaptureUrlPaymentConfigurationProvider implements CompositePaymentConfigurationProviderInterface
{
    public function __construct(
        private CompositePaymentConfigurationProviderInterface $decoratedCompositePaymentConfigurationProvider,
        private Payum $payum
    ) {
    }

    public function provide(PaymentInterface $payment): array
    {
        $configuration = $this->decoratedCompositePaymentConfigurationProvider->provide($payment);
        if (isset($configuration['capture_url'])) {
            return $configuration;
        }

        $token = $this->provideTokenBasedOnPayment($payment);
        $configuration['capture_url'] = $token->getTargetUrl();

        return $configuration;
    }

    private function provideTokenBasedOnPayment(PaymentInterface $payment): TokenInterface
    {
        $gatewayConfig = $this->getGatewayConfig($payment);
        $config = $gatewayConfig->getConfig();
        $gatewayName = $gatewayConfig->getGatewayName();
        $tokenFactory = $this->payum->getTokenFactory();

        $afterToken = $tokenFactory->createToken(
            $gatewayName,
            $payment,
            $config['sylius_api_after_path'] ?? 'api_payums_shop_after_pay_item',
            $config['sylius_api_after_path_parameters'] ?? [],
        );

        return $tokenFactory->createToken(
            $gatewayName,
            $payment,
            'api_payums_shop_capture_item',
            [],
            $afterToken->getTargetUrl()
        );
    }

    private function getGatewayConfig(PaymentInterface $payment): GatewayConfigInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod);

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        return $gatewayConfig;
    }
}
