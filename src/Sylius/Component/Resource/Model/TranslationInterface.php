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

namespace Sylius\Component\Resource\Model;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TranslationInterface
{
    /**
     * @return TranslatableInterface
     */
    public function getTranslatable(): TranslatableInterface;

    /**
     * @param TranslatableInterface|null $translatable
     */
    public function setTranslatable(?TranslatableInterface $translatable): void;

    /**
     * @return string|null
     */
    public function getLocale(): ?string;

    /**
     * @param string|null $locale
     */
    public function setLocale(?string $locale): void;
}
