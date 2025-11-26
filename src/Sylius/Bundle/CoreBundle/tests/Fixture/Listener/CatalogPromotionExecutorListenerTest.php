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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture\Listener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture;
use Sylius\Bundle\CoreBundle\Fixture\Listener\CatalogPromotionExecutorListener;
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

final class CatalogPromotionExecutorListenerTest extends TestCase
{
    private AllProductVariantsCatalogPromotionsProcessorInterface&MockObject $allCatalogPromotionsProcessor;

    private CatalogPromotionRepositoryInterface&MockObject $catalogPromotionRepository;

    private MessageBusInterface&MockObject $messageBus;

    private CriteriaInterface&MockObject $firstCriterion;

    private CriteriaInterface&MockObject $secondCriterion;

    private CatalogPromotionExecutorListener $listener;

    protected function setUp(): void
    {
        $this->allCatalogPromotionsProcessor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $this->catalogPromotionRepository = $this->createMock(CatalogPromotionRepositoryInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->firstCriterion = $this->createMock(CriteriaInterface::class);
        $this->secondCriterion = $this->createMock(CriteriaInterface::class);
        $this->listener = new CatalogPromotionExecutorListener(
            $this->allCatalogPromotionsProcessor,
            $this->catalogPromotionRepository,
            $this->messageBus,
            [$this->firstCriterion, $this->secondCriterion],
        );
    }

    public function testImplementsListenerInterface(): void
    {
        $this->assertInstanceOf(ListenerInterface::class, $this->listener);
    }

    public function testListensForAfterFixtureEvents(): void
    {
        $this->assertInstanceOf(AfterFixtureListenerInterface::class, $this->listener);
    }

    public function testTriggersCatalogPromotionProcessingAfterCatalogPromotionFixtureExecution(): void
    {
        $suite = $this->createMock(SuiteInterface::class);
        $fixture = $this->createMock(CatalogPromotionFixture::class);
        $promotion1 = $this->createMock(CatalogPromotionInterface::class);
        $promotion2 = $this->createMock(CatalogPromotionInterface::class);

        $promotion1->method('getCode')->willReturn('WINTER');
        $promotion2->method('getCode')->willReturn('AUTUMN');

        $this->catalogPromotionRepository
            ->expects($this->once())
            ->method('findByCriteria')
            ->with([$this->firstCriterion, $this->secondCriterion])
            ->willReturn([$promotion1, $promotion2])
        ;

        $this->allCatalogPromotionsProcessor
            ->expects($this->once())
            ->method('process')
        ;

        $dispatchedCommands = [];
        $this->messageBus
            ->method('dispatch')
            ->willReturnCallback(function ($command) use (&$dispatchedCommands) {
                if ($command instanceof UpdateCatalogPromotionState) {
                    $dispatchedCommands[] = $command;
                }

                return new Envelope($command);
            })
        ;

        $event = new FixtureEvent($suite, $fixture, []);
        $this->listener->afterFixture($event, []);

        $this->assertCount(4, $dispatchedCommands);

        $dispatchedCodes = array_map(function (UpdateCatalogPromotionState $command) {
            $reflection = new \ReflectionObject($command);
            $property = $reflection->getProperty('code');
            $property->setAccessible(true);

            return $property->getValue($command);
        }, $dispatchedCommands)
        ;

        $this->assertEqualsCanonicalizing(['WINTER', 'WINTER', 'AUTUMN', 'AUTUMN'], $dispatchedCodes);
    }

    public function testDoesNotTriggerCatalogPromotionProcessingAfterAnyOtherFixtureExecution(): void
    {
        $suite = $this->createMock(SuiteInterface::class);
        $someOtherFixture = $this->createMock(FixtureInterface::class);

        $this->catalogPromotionRepository
            ->expects($this->never())
            ->method('findByCriteria')
        ;

        $this->messageBus
            ->expects($this->never())
            ->method('dispatch')
        ;

        $event = new FixtureEvent($suite, $someOtherFixture, []);
        $this->listener->afterFixture($event, []);
    }
}
