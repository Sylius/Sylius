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

namespace Sylius\Component\Core\Payment\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface OrderPaymentProviderInterface
{
    /**
     * @param OrderInterface $order
     * @param string $targetState
     *
     * @return PaymentInterface|null
     */
    public function provideOrderPayment(OrderInterface $order, string $targetState): ?PaymentInterface;
}
