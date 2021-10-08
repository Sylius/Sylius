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

use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class EligibleCatalogPromotionsProvider implements EligibleCatalogPromotionsProviderInterface
{
    private RepositoryInterface $catalogPromotionRepository;

    public function __construct(RepositoryInterface $catalogPromotionRepository)
    {
        $this->catalogPromotionRepository = $catalogPromotionRepository;
    }

    public function provide(iterable $criteria = []): iterable
    {
        $catalogPromotions = $this->catalogPromotionRepository->findAll();

        /** @var CriteriaInterface $criterion */
        foreach ($criteria as $criterion) {
            $catalogPromotions = $criterion->meets($catalogPromotions);
        }

        return array_values($catalogPromotions);
    }
}
