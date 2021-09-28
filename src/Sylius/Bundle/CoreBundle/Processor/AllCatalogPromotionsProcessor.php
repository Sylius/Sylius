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

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AllCatalogPromotionsProcessor implements AllCatalogPromotionsProcessorInterface
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    private CatalogPromotionProcessorInterface $catalogPromotionProcessor;

    private RepositoryInterface $catalogPromotionRepository;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
        $this->catalogPromotionProcessor = $catalogPromotionProcessor;
        $this->catalogPromotionRepository = $catalogPromotionRepository;
    }

    public function process(): void
    {
        $this->catalogPromotionClearer->clear();

        foreach ($this->catalogPromotionRepository->findAll() as $catalogPromotion) {
            $this->catalogPromotionProcessor->process($catalogPromotion);
        }
    }
}
