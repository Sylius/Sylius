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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

final class ActivePromotionsByChannelProvider implements PreQualifiedPromotionsProviderInterface
{
    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {
    }

    public function getPromotions(PromotionSubjectInterface $subject): array
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $channel = $subject->getChannel();
        if (null === $channel) {
            throw new \InvalidArgumentException('Order has no channel, but it should.');
        }

        if (null === $subject->getPromotionCoupon()) {
            return $this->promotionRepository->findActiveNonCouponBasedByChannel($channel);
        }

        return $this->promotionRepository->findActiveByChannel($channel);
    }
}
