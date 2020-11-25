<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Provider;

use Sylius\Bundle\ApiBundle\Payment\ApiPaymentMethodInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ApiPaymentMethod implements ApiPaymentMethodInterface
{

    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supports(PaymentMethodInterface $paymentMethod): bool
    {
        return $paymentMethod->getGatewayConfig()->getFactoryName() === 'sylius.pay_pal';
    }

    public function provideConfiguration(PaymentInterface $payment): array
    {

        /** @var PaymentMethodInterface */
        $paymentMethod = $payment->getMethod();

        /** @var OrderInterface */
        $order = $payment->getOrder();

        return [
            'clientId' => $paymentMethod->getGatewayConfig()->getConfig()['client_id'],
            'completePayPalOrderFromPaymentPageUrl' => $this->router->generate('sylius_paypal_plugin_complete_paypal_order', ['token' => $order->getTokenValue()], UrlGeneratorInterface::ABSOLUTE_PATH),
            'createPayPalOrderFromPaymentPageUrl' => $this->router->generate('sylius_paypal_plugin_create_paypal_order', ['token' => $order->getTokenValue()]),
            'cancelPayPalPaymentUrl' => $this->router->generate('sylius_paypal_plugin_cancel_payment'),
            'partnerAttributionId' => $paymentMethod->getGatewayConfig()->getConfig()['partner_attribution_id'],
            'locale' => $order->getLocaleCode(),
            'orderId' => $order->getId(),
            'currency' => $order->getCurrencyCode(),
            'orderToken' => $order->getTokenValue(),
            'errorPayPalPaymentUrl' => $this->router->generate('sylius_paypal_plugin_payment_error'),
            'available_countries' => [],
        ];
    }
}
