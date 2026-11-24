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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionEndedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CatalogPromotionEndedListenerTest extends TestCase
{
    private MockObject&RepositoryInterface $catalogPromotionRepository;

    private EntityManagerInterface&MockObject $entityManager;

    private AllProductVariantsCatalogPromotionsProcessorInterface&MockObject $processor;

    private MessageBusInterface&MockObject $messageBus;

    private MockObject&TargetedCatalogPromotionProcessorInterface $targetedProcessor;

    private CatalogPromotionEndedListener $listener;

    protected function setUp(): void
    {
        $this->processor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $this->catalogPromotionRepository = $this->createMock(RepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->targetedProcessor = $this->createMock(TargetedCatalogPromotionProcessorInterface::class);

        $this->listener = new CatalogPromotionEndedListener(
            $this->processor,
            $this->catalogPromotionRepository,
            $this->entityManager,
            $this->messageBus,
            $this->targetedProcessor,
        );
    }

    public function testItProcessesCatalogPromotionThatHasJustEnded(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $catalogPromotion->method('getCode')->willReturn('WINTER_MUGS_SALE');

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'WINTER_MUGS_SALE'])
            ->willReturn($catalogPromotion)
        ;

        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $envelope = new Envelope($command, [new HandledStamp(null, 'handler')]);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope)
        ;

        $this->targetedProcessor
            ->expects($this->once())
            ->method('process')
            ->with($catalogPromotion)
        ;

        $this->processor
            ->expects($this->never())
            ->method('process')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        ($this->listener)(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }

    public function testItDoesNothingIfThereIsNoCatalogPromotionWithGivenCode(): void
    {
        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'WINTER_MUGS_SALE'])
            ->willReturn(null)
        ;

        $this->messageBus
            ->expects($this->never())
            ->method('dispatch')
        ;

        $this->targetedProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $this->processor
            ->expects($this->never())
            ->method('process')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        ($this->listener)(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }

    #[IgnoreDeprecations]
    public function testProcessesAllVariantsAndTriggersDeprecationIfNoTargetedProcessorIsGiven(): void
    {
        $this->expectUserDeprecationMessage(sprintf(
            'Since sylius/core-bundle 2.4: Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
            TargetedCatalogPromotionProcessorInterface::class,
            CatalogPromotionEndedListener::class,
        ));

        $listener = new CatalogPromotionEndedListener(
            $this->processor,
            $this->catalogPromotionRepository,
            $this->entityManager,
            $this->messageBus,
        );

        $catalogPromotion = $this->createStub(CatalogPromotionInterface::class);
        $catalogPromotion->method('getCode')->willReturn('WINTER_MUGS_SALE');

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'WINTER_MUGS_SALE'])
            ->willReturn($catalogPromotion)
        ;

        $this->messageBus
            ->method('dispatch')
            ->willReturn(new Envelope(new UpdateCatalogPromotionState('WINTER_MUGS_SALE')))
        ;

        $this->processor
            ->expects($this->once())
            ->method('process')
        ;

        $this->targetedProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $listener(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }
}
