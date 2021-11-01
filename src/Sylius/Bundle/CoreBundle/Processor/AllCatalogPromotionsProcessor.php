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

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class AllCatalogPromotionsProcessor implements AllCatalogPromotionsProcessorInterface
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionProcessorInterface $catalogPromotionProcessor;

    private EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider;

    private iterable $defaultCriteria;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        iterable $defaultCriteria = []
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionProcessor = $catalogPromotionProcessor;
        $this->catalogPromotionsProvider = $catalogPromotionsProvider;
        $this->defaultCriteria = $defaultCriteria;
    }

    public function process(): void
    {
        $this->catalogPromotionClearer->clear();
        $eligibleCatalogPromotions = $this->catalogPromotionsProvider->provide($this->defaultCriteria);

        $sortedCatalogPromotion = $this->sortWithPrioritiesAndExclusiveness($eligibleCatalogPromotions);

        foreach ($sortedCatalogPromotion as $catalogPromotion) {
            $this->catalogPromotionProcessor->process($catalogPromotion);
        }
    }

    private function sortWithPrioritiesAndExclusiveness(array $catalogPromotions): array
    {
        usort(
            $catalogPromotions,
            function(CatalogPromotionInterface $a, CatalogPromotionInterface $b): int {
                if (($a->isExclusive() && $b->isExclusive()) && ($a->getPriority() > $b->getPriority())) {
                    return -1;
                }

                if (!$a->isExclusive() && !$b->isExclusive() && ($a->getPriority() > $b->getPriority())) {
                    return -1;
                }

                if ($a->isExclusive() && !$b->isExclusive()) {
                    return -1;
                }

                return 1;

            });

        return $catalogPromotions;
    }
}
