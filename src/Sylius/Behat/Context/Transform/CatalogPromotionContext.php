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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CatalogPromotionContext implements Context
{
    private RepositoryInterface $catalogPromotionRepository;

    public function __construct(RepositoryInterface $catalogPromotionRepository)
    {
        $this->catalogPromotionRepository = $catalogPromotionRepository;
    }

    /**
     * @Transform /^"([^"]+)" catalog promotion$/
     * @Transform :catalogPromotion
     */
    public function getPromotionByName($promotionName): CatalogPromotionInterface
    {
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['name' => $promotionName]);

        Assert::notNull(
            $catalogPromotion,
            sprintf('Catalog Promotion with name "%s" does not exist', $promotionName)
        );

        return $catalogPromotion;
    }
}
