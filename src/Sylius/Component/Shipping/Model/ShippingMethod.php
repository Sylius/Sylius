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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ArchivableTrait;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class ShippingMethod implements ShippingMethodInterface, \Stringable
{
    use ArchivableTrait, TimestampableTrait, ToggleableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var int|null */
    protected $position;

    /** @var ShippingCategoryInterface|null */
    protected $category;

    /** @var int */
    protected $categoryRequirement = ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY;

    /** @var string|null */
    protected $calculator;

    /** @var mixed[] */
    protected $configuration = [];

    /**
     * @var Collection|ShippingMethodRuleInterface[]
     *
     * @psalm-var Collection<array-key, ShippingMethodRuleInterface>
     */
    protected $rules;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->createdAt = new \DateTime();
        $this->rules = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getCategory(): ?ShippingCategoryInterface
    {
        return $this->category;
    }

    public function setCategory(?ShippingCategoryInterface $category): void
    {
        $this->category = $category;
    }

    public function getCategoryRequirement(): ?int
    {
        return $this->categoryRequirement;
    }

    public function setCategoryRequirement(?int $categoryRequirement): void
    {
        $this->categoryRequirement = $categoryRequirement;
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    public function getCalculator(): ?string
    {
        return $this->calculator;
    }

    public function setCalculator(?string $calculator): void
    {
        $this->calculator = $calculator;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function hasRules(): bool
    {
        return !$this->rules->isEmpty();
    }

    public function hasRule(ShippingMethodRuleInterface $rule): bool
    {
        return $this->rules->contains($rule);
    }

    public function addRule(ShippingMethodRuleInterface $rule): void
    {
        if (!$this->hasRule($rule)) {
            $rule->setShippingMethod($this);
            $this->rules->add($rule);
        }
    }

    public function removeRule(ShippingMethodRuleInterface $rule): void
    {
        $rule->setShippingMethod(null);
        $this->rules->removeElement($rule);
    }

    /**
     * @return ShippingMethodTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var ShippingMethodTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ShippingMethodTranslationInterface
    {
        return new ShippingMethodTranslation();
    }
}
