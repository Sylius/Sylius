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

namespace Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\Processor\RequestProductVariantCatalogPromotionRecalculateInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionEndedListener
{
    private RequestProductVariantCatalogPromotionRecalculateInterface $catalogPromotionsProcessor;

    private RepositoryInterface $catalogPromotionRepository;

    private EntityManagerInterface $entityManager;

    private FactoryInterface $stateMachine;

    public function __construct(
        RequestProductVariantCatalogPromotionRecalculateInterface $catalogPromotionsProcessor,
        RepositoryInterface                                       $catalogPromotionRepository,
        EntityManagerInterface                                    $entityManager,
        FactoryInterface                                          $stateMachine
    ) {
        $this->catalogPromotionsProcessor = $catalogPromotionsProcessor;
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->entityManager = $entityManager;
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(CatalogPromotionEnded $event): void
    {
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);

        if (null === $catalogPromotion) {
            return;
        }

        $stateMachine = $this->stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);
        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE);

        $this->catalogPromotionsProcessor->recalculate();

        $this->entityManager->flush();
    }
}
