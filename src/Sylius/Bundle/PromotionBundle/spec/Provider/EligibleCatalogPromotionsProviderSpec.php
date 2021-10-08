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

namespace spec\Sylius\Bundle\PromotionBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class EligibleCatalogPromotionsProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $catalogPromotionRepository): void
    {
        $this->beConstructedWith($catalogPromotionRepository);
    }

    function it_implements_eligible_catalog_promotions_provider_interface(): void
    {
        $this->shouldImplement(EligibleCatalogPromotionsProviderInterface::class);
    }

    function it_provides_all_enabled_catalog_promotions_from_repository(
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion
    ): void {
        $catalogPromotionRepository
            ->findBy(['enabled' => true])
            ->willReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;

        $this->provide()->shouldReturn([$firstCatalogPromotion, $secondCatalogPromotion]);
    }
}
