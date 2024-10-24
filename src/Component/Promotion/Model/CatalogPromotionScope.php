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

class CatalogPromotionScope implements CatalogPromotionScopeInterface
{
    /** @var mixed */
    protected $id;

    protected ?string $type = null;

    protected array $configuration = [];

    protected ?CatalogPromotionInterface $catalogPromotion = null;

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getCatalogPromotion(): ?CatalogPromotionInterface
    {
        return $this->catalogPromotion;
    }

    public function setCatalogPromotion(?CatalogPromotionInterface $catalogPromotion): void
    {
        $this->catalogPromotion = $catalogPromotion;
    }
}
