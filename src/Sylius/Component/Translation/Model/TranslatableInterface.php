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
interface TranslatableInterface
{
    /**
     * Get all translations
     *
     * @return TranslationInterface[]
     */
    public function getTranslations();

    /**
     * Add a new translation
     *
     * @param TranslationInterface $translation
     * @return self
     */
    public function addTranslation(TranslationInterface $translation);

    /**
     * Remove a translation
     *
     * @param TranslationInterface $translation
     * @return $this
     */
    public function removeTranslation(TranslationInterface $translation);
}
