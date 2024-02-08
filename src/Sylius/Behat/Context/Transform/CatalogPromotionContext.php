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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CatalogPromotionContext implements Context
{
    public function __construct(private RepositoryInterface $catalogPromotionRepository)
    {
    }

    /**
     * @Transform /^"([^"]+)" catalog promotion$/
     * @Transform :catalogPromotion
     */
    public function getCatalogPromotionByName(string $name): CatalogPromotionInterface
    {
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['name' => $name]);

        Assert::notNull(
            $catalogPromotion,
            sprintf('Catalog promotion with name "%s" does not exist', $name),
        );

        return $catalogPromotion;
    }
}
