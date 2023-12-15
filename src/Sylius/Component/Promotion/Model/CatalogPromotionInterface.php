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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface CatalogPromotionInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface, ToggleableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getStartDate(): ?\DateTimeInterface;

    public function setStartDate(?\DateTimeInterface $startDate): void;

    public function getEndDate(): ?\DateTimeInterface;

    public function setEndDate(?\DateTimeInterface $endDate): void;

    public function getPriority(): int;

    public function setPriority(int $priority): void;

    public function isExclusive(): bool;

    public function setExclusive(bool $exclusive): void;

    public function getState(): ?string;

    public function setState(?string $state): void;

    public function getLabel(): ?string;

    public function setLabel(?string $label): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    /** @return CatalogPromotionTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;

    /**
     * @return Collection<array-key, CatalogPromotionScopeInterface>
     */
    public function getScopes(): Collection;

    public function hasScope(CatalogPromotionScopeInterface $scope): bool;

    public function addScope(CatalogPromotionScopeInterface $scope): void;

    public function removeScope(CatalogPromotionScopeInterface $scope): void;

    /**
     * @return Collection<array-key, CatalogPromotionActionInterface>
     */
    public function getActions(): Collection;

    public function hasAction(CatalogPromotionActionInterface $action): bool;

    public function addAction(CatalogPromotionActionInterface $action): void;

    public function removeAction(CatalogPromotionActionInterface $action): void;
}
