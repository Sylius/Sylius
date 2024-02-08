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

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CatalogPromotionActionInterface extends ResourceInterface
{
    public function setType(?string $type): void;

    public function setConfiguration(array $configuration): void;

    public function setCatalogPromotion(?CatalogPromotionInterface $catalogPromotion): void;

    public function getType(): ?string;

    public function getConfiguration(): array;

    public function getCatalogPromotion(): ?CatalogPromotionInterface;
}
