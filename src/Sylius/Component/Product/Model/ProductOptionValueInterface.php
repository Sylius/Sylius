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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductOptionValueInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    /**
     * @return ProductOptionInterface|null
     */
    public function getOption(): ?ProductOptionInterface;

    /**
     * @param ProductOptionInterface|null $option
     */
    public function setOption(?ProductOptionInterface $option): void;

    /**
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void;

    /**
     * @return string|null
     */
    public function getOptionCode(): ?string;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $locale
     *
     * @return ProductOptionValueTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
