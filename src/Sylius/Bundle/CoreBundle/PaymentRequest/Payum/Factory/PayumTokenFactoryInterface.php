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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Factory;

use Payum\Core\Security\TokenInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PayumTokenFactoryInterface
{
    public function createNew(PaymentRequestInterface $paymentRequest): TokenInterface;
}
