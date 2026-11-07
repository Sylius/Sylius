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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesInScopeProviderInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesWithAppliedPromotionProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class TargetedCatalogPromotionProcessor implements TargetedCatalogPromotionProcessorInterface
{
    /** @param ServiceLocator<VariantCodesInScopeProviderInterface> $variantCodesProviders */
    public function __construct(
        private readonly ServiceLocator $variantCodesProviders,
        private readonly VariantCodesWithAppliedPromotionProviderInterface $appliedPromotionVariantCodesProvider,
        private readonly ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
        private readonly AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
    ) {
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        $scopesWithProviders = [];

        /** @var CatalogPromotionScopeInterface $scope */
        foreach ($catalogPromotion->getScopes() as $scope) {
            if (!$this->variantCodesProviders->has($scope->getType())) {
                $this->allProductVariantsCatalogPromotionsProcessor->process();

                return;
            }

            $scopesWithProviders[] = [$this->variantCodesProviders->get($scope->getType()), $scope];
        }

        $variantCodes = $this->appliedPromotionVariantCodesProvider->provide($catalogPromotion);

        /** @var VariantCodesInScopeProviderInterface $provider */
        foreach ($scopesWithProviders as [$provider, $scope]) {
            $variantCodes = array_merge($variantCodes, $provider->provide($scope));
        }

        $variantCodes = array_values(array_unique($variantCodes));

        if ($variantCodes === []) {
            return;
        }

        $this->commandDispatcher->updateVariants($variantCodes);
    }
}
