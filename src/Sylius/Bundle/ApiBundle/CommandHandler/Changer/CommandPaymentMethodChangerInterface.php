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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Changer;

use Sylius\Bundle\ApiBundle\Command\AbstractPaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;

interface CommandPaymentMethodChangerInterface
{
    public function changePaymentMethod(
        AbstractPaymentMethod $abstractPaymentMethod,
        OrderInterface $order
    ): OrderInterface;
}
