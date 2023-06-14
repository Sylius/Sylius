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

use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of PaymentMethodInterface
 *
 * @extends RepositoryInterface<T>
 */
interface PaymentMethodRepositoryInterface extends RepositoryInterface
{
    /**
     * @return PaymentMethodInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
