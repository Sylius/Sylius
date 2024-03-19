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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ApiBundle\Exception\PromotionCouponCannotBeRemoved;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PromotionCouponDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
    ) {
    }

    /** @param array<array-key, mixed> $context */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof PromotionCouponInterface;
    }

    /** @param array<array-key, mixed> $context */
    public function persist($data, array $context = [])
    {
        return $this->decoratedDataPersister->persist($data, $context);
    }

    /** @param array<array-key, mixed> $context */
    public function remove($data, array $context = []): void
    {
        try {
            $this->decoratedDataPersister->remove($data, $context);
        } catch (ForeignKeyConstraintViolationException) {
            throw new PromotionCouponCannotBeRemoved();
        }
    }
}
