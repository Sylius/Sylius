<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TranslationInterface
{
    /**
     * @return TranslatableInterface
     */
    public function getTranslatable();

    /**
     * @param null|TranslatableInterface $translatable
     */
    public function setTranslatable(TranslatableInterface $translatable = null);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);
}
