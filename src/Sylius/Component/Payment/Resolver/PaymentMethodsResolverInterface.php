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

namespace Sylius\Component\Payment\Resolver;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface PaymentMethodsResolverInterface
{
    /**
     * @param PaymentInterface $subject
     *
     * @return PaymentMethodInterface[]
     */
    public function getSupportedMethods(PaymentInterface $subject): array;

    /**
     * @param PaymentInterface $subject
     *
     * @return bool
     */
    public function supports(PaymentInterface $subject): bool;
}
