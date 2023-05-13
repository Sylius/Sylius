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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

/**
 * @template T of PaymentMethodInterface
 * @extends BasePaymentMethodRepositoryInterface<T>
 */
interface PaymentMethodRepositoryInterface extends BasePaymentMethodRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder;

    public function findEnabledForChannel(ChannelInterface $channel): array;
}
