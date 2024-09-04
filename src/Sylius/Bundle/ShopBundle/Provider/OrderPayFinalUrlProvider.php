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

namespace Sylius\Bundle\ShopBundle\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Symfony\Component\Routing\RouterInterface;

final class OrderPayFinalUrlProvider implements OrderPayFinalUrlProviderInterface
{
    /**
     * @param array<string, string> $finalRouteParameters
     */
    public function __construct(
        private RouterInterface $router,
        private string $finalRoute,
        private array $finalRouteParameters,
    ) {
    }

    public function getUrl(?PaymentInterface $payment): string {
        $finalUrl = $this->router->generate($this->finalRoute, $this->finalRouteParameters);
        if (
            null === $payment ||
            $payment->getState() === BasePaymentInterface::STATE_COMPLETED ||
            $payment->getState() === BasePaymentInterface::STATE_AUTHORIZED
        ) {
            return $finalUrl;
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        return $this->router->generate(
            'sylius_shop_order_show',
            [
                'tokenValue' => $order->getTokenValue(),
            ]
        );
    }
}
