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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class PaymentToPayResolver implements PaymentToPayResolverInterface
{
    public function __construct(
        private string $state,
    ) {
    }

    public function getLastPayment(OrderInterface $order): ?PaymentInterface {
        return $order->getLastPayment($this->state);
    }
}
