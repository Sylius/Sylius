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

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductOptionValueInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    public function getOption(): ?ProductOptionInterface;

    public function setOption(?ProductOptionInterface $option): void;

    public function getValue(): ?string;

    public function setValue(?string $value): void;

    public function getOptionCode(): ?string;

    public function getName(): ?string;

    /**
     * @return ProductOptionValueTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
