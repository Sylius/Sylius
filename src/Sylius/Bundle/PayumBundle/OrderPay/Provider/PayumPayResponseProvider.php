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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class PayumPayResponseProvider implements PayResponseProviderInterface
{
    /**
     * @param array<string, string> $afterPayUrlParameters
     */
    public function __construct(
        private Payum $payum,
        private PaymentToPayResolverInterface $paymentToPayResolver,
        private readonly ?string $afterPayUrlRoute = null,
        private readonly array $afterPayUrlParameters = [],
    ) {
    }

    public function getResponse(
        Request $request,
        OrderInterface $order,
    ): Response {
        $payment = $this->paymentToPayResolver->getPayment($order);
        Assert::notNull($payment, sprintf('Order (id %s) must have last payment in state "new".', $order->getId()));

        $token = $this->provideTokenBasedOnPayment($payment);

        return new RedirectResponse($token->getTargetUrl());
    }

    public function supports(
        Request $request,
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

    private function provideTokenBasedOnPayment(PaymentInterface $payment): TokenInterface
    {
        $gatewayConfig = $this->getGatewayConfigFromPayment($payment);
        Assert::notNull($gatewayConfig, 'An existing gateway config must exist.');

        $config = $gatewayConfig->getConfig();
        $tokenFactory = $this->payum->getTokenFactory();

        if (isset($config['use_authorize']) && true === (bool) $config['use_authorize']) {
            return $tokenFactory->createAuthorizeToken(
                $gatewayConfig->getGatewayName(),
                $payment,
                $this->afterPayUrlRoute,
                $this->afterPayUrlParameters,
            );
        }

        return $tokenFactory->createCaptureToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            $this->afterPayUrlRoute,
            $this->afterPayUrlParameters,
        );
    }

    private function getGatewayConfigFromPayment(PaymentInterface $payment): ?GatewayConfigInterface
    {
        return $payment->getMethod()?->getGatewayConfig();
    }
}
