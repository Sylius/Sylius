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

namespace spec\Sylius\Bundle\CoreBundle\Fixture\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture;
use Sylius\Bundle\CoreBundle\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\AfterFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\FixtureEvent;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class CatalogPromotionExecutorListenerSpec extends ObjectBehavior
{
    function let(
        AllProductVariantsCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion
    ): void {
        $this->beConstructedWith(
            $allCatalogPromotionsProcessor,
            $catalogPromotionStateProcessor,
            $catalogPromotionRepository,
            [$firstCriterion, $secondCriterion]
        );
    }

    function it_implements_listener_interface(): void
    {
        $this->shouldImplement(ListenerInterface::class);
    }

    function it_listens_for_after_fixture_events(): void
    {
        $this->shouldImplement(AfterFixtureListenerInterface::class);
    }

    function it_triggers_catalog_promotion_processing_after_catalog_promotion_fixture_execution(
        AllProductVariantsCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        SuiteInterface $suite,
        CatalogPromotionFixture $catalogPromotionFixture,
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion
    ): void {
        $catalogPromotionRepository
            ->findByCriteria([$firstCriterion, $secondCriterion])
            ->willReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;

        $this->afterFixture(new FixtureEvent($suite->getWrappedObject(), $catalogPromotionFixture->getWrappedObject(), []), []);

        $allCatalogPromotionsProcessor->process()->shouldBeCalled();
        $catalogPromotionStateProcessor->process($firstCatalogPromotion)->shouldBeCalled();
        $catalogPromotionStateProcessor->process($secondCatalogPromotion)->shouldBeCalled();
    }

    function it_does_not_trigger_catalog_promotion_processing_after_any_other_fixture_execution(
        AllProductVariantsCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        SuiteInterface $suite,
        FixtureInterface $fixture,
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion
    ): void {
        $catalogPromotionRepository->findByCriteria([$firstCriterion, $secondCriterion])->shouldNotBeCalled();

        $this->afterFixture(new FixtureEvent($suite->getWrappedObject(), $fixture->getWrappedObject(), []), []);

        $allCatalogPromotionsProcessor->process()->shouldNotBeCalled();
        $catalogPromotionStateProcessor->process()->shouldNotBeCalled();
    }
}
