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

use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CatalogPromotion implements CatalogPromotionInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    protected ?string $name = null;

    protected ?string $code = null;

    /**
     * @var Collection|CatalogPromotionRuleInterface[]
     *
     * @psalm-var Collection<array-key, CatalogPromotionRuleInterface>
     */
    protected Collection $rules;

    protected Collection $actions;

    protected ?bool $enabled = true;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->rules = new ArrayCollection();
        $this->actions = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getLabel(): ?string
    {
        return $this->getTranslation()->getLabel();
    }

    public function setLabel(?string $label): void
    {
        $this->getTranslation()->setLabel($label);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /** @return CatalogPromotionTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var CatalogPromotionTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): CatalogPromotionTranslationInterface
    {
        return new CatalogPromotionTranslation();
    }

    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function hasRule(CatalogPromotionRuleInterface $rule): bool
    {
        return $this->rules->contains($rule);
    }

    public function addRule(CatalogPromotionRuleInterface $rule): void
    {
        if (!$this->hasRule($rule)) {
            $rule->setCatalogPromotion($this);
            $this->rules->add($rule);
        }
    }

    public function removeRule(CatalogPromotionRuleInterface $rule): void
    {
        $rule->setCatalogPromotion(null);
        $this->rules->removeElement($rule);
    }

    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function hasAction(CatalogPromotionActionInterface $action): bool
    {
        return $this->actions->contains($action);
    }

    public function addAction(CatalogPromotionActionInterface $action): void
    {
        if (!$this->hasAction($action)) {
            $action->setCatalogPromotion($this);
            $this->actions->add($action);
        }
    }

    public function removeAction(CatalogPromotionActionInterface $action): void
    {
        $action->setCatalogPromotion(null);
        $this->actions->removeElement($action);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function enable(): void
    {
        $this->setEnabled(true);
    }

    public function disable(): void
    {
        $this->setEnabled(false);
    }
}
