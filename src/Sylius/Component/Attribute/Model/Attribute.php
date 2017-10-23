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

namespace Sylius\Component\Attribute\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class Attribute implements AttributeInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $type = TextAttributeType::TYPE;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var string
     */
    protected $storageType;

    /**
     * @var Collection|AttributeSelectOptionInterface[]
     */
    protected $selectOptions;

    /**
     * @var int
     */
    protected $position;

    public function __construct()
    {
        $this->selectOptions = new ArrayCollection();
        $this->initializeTranslationsCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageType(): ?string
    {
        return $this->storageType;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorageType(?string $storageType): void
    {
        $this->storageType = $storageType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }


    public function hasSelectOptions(): bool
    {
        return !$this->selectOptions->isEmpty();
    }

    public function getSelectOptions(): Collection
    {
        if( $this->selectOptions === null )
            $this->selectOptions = new ArrayCollection();

        return $this->selectOptions;
    }

    public function addSelectOption(AttributeSelectOptionInterface $selectOption): void
    {
        if (!$this->hasSelectOption($selectOption)) {
            $selectOption->setAttribute($this);
            $this->selectOptions->add($selectOption);
        }
    }

    public function removeSelectOption(AttributeSelectOptionInterface $selectOption): void
    {
        if (!$this->hasSelectOption($selectOption)) {
            $selectOption->setAttribute(null);
            $this->selectOptions->removeElement($selectOption);
        }
    }

    public function hasSelectOption(AttributeSelectOptionInterface $selectOption): bool
    {
        return $this->selectOptions->contains($selectOption);
    }

    /**
     * @param string|null $locale
     *
     * @return AttributeTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var AttributeTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    /**
     * @return AttributeTranslationInterface
     */
    protected function createTranslation(): AttributeTranslationInterface
    {
        return new AttributeTranslation();
    }
}
