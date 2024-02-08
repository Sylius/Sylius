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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEndedListenerSpec extends ObjectBehavior
{
    function let(
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
    ): void {
        $this->beConstructedWith(
            $allProductVariantsCatalogPromotionsProcessor,
            $catalogPromotionRepository,
            $entityManager,
            $messageBus,
        );
    }

    function it_processes_catalog_promotion_that_has_just_ended(
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($catalogPromotion);

        $catalogPromotion->getCode()->willReturn('WINTER_MUGS_SALE');
        $updateCatalogPromotionStateCommand = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');

        $messageBus
            ->dispatch($updateCatalogPromotionStateCommand)
            ->willReturn(new Envelope($updateCatalogPromotionStateCommand))
            ->shouldBeCalled()
        ;

        $allProductVariantsCatalogPromotionsProcessor->process()->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }

    function it_does_nothing_if_there_is_no_catalog_promotion_with_given_code(
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn(null);
        $catalogPromotionRepository->findAll()->shouldNotBeCalled();

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();
        $allProductVariantsCatalogPromotionsProcessor->process()->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }
}
