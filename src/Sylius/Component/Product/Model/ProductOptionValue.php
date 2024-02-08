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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class ProductOptionValue implements ProductOptionValueInterface, \Stringable
{
    use TranslatableTrait {
        __construct as private initializeTranslationCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var ProductOptionInterface|null */
    protected $option;

    public function __construct()
    {
        $this->initializeTranslationCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
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

    public function getOption(): ?ProductOptionInterface
    {
        return $this->option;
    }

    public function setOption(?ProductOptionInterface $option): void
    {
        $this->option = $option;
    }

    public function getValue(): ?string
    {
        return $this->getTranslation()->getValue();
    }

    public function setValue(?string $value): void
    {
        $this->getTranslation()->setValue($value);
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getOptionCode(): ?string
    {
        if (null === $this->option) {
            throw new \BadMethodCallException(
                'The option have not been created yet so you cannot access proxy methods.',
            );
        }

        return $this->option->getCode();
    }

    /**
     * @throws \BadMethodCallException
     */
    public function getName(): ?string
    {
        if (null === $this->option) {
            throw new \BadMethodCallException(
                'The option have not been created yet so you cannot access proxy methods.',
            );
        }

        return $this->option->getName();
    }

    /**
     * @return ProductOptionValueTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var ProductOptionValueTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ProductOptionValueTranslationInterface
    {
        return new ProductOptionValueTranslation();
    }
}
