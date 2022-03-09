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

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CatalogPromotionScopeInterface extends ResourceInterface
{
    public function setType(?string $type): void;

    public function getType(): ?string;

    public function getConfiguration(): array;

    public function setConfiguration(array $configuration): void;

    public function getCatalogPromotion(): ?CatalogPromotionInterface;

    public function setCatalogPromotion(?CatalogPromotionInterface $catalogPromotion): void;
}
