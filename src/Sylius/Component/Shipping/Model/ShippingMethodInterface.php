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

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ArchivableInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ShippingMethodInterface extends
    ResourceInterface,
    ArchivableInterface,
    CodeAwareInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public const CATEGORY_REQUIREMENT_MATCH_NONE = 0;

    public const CATEGORY_REQUIREMENT_MATCH_ANY = 1;

    public const CATEGORY_REQUIREMENT_MATCH_ALL = 2;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    public function getCategory(): ?ShippingCategoryInterface;

    public function setCategory(?ShippingCategoryInterface $category);

    /**
     * Get the one of matching requirements.
     * For example, a method can apply to shipment on 3 different conditions.
     *
     * 1) None of shippables matches the category.
     * 2) At least one of shippables matches the category.
     * 3) All shippables have to match the method category.
     */
    public function getCategoryRequirement(): ?int;

    public function setCategoryRequirement(?int $categoryRequirement): void;

    /**
     * @return string
     */
    public function getCalculator(): ?string;

    /**
     * @param string $calculator
     */
    public function setCalculator(?string $calculator): void;

    public function getConfiguration(): array;

    public function setConfiguration(array $configuration): void;

    /**
     * @return Collection|ShippingMethodRuleInterface[]
     *
     * @psalm-return Collection<array-key, ShippingMethodRuleInterface>
     */
    public function getRules(): Collection;

    public function hasRules(): bool;

    public function hasRule(ShippingMethodRuleInterface $rule): bool;

    public function addRule(ShippingMethodRuleInterface $rule): void;

    public function removeRule(ShippingMethodRuleInterface $rule): void;

    /**
     * @return ShippingMethodTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
