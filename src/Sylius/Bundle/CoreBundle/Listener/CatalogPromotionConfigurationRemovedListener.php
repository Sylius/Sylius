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
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionReprocessorInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionConfigurationRemoved;

final class CatalogPromotionConfigurationRemovedListener
{
    private CatalogPromotionReprocessorInterface $catalogPromotionReprocessor;

    private EntityManagerInterface $entityManager;

    public function __construct(
        CatalogPromotionReprocessorInterface $catalogPromotionReprocessor,
        EntityManagerInterface $entityManager
    ) {
        $this->catalogPromotionReprocessor = $catalogPromotionReprocessor;
        $this->entityManager = $entityManager;
    }

    public function __invoke(CatalogPromotionConfigurationRemoved $event): void
    {
        $this->catalogPromotionReprocessor->reprocess();

        $this->entityManager->flush();
    }
}
