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

namespace spec\Sylius\Bundle\CoreBundle\Fixture\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\AfterFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\FixtureEvent;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionExecutorListenerSpec extends ObjectBehavior
{
    function let(
        AllProductVariantsCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $messageBus,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
    ): void {
        $this->beConstructedWith(
            $allCatalogPromotionsProcessor,
            $catalogPromotionRepository,
            $messageBus,
            [$firstCriterion, $secondCriterion],
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
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $messageBus,
        SuiteInterface $suite,
        CatalogPromotionFixture $catalogPromotionFixture,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
    ): void {
        $catalogPromotionRepository
            ->findByCriteria([$firstCriterion, $secondCriterion])
            ->willReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;

        $firstCatalogPromotion->getCode()->willReturn('WINTER');
        $secondCatalogPromotion->getCode()->willReturn('AUTUMN');

        $allCatalogPromotionsProcessor->process()->shouldBeCalled();

        $firstCommand = new UpdateCatalogPromotionState('WINTER');
        $messageBus->dispatch($firstCommand)->willReturn(new Envelope($firstCommand))->shouldBeCalled();

        $secondCommand = new UpdateCatalogPromotionState('AUTUMN');
        $messageBus->dispatch($secondCommand)->willReturn(new Envelope($secondCommand))->shouldBeCalled();

        $this->afterFixture(new FixtureEvent($suite->getWrappedObject(), $catalogPromotionFixture->getWrappedObject(), []), []);
    }

    function it_does_not_trigger_catalog_promotion_processing_after_any_other_fixture_execution(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $messageBus,
        SuiteInterface $suite,
        FixtureInterface $fixture,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
    ): void {
        $catalogPromotionRepository->findByCriteria([$firstCriterion, $secondCriterion])->shouldNotBeCalled();

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->afterFixture(new FixtureEvent($suite->getWrappedObject(), $fixture->getWrappedObject(), []), []);
    }
}
