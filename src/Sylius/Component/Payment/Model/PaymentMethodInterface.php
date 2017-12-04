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

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface PaymentMethodInterface extends
    CodeAwareInterface,
    ResourceInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName(): ?string;

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void;

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string;

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void;

    /**
     * {@inheritdoc}
     */
    public function getInstructions(): ?string;

    /**
     * {@inheritdoc}
     */
    public function setInstructions(?string $instructions): void;

    /**
     * @return string|null
     */
    public function getEnvironment(): ?string;

    /**
     * @param string|null $environment
     */
    public function setEnvironment(?string $environment): void;

    /**
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void;

    /**
     * @param string|null $locale
     *
     * @return PaymentMethodTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
