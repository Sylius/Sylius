<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CheckoutStepsHelper extends Helper
{
    /**
     * @var OrderShippingMethodSelectionRequirementCheckerInterface
     */
    private $orderShippingMethodSelectionRequirementChecker;

    /**
     * @param OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
     */
    public function __construct(
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ) {
        $this->orderShippingMethodSelectionRequirementChecker = $orderShippingMethodSelectionRequirementChecker;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function isShippingRequired(OrderInterface $order)
    {
        return $this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function isPaymentRequired(OrderInterface $order)
    {
        return 0 < $order->getTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_steps';
    }
}
