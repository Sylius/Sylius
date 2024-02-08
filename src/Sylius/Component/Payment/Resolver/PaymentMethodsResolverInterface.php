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

namespace Sylius\Component\Payment\Resolver;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

interface PaymentMethodsResolverInterface
{
    /**
     * @return PaymentMethodInterface[]
     */
    public function getSupportedMethods(PaymentInterface $subject): array;

    public function supports(PaymentInterface $subject): bool;
}
