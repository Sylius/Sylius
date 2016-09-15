<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CheckoutStateUrlGeneratorInterface extends UrlGeneratorInterface
{
    /**
     * @param OrderInterface $order
     * @param array $parameters
     * @param int $referenceType
     *
     * @return string
     */
    public function generateForOrderCheckoutState(OrderInterface $order, $parameters = [], $referenceType = self::ABSOLUTE_PATH);
}
