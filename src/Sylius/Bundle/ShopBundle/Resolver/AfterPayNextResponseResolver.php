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

namespace Sylius\Bundle\ShopBundle\Resolver;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class AfterPayNextResponseResolver implements AfterPayNextResponseResolverInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {}

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): Response {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        $order = $payment->getOrder();
        Assert::notNull($order, 'An order is required at this point.');

        $route = 'sylius_shop_order_show';
        $params = ['tokenValue' => $order->getTokenValue()];

        if (
            $payment->getState() === BasePaymentInterface::STATE_COMPLETED ||
            $payment->getState() === BasePaymentInterface::STATE_AUTHORIZED
        ) {
            $route = 'sylius_shop_order_thank_you';
        }

        $url = $this->router->generate($route, $params);

        return new RedirectResponse($url);
    }
}
