<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Payment\Resolver;

use Sylius\Payment\Model\PaymentInterface;
use Sylius\Payment\Model\PaymentMethodInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface MethodsResolverInterface
{
    /**
     * @param PaymentInterface $subject
     *
     * @return PaymentMethodInterface[]
     */
    public function getSupportedMethods(PaymentInterface $subject);

    /**
     * @param PaymentInterface $subject
     *
     * @return bool
     */
    public function supports(PaymentInterface $subject);
}
