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

namespace spec\Sylius\Bundle\CoreBundle\Processor;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Announcer\BatchedVariantsUpdateAnnouncerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class AllCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        ProductVariantRepositoryInterface $productVariantRepository,
        BatchedVariantsUpdateAnnouncerInterface $announcer
    ): void {
        $this->beConstructedWith($catalogPromotionClearer, $productVariantRepository, $announcer);
    }

    function it_clears_and_processes_catalog_promotions(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        ProductVariantRepositoryInterface $productVariantRepository,
        BatchedVariantsUpdateAnnouncerInterface $announcer
    ): void {
        $catalogPromotionClearer->clear()->shouldBeCalled();

        $productVariantRepository->getCodesOfAllVariants()->willReturn(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE']);

        $announcer->dispatchVariantsUpdateCommand(['FIRST_VARIANT_CODE', 'SECOND_VARIANT_CODE'])->shouldBeCalled();

        $this->process();
    }
}
