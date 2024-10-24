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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class DisableCatalogPromotionHandlerSpec extends ObjectBehavior
{
    public function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, $allProductVariantsCatalogPromotionsProcessor);
    }

    public function it_disables_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->disable()->shouldBeCalled();
        $allProductVariantsCatalogPromotionsProcessor->process()->shouldBeCalled();

        $this(new DisableCatalogPromotion('CATALOG_PROMOTION_CODE'));
    }

    public function it_returns_if_there_is_no_catalog_promotion_with_given_code(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn(null);

        $catalogPromotion->disable()->shouldNotBeCalled();
        $allProductVariantsCatalogPromotionsProcessor->process()->shouldNotBeCalled();

        $this(new DisableCatalogPromotion('CATALOG_PROMOTION_CODE'));
    }
}
