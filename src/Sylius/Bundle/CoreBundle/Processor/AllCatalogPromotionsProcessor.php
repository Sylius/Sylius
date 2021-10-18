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

use SM\Factory\FactoryInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class AllCatalogPromotionsProcessor implements AllCatalogPromotionsProcessorInterface
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionProcessorInterface $catalogPromotionProcessor;

    private EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider;

    private FactoryInterface $stateMachine;

    private iterable $defaultCriteria;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        FactoryInterface $stateMachine,
        iterable $defaultCriteria = []
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionProcessor = $catalogPromotionProcessor;
        $this->catalogPromotionsProvider = $catalogPromotionsProvider;
        $this->stateMachine = $stateMachine;
        $this->defaultCriteria = $defaultCriteria;
    }

    public function process(): void
    {
        $this->catalogPromotionClearer->clear();
        $eligibleCatalogPromotions = $this->catalogPromotionsProvider->provide($this->defaultCriteria);

        foreach ($eligibleCatalogPromotions as $catalogPromotion) {
            $stateMachine = $this->stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

            if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_PROCESS)) {
                $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);
                $this->catalogPromotionProcessor->process($catalogPromotion);
            }
        }
    }
}
