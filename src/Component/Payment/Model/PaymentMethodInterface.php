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

namespace Sylius\Component\Payment\Model;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;
use Sylius\Resource\Model\ToggleableInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;

interface PaymentMethodInterface extends
    CodeAwareInterface,
    ResourceInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getInstructions(): ?string;

    public function setInstructions(?string $instructions): void;

    public function getEnvironment(): ?string;

    public function setEnvironment(?string $environment): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    public function getGatewayConfig(): ?GatewayConfigInterface;

    public function setGatewayConfig(?GatewayConfigInterface $gatewayConfig): void;

    /**
     * @return PaymentMethodTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
