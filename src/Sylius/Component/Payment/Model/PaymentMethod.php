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

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class PaymentMethod implements PaymentMethodInterface
{
    use TimestampableTrait, ToggleableTrait;
    use TranslatableTrait {
        __construct as initializeTranslationsCollection;
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
    protected $environment;

    /**
     * @var int
     */
    protected $position;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
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
    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions(): ?string
    {
        return $this->getTranslation()->getInstructions();
    }

    /**
     * {@inheritdoc}
     */
    public function setInstructions(?string $instructions): void
    {
        $this->getTranslation()->setInstructions($instructions);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironment(?string $environment): void
    {
        $this->environment = $environment;
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

    /**
     * @param string|null $locale
     *
     * @return PaymentMethodTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var PaymentMethodTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): PaymentMethodTranslationInterface
    {
        return new PaymentMethodTranslation();
    }
}
