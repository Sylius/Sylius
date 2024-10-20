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

namespace Sylius\Component\Core\Payment\Refresher;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Webmozart\Assert\Assert;

interface OrderPaymentRefresherInterface
{
    public function isPaymentRefreshingNeeded(OrderInterface $order): bool;

    public function refreshPayments(OrderInterface $order, string $targetState): void;
}
