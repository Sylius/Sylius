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

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

class CheckoutStepsHelper extends Helper
{
    /**
     * @var OrderPaymentMethodSelectionRequirementCheckerInterface
     */
    private $orderPaymentMethodSelectionRequirementChecker;

    /**
     * @var OrderShippingMethodSelectionRequirementCheckerInterface
     */
    private $orderShippingMethodSelectionRequirementChecker;

    /**
     * @param OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
     * @param OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
     */
    public function __construct(
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ) {
        $this->orderPaymentMethodSelectionRequirementChecker = $orderPaymentMethodSelectionRequirementChecker;
        $this->orderShippingMethodSelectionRequirementChecker = $orderShippingMethodSelectionRequirementChecker;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function isShippingRequired(OrderInterface $order): bool
    {
        return $this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function isPaymentRequired(OrderInterface $order): bool
    {
        return $this->orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_checkout_steps';
    }
}
