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

namespace Sylius\Component\Promotion\Provider;

use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

final class ActivePromotionsProvider implements PreQualifiedPromotionsProviderInterface
{
    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {
    }

    public function getPromotions(PromotionSubjectInterface $subject): array
    {
        return $this->promotionRepository->findActive();
    }
}
