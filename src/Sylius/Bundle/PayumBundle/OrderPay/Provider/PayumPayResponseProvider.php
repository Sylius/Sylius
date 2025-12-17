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

namespace Sylius\Bundle\PayumBundle\OrderPay\Provider;

use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\PayResponseProviderInterface;
use Sylius\Bundle\CoreBundle\OrderPay\Resolver\PaymentToPayResolverInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface as PayumGatewayConfigInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/** @experimental */
final class PayumPayResponseProvider implements PayResponseProviderInterface
{
    public function __construct(
        private Payum $payum,
        private PaymentToPayResolverInterface $paymentToPayResolver,
    ) {
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order,
    ): Response {
        $payment = $this->paymentToPayResolver->getPayment($order);
        Assert::notNull($payment, sprintf('Order (id %s) must have last payment in state "new".', $order->getId()));

        $redirectOptions = $requestConfiguration->getParameters()->get('redirect');
        if (is_string($redirectOptions)) {
            $redirectOptions = [
                'route' => $redirectOptions,
            ];
        }
        $token = $this->provideTokenBasedOnPayment($payment, $redirectOptions);

        return new RedirectResponse($token->getTargetUrl());
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order,
    ): bool {
        $payment = $this->paymentToPayResolver->getPayment($order);
        if (null === $payment) {
            return false;
        }

        $gatewayConfig = $this->getGatewayConfigFromPayment($payment);
        if (null === $gatewayConfig) {
            return false;
        }

        if (!$gatewayConfig instanceof PayumGatewayConfigInterface) {
            return false;
        }

        return $gatewayConfig->getUsePayum();
    }

    /**
     * @param array{route: ?string, parameters: ?string[]} $redirectOptions
     */
    private function provideTokenBasedOnPayment(PaymentInterface $payment, array $redirectOptions): TokenInterface
    {
        $gatewayConfig = $this->getGatewayConfigFromPayment($payment);
        Assert::notNull($gatewayConfig, 'An existing gateway config must exist.');

        $config = $gatewayConfig->getConfig();
        $tokenFactory = $this->payum->getTokenFactory();

        if (isset($config['use_authorize']) && true === (bool) $config['use_authorize']) {
            return $tokenFactory->createAuthorizeToken(
                $gatewayConfig->getGatewayName(),
                $payment,
                $redirectOptions['route'] ?? null,
                $redirectOptions['parameters'] ?? [],
            );
        }

        return $tokenFactory->createCaptureToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            $redirectOptions['route'] ?? null,
            $redirectOptions['parameters'] ?? [],
        );
    }

    private function getGatewayConfigFromPayment(PaymentInterface $payment): ?GatewayConfigInterface
    {
        return $payment->getMethod()?->getGatewayConfig();
    }
}
