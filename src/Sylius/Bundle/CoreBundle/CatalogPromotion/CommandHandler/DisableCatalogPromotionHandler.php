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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class DisableCatalogPromotionHandler
{
    public function __construct(
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        private AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        private ?TargetedCatalogPromotionProcessorInterface $targetedCatalogPromotionProcessor = null,
    ) {
        if (null === $this->targetedCatalogPromotionProcessor) {
            trigger_deprecation(
                'sylius/core-bundle',
                '2.4',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                TargetedCatalogPromotionProcessorInterface::class,
                self::class,
            );
        }
    }

    public function __invoke(DisableCatalogPromotion $command): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $command->code]);

        if (null === $catalogPromotion) {
            return;
        }

        $catalogPromotion->disable();

        if (null !== $this->targetedCatalogPromotionProcessor) {
            $this->targetedCatalogPromotionProcessor->process($catalogPromotion);

            return;
        }

        $this->allProductVariantsCatalogPromotionsProcessor->process();
    }
}
