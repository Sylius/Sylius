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

namespace Sylius\Bundle\PayumBundle\Provider;

use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentDescriptionProviderInterface
{
    /**
     * @param PaymentInterface $payment
     *
     * @return string
     */
    public function getPaymentDescription(PaymentInterface $payment): string;
}
