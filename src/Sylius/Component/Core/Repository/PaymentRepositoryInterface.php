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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of PaymentInterface
 *
 * @extends RepositoryInterface<T>
 */
interface PaymentRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function findOneByOrderId(mixed $paymentId, mixed $orderId): ?PaymentInterface;

    public function findOneByOrderToken(mixed $paymentId, string $orderToken): ?PaymentInterface;

    public function findOneByCustomer(mixed $id, CustomerInterface $customer): ?PaymentInterface;
}
