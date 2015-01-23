<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Translation\Model;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TranslationInterface
{
    /**
     * Get the translatable object
     *
     * @return TranslatableInterface
     */
    public function getTranslatable();

    /**
     * Set the translatable object
     *
     * @param TranslatableInterface $translatable
     * @return self
     */
    public function setTranslatable(TranslatableInterface $translatable = null);

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set the locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale);
}