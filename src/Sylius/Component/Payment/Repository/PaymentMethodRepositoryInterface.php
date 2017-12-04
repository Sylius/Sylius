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

namespace Sylius\Component\Payment\Repository;

use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PaymentMethodRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return PaymentMethodInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
