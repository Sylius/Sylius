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
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionCreatedListener
{
    private AllCatalogPromotionsProcessorInterface $allCatalogPromotionProcessor;

    private RepositoryInterface $catalogPromotionRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        AllCatalogPromotionsProcessorInterface $allCatalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
    ) {
        $this->allCatalogPromotionProcessor = $allCatalogPromotionProcessor;
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(CatalogPromotionCreated $event): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);
        if (null === $catalogPromotion) {
            return;
        }

        $this->allCatalogPromotionProcessor->process();

        $this->entityManager->flush();
    }
}
