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

namespace Sylius\Bundle\PromotionBundle\Provider;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class EligibleCatalogPromotionProvider implements EligibleCatalogPromotionProviderInterface
{
    private CatalogPromotionRepositoryInterface $catalogPromotionRepository;

    private iterable $defaultCriteria;

    public function __construct(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        iterable $defaultCriteria =  []
    ) {
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->defaultCriteria = $defaultCriteria;
    }

    public function provide(CatalogPromotionInterface $catalogPromotion): ?CatalogPromotionInterface
    {
        return $this->catalogPromotionRepository->findOneByIdAndCriteria(
            $catalogPromotion->getId(),
            $this->defaultCriteria
        );
    }
}
