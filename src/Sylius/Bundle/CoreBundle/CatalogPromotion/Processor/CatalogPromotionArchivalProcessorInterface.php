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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

interface CatalogPromotionArchivalProcessorInterface
{
    public function canBeArchived(string $catalogPromotionCode): bool;

    public function archive(string $catalogPromotionCode): void;

    public function restore(string $catalogPromotionCode): void;
}
