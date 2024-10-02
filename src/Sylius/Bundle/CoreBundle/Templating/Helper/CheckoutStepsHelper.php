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

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

trigger_deprecation(
    'sylius/core-bundle',
    '1.14',
    'The "%s" class is deprecated, use "%s" and "%s" instead.',
    CheckoutStepsHelper::class,
    OrderPaymentMethodSelectionRequirementChecker::class,
    OrderShippingMethodSelectionRequirementChecker::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker} and {@see \Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementChecker} instead. */
class CheckoutStepsHelper extends Helper
{
    public function __construct(
        private OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        private OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ) {
    }

    public function isShippingRequired(OrderInterface $order): bool
    {
        return $this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order);
    }

    public function isPaymentRequired(OrderInterface $order): bool
    {
        return $this->orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order);
    }

    public function getName(): string
    {
        return 'sylius_checkout_steps';
    }
}
