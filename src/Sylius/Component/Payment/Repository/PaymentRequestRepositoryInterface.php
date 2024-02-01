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

namespace Sylius\Component\Payment\Repository;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestRepositoryInterface
{
    /**
     * @return PaymentRequestInterface[]
     */
    public function findOtherExisting(PaymentRequestInterface $paymentRequest): array;

    public function findOneByHash(string $hash): ?PaymentRequestInterface;
}
