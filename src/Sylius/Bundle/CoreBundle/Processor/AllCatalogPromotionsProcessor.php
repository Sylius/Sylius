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

final class AllCatalogPromotionsProcessor implements AllCatalogPromotionsProcessorInterface
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionProcessorInterface $catalogPromotionProcessor;

    private EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionProcessor = $catalogPromotionProcessor;
        $this->catalogPromotionsProvider = $catalogPromotionsProvider;
    }

    public function process(): void
    {
        $this->catalogPromotionClearer->clear();

        foreach ($this->catalogPromotionsProvider->provide() as $catalogPromotion) {
            $this->catalogPromotionProcessor->process($catalogPromotion);
        }
    }
}
