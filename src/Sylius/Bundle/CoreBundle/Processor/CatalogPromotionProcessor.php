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
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionProcessor implements CatalogPromotionProcessorInterface
{
    private CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider;

    private CatalogPromotionApplicatorInterface $catalogPromotionApplicator;

    private FactoryInterface $stateMachine;

    public function __construct(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        FactoryInterface $stateMachine
    ) {
        $this->catalogPromotionVariantsProvider = $catalogPromotionVariantsProvider;
        $this->catalogPromotionApplicator = $catalogPromotionApplicator;
        $this->stateMachine = $stateMachine;
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        $stateMachine = $this->stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

        if (!$this->isCatalogPromotionEligible($catalogPromotion)) {
            $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE);

            return;
        }

        $variants = $this->catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion);
        if (empty($variants)) {
            $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE);

            return;
        }

        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $this->catalogPromotionApplicator->applyOnVariant($variant, $catalogPromotion);
        }

        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_ACTIVATE);
    }

    private function isCatalogPromotionEligible(CatalogPromotionInterface $catalogPromotion): bool
    {
        return (
            $catalogPromotion->isEnabled() &&
            $catalogPromotion->getState() !== CatalogPromotionStates::STATE_INACTIVE
        );
    }
}
