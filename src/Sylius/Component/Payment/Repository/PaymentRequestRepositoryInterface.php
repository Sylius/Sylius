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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of PaymentRequestInterface
 *
 * @extends RepositoryInterface<T>
 */
interface PaymentRequestRepositoryInterface extends RepositoryInterface
{
    public function duplicateExists(PaymentRequestInterface $paymentRequest): bool;
}
