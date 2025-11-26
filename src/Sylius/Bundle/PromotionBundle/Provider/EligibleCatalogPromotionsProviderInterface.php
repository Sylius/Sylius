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

namespace Sylius\Bundle\PromotionBundle\Provider;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

/**
 * @template T of CatalogPromotionInterface
 */
interface EligibleCatalogPromotionsProviderInterface
{
    /** @return T[] */
    public function provide(): array;
}
