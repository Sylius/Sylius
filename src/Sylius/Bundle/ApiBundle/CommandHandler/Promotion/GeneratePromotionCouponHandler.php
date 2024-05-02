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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Promotion;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\Command\Promotion\GeneratePromotionCoupon;
use Sylius\Bundle\ApiBundle\Exception\PromotionNotFoundException;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GeneratePromotionCouponHandler implements MessageHandlerInterface
{
    /**
     * @param PromotionRepositoryInterface<PromotionInterface> $promotionRepository
     */
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
        private PromotionCouponGeneratorInterface $promotionCouponGenerator,
    ) {
    }

    /** @return Collection<array-key, PromotionCouponInterface> */
    public function __invoke(GeneratePromotionCoupon $generatePromotionCoupon): Collection
    {
        /** @var PromotionInterface|null $promotion */
        $promotion = $this->promotionRepository->findOneBy(['code' => $generatePromotionCoupon->getPromotionCode()]);
        if ($promotion === null) {
            throw new PromotionNotFoundException($generatePromotionCoupon->getPromotionCode());
        }

        $promotionCoupons = $this->promotionCouponGenerator->generate(
            $promotion,
            $generatePromotionCoupon,
        );

        return new ArrayCollection($promotionCoupons);
    }
}
