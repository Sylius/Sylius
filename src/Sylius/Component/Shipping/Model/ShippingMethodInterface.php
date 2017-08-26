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

use Sylius\Component\Resource\Model\ArchivableInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ShippingMethodInterface extends
    ArchivableInterface,
    CodeAwareInterface,
    ShippingMethodTranslationInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public const CATEGORY_REQUIREMENT_MATCH_NONE = 0;
    public const CATEGORY_REQUIREMENT_MATCH_ANY = 1;
    public const CATEGORY_REQUIREMENT_MATCH_ALL = 2;

    /**
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void;

    /**
     * @return ShippingCategoryInterface|null
     */
    public function getCategory(): ?ShippingCategoryInterface;

    /**
     * @param ShippingCategoryInterface|null $category
     */
    public function setCategory(?ShippingCategoryInterface $category);

    /**
     * Get the one of matching requirements.
     * For example, a method can apply to shipment on 3 different conditions.
     *
     * 1) None of shippables matches the category.
     * 2) At least one of shippables matches the category.
     * 3) All shippables have to match the method category.
     *
     * @return int|null
     */
    public function getCategoryRequirement(): ?int;

    /**
     * @param int|null $categoryRequirement
     */
    public function setCategoryRequirement(?int $categoryRequirement): void;

    /**
     * @return string
     */
    public function getCategoryRequirementLabel(): string;

    /**
     * @return string
     */
    public function getCalculator(): ?string;

    /**
     * @param string $calculator
     */
    public function setCalculator(?string $calculator): void;

    /**
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void;
}
