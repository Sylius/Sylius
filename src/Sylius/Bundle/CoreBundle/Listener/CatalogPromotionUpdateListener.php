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
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionUpdateListener
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionProcessorInterface $catalogPromotionProcessor;

    private RepositoryInterface $catalogPromotionRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionProcessor = $catalogPromotionProcessor;
        $this->catalogPromotionRepository = $catalogPromotionRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(CatalogPromotionUpdated $event): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);
        if ($catalogPromotion === null) {
            return;
        }

        $this->catalogPromotionClearer->clear();

        foreach ($this->catalogPromotionRepository->findAll() as $catalogPromotion) {
            $this->catalogPromotionProcessor->process($catalogPromotion);
        }

        $this->entityManager->flush();
    }
}
