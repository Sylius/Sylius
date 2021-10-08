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

namespace Sylius\Bundle\PromotionBundle\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;

final class EligibleCatalogPromotionsProvider implements EligibleCatalogPromotionsProviderInterface
{
    private RepositoryInterface $catalogPromotionRepository;

    public function __construct(RepositoryInterface $catalogPromotionRepository)
    {
        $this->catalogPromotionRepository = $catalogPromotionRepository;
    }

    public function provide(): array
    {
        return $this->catalogPromotionRepository->findBy(['enabled' => true]);
    }
}
