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

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface CheckoutStateUrlGeneratorInterface extends UrlGeneratorInterface
{
    /**
     * @param array $parameters
     * @param int $referenceType
     */
    public function generateForOrderCheckoutState(
        OrderInterface $order,
        array $parameters = [],
        int $referenceType = self::ABSOLUTE_PATH
    ): string;

    /**
     * @param array $parameters
     */
    public function generateForCart(array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string;
}
