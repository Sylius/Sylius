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

use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

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
