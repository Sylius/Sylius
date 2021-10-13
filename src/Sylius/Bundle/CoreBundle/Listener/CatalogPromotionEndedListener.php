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
use Sylius\Bundle\CoreBundle\Processor\AllCatalogPromotionsProcessorInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionEndedListener
{
    private AllCatalogPromotionsProcessorInterface $catalogPromotionsProcessor;

    private RepositoryInterface $catalogPromotionRepository;

    private EntityManagerInterface $entityManager;

    private StateMachineFactoryInterface $stateMachine;

    public function __construct(
        AllCatalogPromotionsProcessorInterface $catalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        StateMachineFactoryInterface $stateMachine
    ) {
        $this->catalogPromotionsProcessor = $catalogPromotionsProcessor;
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->entityManager = $entityManager;
        $this->stateMachine = $stateMachine;
    }

    public function __invoke(CatalogPromotionUpdated $event): void
    {
        if (null === $this->catalogPromotionRepository->findOneBy(['code' => $event->code])) {
            return;
        }
        
        $this->catalogPromotionsProcessor->process();

        $this->entityManager->flush();
    }
}
