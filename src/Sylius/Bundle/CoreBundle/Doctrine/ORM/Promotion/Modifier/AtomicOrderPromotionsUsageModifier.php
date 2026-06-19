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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Resource\Model\VersionedInterface;
use Webmozart\Assert\Assert;

final class AtomicOrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    public function __construct(
        private readonly ?Connection $connection,
        private readonly OrderPromotionsUsageModifierInterface $decoratedModifier,
        private readonly EntityManagerInterface $entityManager,
    ) {
        if (null !== $this->connection) {
            trigger_deprecation(
                'sylius/core-bundle',
                '2.3',
                'Passing a "%s" as the first constructor argument is deprecated and will be prohibited in Sylius 3.0.',
                Connection::class,
            );
        }
    }

    public function increment(OrderInterface $order): void
    {
        $this->lockEntities($order);

        $this->decoratedModifier->increment($order);
    }

    public function decrement(OrderInterface $order): void
    {
        $this->lockEntities($order);

        $this->decoratedModifier->decrement($order);
    }

    private function lockEntities(OrderInterface $order): void
    {
        foreach ($order->getPromotions() as $promotion) {
            $this->refreshAndLock($promotion);
        }

        /** @var PromotionCouponInterface|null $coupon */
        $coupon = $order->getPromotionCoupon();
        if (null !== $coupon) {
            $this->refreshAndLock($coupon);
        }
    }

    private function refreshAndLock(PromotionCouponInterface|PromotionInterface $entity): void
    {
        $this->entityManager->refresh($entity);

        Assert::isInstanceOf($entity, VersionedInterface::class);
        $this->entityManager->lock($entity, LockMode::OPTIMISTIC, $entity->getVersion());
    }
}
